/**
 * Site navigation.
 *
 * Two disclosure widgets: the mobile menu button and the per-item submenu
 * buttons. Both keep aria-expanded in sync, close on Escape, and return focus
 * to the control that opened them. Nothing traps focus — Tab always moves on.
 */
(function () {
	"use strict";

	var DESKTOP = window.matchMedia("(min-width: 64rem)");

	/* --------------------------------------------------------------------
	   Mobile menu
	   -------------------------------------------------------------------- */

	var toggle = document.querySelector(".nav-toggle");
	var nav = document.getElementById("primary-navigation");

	function setMenu(open) {
		if (!toggle || !nav) {
			return;
		}
		toggle.setAttribute("aria-expanded", String(open));
		nav.classList.toggle("is-open", open);
	}

	function closeMenu(returnFocus) {
		setMenu(false);
		if (returnFocus && toggle) {
			toggle.focus();
		}
	}

	if (toggle && nav) {
		toggle.addEventListener("click", function () {
			setMenu(toggle.getAttribute("aria-expanded") !== "true");
		});

		// Clicking a link inside the open mobile menu navigates away, so the
		// menu is closed to keep state correct if the page does not reload.
		nav.addEventListener("click", function (event) {
			if (event.target.closest("a") && !DESKTOP.matches) {
				setMenu(false);
			}
		});
	}

	/* --------------------------------------------------------------------
	   Submenus
	   -------------------------------------------------------------------- */

	var submenuToggles = Array.prototype.slice.call(
		document.querySelectorAll(".submenu-toggle")
	);

	function submenuFor(button) {
		return document.getElementById(
			button.getAttribute("aria-controls") || ""
		);
	}

	// The button's accessible name stays fixed ("Show submenu for X"); the
	// open or closed state is carried by aria-expanded, which screen readers
	// announce on their own. Rewriting the label as well would say it twice.
	function setSubmenu(button, open) {
		var panel = submenuFor(button);
		if (!panel) {
			return;
		}
		button.setAttribute("aria-expanded", String(open));
		panel.hidden = !open;
	}

	function closeAllSubmenus(except) {
		submenuToggles.forEach(function (button) {
			if (button !== except) {
				setSubmenu(button, false);
			}
		});
	}

	submenuToggles.forEach(function (button) {
		// Submenus start closed. The hidden attribute is already in the markup
		// so the menu is usable before this script runs.
		setSubmenu(button, false);

		button.addEventListener("click", function () {
			var open = button.getAttribute("aria-expanded") === "true";

			// On desktop only one submenu is open at a time; stacked mobile
			// menus can stay open together so the user does not lose context.
			if (!open && DESKTOP.matches) {
				closeAllSubmenus(button);
			}

			setSubmenu(button, !open);
		});
	});

	/* --------------------------------------------------------------------
	   Dismissal
	   -------------------------------------------------------------------- */

	document.addEventListener("keydown", function (event) {
		if (event.key !== "Escape" && event.key !== "Esc") {
			return;
		}

		var openSubmenu = document.querySelector(
			'.submenu-toggle[aria-expanded="true"]'
		);

		// Escape closes the innermost thing first.
		if (openSubmenu) {
			setSubmenu(openSubmenu, false);
			openSubmenu.focus();
			return;
		}

		if (toggle && toggle.getAttribute("aria-expanded") === "true") {
			closeMenu(true);
		}
	});

	// On desktop, a submenu closes once focus or the pointer leaves its item.
	document.addEventListener("focusin", function (event) {
		if (!DESKTOP.matches) {
			return;
		}

		submenuToggles.forEach(function (button) {
			if (button.getAttribute("aria-expanded") !== "true") {
				return;
			}
			var item = button.closest(".menu-item-has-children");
			if (item && !item.contains(event.target)) {
				setSubmenu(button, false);
			}
		});
	});

	document.addEventListener("click", function (event) {
		if (!DESKTOP.matches || !event.target.closest) {
			return;
		}
		if (!event.target.closest(".menu-item-has-children")) {
			closeAllSubmenus();
		}
	});

	// Reset state when crossing the breakpoint so a menu left open on mobile
	// does not linger in a desktop layout.
	function handleBreakpoint() {
		closeAllSubmenus();
		if (DESKTOP.matches) {
			setMenu(false);
		}
	}

	if (typeof DESKTOP.addEventListener === "function") {
		DESKTOP.addEventListener("change", handleBreakpoint);
	} else if (typeof DESKTOP.addListener === "function") {
		DESKTOP.addListener(handleBreakpoint);
	}
})();
