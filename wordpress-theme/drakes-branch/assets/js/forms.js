/**
 * Civic forms.
 *
 * Progressive enhancement only. Without this script the form posts normally
 * and the server renders the result, so nothing here is load-bearing.
 *
 * Error handling follows the GOV.UK pattern: a summary at the top of the form
 * listing every problem as a link to the field, plus a message beside each
 * field. The summary receives focus on a failed submit, so screen reader and
 * keyboard users hear what went wrong instead of being left at the button.
 */
(function () {
	"use strict";

	if (typeof window.dbForms === "undefined") {
		return;
	}

	var config = window.dbForms;
	var strings = config.strings || {};

	function t(key, fallback) {
		return strings[key] || fallback;
	}

	/* --------------------------------------------------------------------
	   Field helpers
	   -------------------------------------------------------------------- */

	// Radio groups have no single element, so the fieldset stands in for them.
	function containerOf(control) {
		return control.closest(".field") || control.closest(".fieldset");
	}

	function errorNodeOf(control) {
		var container = containerOf(control);
		return container ? container.querySelector(".field__error") : null;
	}

	function labelTextOf(control) {
		var container = containerOf(control);
		if (!container) {
			return "";
		}
		var label =
			container.querySelector(".field__label") ||
			container.querySelector(".fieldset__legend");
		if (!label) {
			return "";
		}
		// Drop the required marker and the "(optional)" hint from the name.
		return label.textContent.replace(/[*]|\(optional\)/g, "").trim();
	}

	function setError(control, message) {
		var node = errorNodeOf(control);

		if (node) {
			node.textContent = message || "";
		}

		if (control.type === "radio") {
			var group = control.form.querySelectorAll(
				'[name="' + control.name + '"]'
			);
			Array.prototype.forEach.call(group, function (radio) {
				radio.setAttribute("aria-invalid", message ? "true" : "false");
			});
			return;
		}

		if (message) {
			control.setAttribute("aria-invalid", "true");
		} else {
			control.removeAttribute("aria-invalid");
		}
	}

	/* --------------------------------------------------------------------
	   Validation — mirrors the server rules so the visitor is not told one
	   thing here and another after submitting.
	   -------------------------------------------------------------------- */

	var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

	function validate(control) {
		var required =
			control.hasAttribute("required") ||
			control.getAttribute("aria-required") === "true";
		var value = (control.value || "").trim();
		var label = labelTextOf(control);

		if (control.type === "radio") {
			var checked = control.form.querySelector(
				'[name="' + control.name + '"]:checked'
			);
			if (required && !checked) {
				return label
					? "Choose an option for " + label.toLowerCase() + "."
					: t("required", "This field is required.");
			}
			return "";
		}

		if (control.type === "file") {
			var file = control.files && control.files[0];

			if (required && !file) {
				return label + " is required.";
			}
			if (!file) {
				return "";
			}

			var max = parseInt(control.getAttribute("data-max-bytes"), 10);
			if (max && file.size > max) {
				return t("fileTooLarge", "That file is larger than %s.").replace(
					"%s",
					Math.round(max / 1048576) + " MB"
				);
			}

			var accept = (control.getAttribute("data-accept") || "")
				.split(",")
				.map(function (item) {
					return item.trim().toLowerCase();
				})
				.filter(Boolean);
			var ext = file.name.split(".").pop().toLowerCase();

			if (accept.length && accept.indexOf(ext) === -1) {
				return t(
					"fileWrongType",
					"That file type is not accepted. Allowed types: %s."
				).replace("%s", accept.join(", "));
			}

			return "";
		}

		if (required && !value) {
			return label ? label + " is required." : t("required", "This field is required.");
		}

		if (!value) {
			return "";
		}

		if (control.type === "email" && !EMAIL.test(value)) {
			return t("invalidEmail", "Enter a valid email address.");
		}

		return "";
	}

	function controlsOf(form) {
		return Array.prototype.filter.call(
			form.querySelectorAll(".field__control, .choice input"),
			function (control) {
				return control.name !== "website";
			}
		);
	}

	/* --------------------------------------------------------------------
	   Error summary
	   -------------------------------------------------------------------- */

	function renderSummary(form, problems) {
		var box = document.getElementById(form.id + "-message");
		if (!box) {
			return;
		}

		box.className = "form-message form-message--error error-summary";
		box.innerHTML = "";

		var heading = document.createElement("p");
		heading.textContent = t("summaryTitle", "There is a problem with this form");
		box.appendChild(heading);

		var list = document.createElement("ul");

		problems.forEach(function (problem) {
			var item = document.createElement("li");
			var link = document.createElement("a");
			link.href = "#" + problem.id;
			link.textContent = problem.message;
			link.addEventListener("click", function (event) {
				event.preventDefault();
				var target = document.getElementById(problem.id);
				if (target) {
					target.focus();
				}
			});
			item.appendChild(link);
			list.appendChild(item);
		});

		box.appendChild(list);
		box.focus();
	}

	function showMessage(form, text, isError) {
		var box = document.getElementById(form.id + "-message");
		if (!box) {
			return;
		}

		box.className =
			"form-message " + (isError ? "form-message--error" : "form-message--success");
		box.innerHTML = "";

		var paragraph = document.createElement("p");
		paragraph.textContent = text;
		box.appendChild(paragraph);
		box.focus();
	}

	function clearMessage(form) {
		var box = document.getElementById(form.id + "-message");
		if (box) {
			box.className = "form-message";
			box.innerHTML = "";
		}
	}

	/* --------------------------------------------------------------------
	   Wiring
	   -------------------------------------------------------------------- */

	var forms = document.querySelectorAll("[data-town-form]");

	Array.prototype.forEach.call(forms, function (form) {
		var submitButton = form.querySelector('button[type="submit"]');

		// Validate on blur only after the visitor has left a field, never
		// while they are still typing into it.
		controlsOf(form).forEach(function (control) {
			control.addEventListener("blur", function () {
				setError(control, validate(control));
			});

			// Once a field is marked invalid, clear it as soon as it is fixed.
			control.addEventListener("input", function () {
				if (control.getAttribute("aria-invalid") === "true") {
					setError(control, validate(control));
				}
			});

			if (control.type === "radio" || control.tagName === "SELECT") {
				control.addEventListener("change", function () {
					setError(control, validate(control));
				});
			}
		});

		form.addEventListener("submit", function (event) {
			event.preventDefault();

			var problems = [];
			var seenRadioGroups = {};

			controlsOf(form).forEach(function (control) {
				// A radio group produces at most one summary entry.
				if (control.type === "radio") {
					if (seenRadioGroups[control.name]) {
						return;
					}
					seenRadioGroups[control.name] = true;
				}

				var message = validate(control);
				setError(control, message);

				if (message) {
					problems.push({ id: control.id, message: message });
				}
			});

			if (problems.length) {
				renderSummary(form, problems);
				return;
			}

			clearMessage(form);

			if (submitButton) {
				submitButton.disabled = true;
				submitButton.classList.add("is-busy");
				submitButton.setAttribute("aria-label", t("sending", "Sending, please wait."));
			}

			function finish() {
				if (submitButton) {
					submitButton.disabled = false;
					submitButton.classList.remove("is-busy");
					submitButton.removeAttribute("aria-label");
				}
			}

			fetch(config.endpoint, {
				method: "POST",
				body: new FormData(form),
				credentials: "same-origin"
			})
				.then(function (response) {
					return response
						.json()
						.catch(function () {
							return {};
						})
						.then(function (data) {
							return { status: response.status, data: data };
						});
				})
				.then(function (result) {
					finish();

					if (result.status === 200) {
						// Replace the fields with the confirmation so the form
						// cannot be sent twice by accident.
						showMessage(form, result.data.message, false);
						controlsOf(form).forEach(function (control) {
							var container = containerOf(control);
							if (container) {
								container.hidden = true;
							}
						});
						var actions = form.querySelector(".form__actions");
						if (actions) {
							actions.hidden = true;
						}
						return;
					}

					if (result.status === 422 && result.data.errors) {
						var problems = [];

						Object.keys(result.data.errors).forEach(function (name) {
							var control = form.querySelector('[name="' + name + '"]');
							if (!control) {
								return;
							}
							setError(control, result.data.errors[name]);
							problems.push({
								id: control.id,
								message: result.data.errors[name]
							});
						});

						renderSummary(form, problems);
						return;
					}

					showMessage(
						form,
						result.data.message || t("genericError", "Something went wrong."),
						true
					);
				})
				.catch(function () {
					finish();
					showMessage(form, t("networkError", "We could not reach the server."), true);
				});
		});
	});
})();
