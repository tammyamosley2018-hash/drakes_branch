/**
 * Town of Drakes Branch — standalone form submission.
 *
 * Validates in the browser for a fast correction loop, then posts to the
 * endpoint configured in config.js. The server validates again; nothing here
 * is trusted.
 *
 * Errors are announced, focus is moved to the first bad field, and the submit
 * button is locked while the request is in flight so nothing is sent twice.
 */
(function () {
	'use strict';

	var form = document.querySelector('[data-town-form]');
	if (!form) {
		return;
	}

	var summary = form.querySelector('[data-error-summary]');
	var result = form.querySelector('[data-result]');
	var button = form.querySelector('[data-submit]');
	var buttonLabel = button ? button.textContent : '';

	var config = window.DRAKES_BRANCH_FORM || {};
	var endpoint = config.endpoint || '';

	/** Human-readable label for a field, taken from its visible <label>. */
	function labelFor(field) {
		var group = field.closest('.field, .fieldset');
		if (!group) {
			return field.name;
		}
		var label = group.querySelector('.field__label, .fieldset__legend');
		if (!label) {
			return field.name;
		}
		// Strip the "*" and the "(required)" / "(optional)" marker, so the error
		// summary reads "Your name — This field is required."
		return label.textContent.replace(/\s*\*?\s*\(?(required|optional)\)?\s*$/i, '').trim();
	}

	function groupOf(field) {
		return field.closest('.field, .fieldset');
	}

	function showFieldError(field, message) {
		var group = groupOf(field);
		if (!group) {
			return;
		}
		group.classList.add('field--invalid');
		var slot = group.querySelector('[data-error]');
		if (slot) {
			slot.textContent = message;
		}
		field.setAttribute('aria-invalid', 'true');
	}

	function clearErrors() {
		form.querySelectorAll('.field--invalid').forEach(function (group) {
			group.classList.remove('field--invalid');
			var slot = group.querySelector('[data-error]');
			if (slot) {
				slot.textContent = '';
			}
		});
		form.querySelectorAll('[aria-invalid="true"]').forEach(function (field) {
			field.removeAttribute('aria-invalid');
		});
		if (summary) {
			summary.innerHTML = '';
			summary.hidden = true;
		}
	}

	/** The controls a person actually fills in, in document order. */
	function controls() {
		return Array.prototype.slice.call(
			form.querySelectorAll('input:not([type="hidden"]), select, textarea')
		).filter(function (field) {
			return !field.classList.contains('hp-field');
		});
	}

	function validate() {
		var problems = [];
		var seenGroups = [];

		controls().forEach(function (field) {
			var group = groupOf(field);

			// A radio group is one question: report it once, not once per option.
			if (field.type === 'radio') {
				if (seenGroups.indexOf(field.name) !== -1) {
					return;
				}
				seenGroups.push(field.name);
				var checked = form.querySelector('input[name="' + field.name + '"]:checked');
				if (field.required && !checked) {
					problems.push({ field: field, message: 'Choose one of these options.' });
				}
				return;
			}

			var value = (field.value || '').trim();

			if (field.required && !value) {
				problems.push({ field: field, message: 'This field is required.' });
				return;
			}

			if (value && field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
				problems.push({ field: field, message: 'Enter an email address, like name@example.com.' });
				return;
			}

			if (field.type === 'file' && field.files && field.files.length) {
				var max = (config.maxUploadBytes || 8 * 1024 * 1024);
				if (field.files[0].size > max) {
					problems.push({
						field: field,
						message: 'That file is larger than ' + Math.round(max / 1048576) + ' MB. Send it by email instead.'
					});
				}
			}

			if (group && group.dataset.maxlength) {
				var limit = parseInt(group.dataset.maxlength, 10);
				if (value.length > limit) {
					problems.push({ field: field, message: 'Please shorten this to ' + limit + ' characters or fewer.' });
				}
			}
		});

		return problems;
	}

	function reportProblems(problems) {
		problems.forEach(function (problem) {
			showFieldError(problem.field, problem.message);
		});

		if (!summary) {
			return;
		}

		var heading = problems.length === 1
			? 'There is one problem with this form'
			: 'There are ' + problems.length + ' problems with this form';

		var list = problems.map(function (problem) {
			var id = problem.field.id;
			return '<li><a href="#' + id + '">' + labelFor(problem.field) + ' — ' + problem.message + '</a></li>';
		}).join('');

		summary.className = 'notice notice--error';
		summary.innerHTML = '<h2>' + heading + '</h2><ul>' + list + '</ul>';
		summary.hidden = false;
		summary.setAttribute('tabindex', '-1');
		summary.focus();
	}

	function setBusy(busy) {
		if (!button) {
			return;
		}
		if (busy) {
			button.setAttribute('aria-disabled', 'true');
			button.textContent = 'Sending…';
		} else {
			button.removeAttribute('aria-disabled');
			button.textContent = buttonLabel;
		}
	}

	function showResult(kind, heading, body) {
		if (!result) {
			return;
		}
		result.className = 'notice notice--' + kind;
		result.innerHTML = '<h2>' + heading + '</h2>' + body;
		result.hidden = false;
		result.setAttribute('tabindex', '-1');
		result.focus();
	}

	form.addEventListener('submit', function (event) {
		event.preventDefault();

		if (button && button.getAttribute('aria-disabled') === 'true') {
			return;
		}

		clearErrors();

		var problems = validate();
		if (problems.length) {
			reportProblems(problems);
			return;
		}

		if (!endpoint) {
			showResult(
				'error',
				'This form is not connected yet',
				'<p>Please call the town office on ' +
					'<a href="tel:+14345683091">434-568-3091</a> or email ' +
					'<a href="mailto:drakesbr@hovac.com">drakesbr@hovac.com</a>.</p>'
			);
			return;
		}

		setBusy(true);

		var data = new FormData(form);

		fetch(endpoint, {
			method: 'POST',
			body: data,
			headers: { Accept: 'application/json' }
		})
			.then(function (response) {
				return response.json().then(function (payload) {
					return { ok: response.ok, payload: payload };
				});
			})
			.then(function (outcome) {
				setBusy(false);

				if (!outcome.ok) {
					// Field-level errors from the server land on the fields.
					var errors = (outcome.payload && outcome.payload.errors) || null;
					if (errors) {
						Object.keys(errors).forEach(function (name) {
							var field = form.querySelector('[name="' + name + '"]');
							if (field) {
								showFieldError(field, errors[name]);
							}
						});
					}
					showResult(
						'error',
						'That did not send',
						'<p>' + ((outcome.payload && outcome.payload.message) ||
							'Something went wrong. Please try again, or call the town office on ' +
							'<a href="tel:+14345683091">434-568-3091</a>.') + '</p>'
					);
					return;
				}

				var reference = outcome.payload && outcome.payload.reference;
				var confirm = (outcome.payload && outcome.payload.message) || form.dataset.confirm ||
					'Thank you. Your message has been sent.';

				form.hidden = true;
				showResult(
					'success',
					'Sent',
					'<p>' + confirm + '</p>' +
						(reference
							? '<p>Your reference number is <span class="reference">' + reference +
								'</span>. Please quote it if you contact the office about this.</p>'
							: '')
				);
			})
			.catch(function () {
				setBusy(false);
				showResult(
					'error',
					'That did not send',
					'<p>We could not reach the town office. Please check your connection and try again, ' +
						'or call <a href="tel:+14345683091">434-568-3091</a>.</p>'
				);
			});
	});

	// Clear a field's error as soon as the person fixes it.
	form.addEventListener('input', function (event) {
		var group = groupOf(event.target);
		if (group && group.classList.contains('field--invalid')) {
			group.classList.remove('field--invalid');
			var slot = group.querySelector('[data-error]');
			if (slot) {
				slot.textContent = '';
			}
			event.target.removeAttribute('aria-invalid');
		}
	});
})();
