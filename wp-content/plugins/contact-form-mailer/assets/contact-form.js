/* global cfm_ajax */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var form      = document.getElementById('cfm-form');
		if (!form) return;

		var submitBtn    = document.getElementById('cfm-submit-btn');
		var responseDiv  = document.getElementById('cfm-response');
		var messageField = document.getElementById('cfm_message');
		var charCount    = document.getElementById('cfm_message_count');

		// -----------------------------------------------------------------------
		// Character counter for message field
		// -----------------------------------------------------------------------
		if (messageField && charCount) {
			messageField.addEventListener('input', function () {
				var len = messageField.value.length;
				charCount.textContent = len + ' / 5000';
				charCount.style.color = len > 4800 ? '#ef4444' : '#9ca3af';
			});
		}

		// -----------------------------------------------------------------------
		// Real-time validation (blur → validate; input → re-validate if already
		// marked as error so the user sees the checkmark as soon as they fix it)
		// -----------------------------------------------------------------------
		['cfm_name', 'cfm_surname', 'cfm_email', 'cfm_phone', 'cfm_message'].forEach(function (id) {
			var el = document.getElementById(id);
			if (!el) return;

			el.addEventListener('blur', function () {
				validateField(el);
			});

			el.addEventListener('input', function () {
				if (el.classList.contains('cfm-input-error')) {
					validateField(el);
				}
			});
		});

		var privacyBox = document.getElementById('cfm_privacy');
		if (privacyBox) {
			privacyBox.addEventListener('change', validatePrivacy);
		}

		// -----------------------------------------------------------------------
		// Form submit
		// -----------------------------------------------------------------------
		form.addEventListener('submit', function (e) {
			e.preventDefault();

			clearResponse();

			if (!validateAll()) {
				scrollToFirstError();
				return;
			}

			setLoading(true);

			var formData = new FormData(form);
			formData.set('action', 'cfm_submit');

			fetch(cfm_ajax.ajax_url, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData,
			})
				.then(function (res) {
					if (!res.ok) throw new Error('HTTP ' + res.status);
					return res.json();
				})
				.then(function (data) {
					setLoading(false);

					if (data.success) {
						showResponse('success', data.data.message);
						form.reset();
						clearAllValidation();
						if (charCount) charCount.textContent = '0 / 5000';
						responseDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
					} else {
						if (data.data && data.data.errors) {
							applyServerErrors(data.data.errors);
							scrollToFirstError();
						} else {
							showResponse('error', (data.data && data.data.message) || 'Došlo je do neočekivane greške. Molimo pokušajte ponovo.');
						}
					}
				})
				.catch(function () {
					setLoading(false);
					showResponse('error', 'Mrežna greška — molimo provjerite vezu i pokušajte ponovo.');
				});
		});

		// -----------------------------------------------------------------------
		// Validation helpers
		// -----------------------------------------------------------------------

		function validateField(input) {
			var id    = input.id;
			var value = input.value.trim();
			var error = '';

			switch (id) {
				case 'cfm_name':
					if (!value) {
						error = 'Ime je obavezno.';
					} else if (value.length < 2) {
						error = 'Ime mora imati najmanje 2 znaka.';
					}
					break;

				case 'cfm_surname':
					if (!value) {
						error = 'Prezime je obavezno.';
					} else if (value.length < 2) {
						error = 'Prezime mora imati najmanje 2 znaka.';
					}
					break;

				case 'cfm_email':
					if (!value) {
						error = 'E-mail adresa je obavezna.';
					} else if (!isValidEmail(value)) {
						error = 'Molimo unesite ispravnu e-mail adresu.';
					}
					break;

				case 'cfm_phone':
					// Telefon nije obavezan — validira se samo ako je unesen
					if (value && !/^[\+\d\s\-\(\)]{6,30}$/.test(value)) {
						error = 'Molimo unesite ispravan broj telefona (brojevi, razmaci, +, -, zagrade).';
					}
					break;

				case 'cfm_message':
					if (!value) {
						error = 'Poruka je obavezna.';
					} else if (value.length < 10) {
						error = 'Poruka mora imati najmanje 10 znakova.';
					}
					break;
			}

			setFieldState(id, error);
			return error === '';
		}

		function validatePrivacy() {
			var cb  = document.getElementById('cfm_privacy');
			var err = document.getElementById('cfm_privacy_error');
			if (!cb || !err) return true;

			if (!cb.checked) {
				err.textContent = 'Morate prihvatiti uvjete privatnosti.';
				err.classList.add('cfm-visible');
				return false;
			}
			err.textContent = '';
			err.classList.remove('cfm-visible');
			return true;
		}

		function validateAll() {
			var valid = true;

			['cfm_name', 'cfm_surname', 'cfm_email', 'cfm_phone', 'cfm_message'].forEach(function (id) {
				var el = document.getElementById(id);
				if (el && !validateField(el)) valid = false;
			});

			if (!validatePrivacy()) valid = false;
			return valid;
		}

		// -----------------------------------------------------------------------
		// DOM helpers
		// -----------------------------------------------------------------------

		function setFieldState(fieldId, errorMsg) {
			var input   = document.getElementById(fieldId);
			var errorEl = document.getElementById(fieldId + '_error');
			if (!input || !errorEl) return;

			if (errorMsg) {
				input.classList.add('cfm-input-error');
				input.classList.remove('cfm-input-valid');
				errorEl.textContent = errorMsg;
				errorEl.classList.add('cfm-visible');
			} else {
				input.classList.remove('cfm-input-error');
				if (input.value.trim()) input.classList.add('cfm-input-valid');
				errorEl.textContent = '';
				errorEl.classList.remove('cfm-visible');
			}
		}

		function applyServerErrors(errors) {
			Object.keys(errors).forEach(function (key) {
				var el = document.getElementById(key);
				if (el) {
					setFieldState(key, errors[key]);
				} else {
					// e.g. privacy error
					var errEl = document.getElementById(key + '_error');
					if (errEl) {
						errEl.textContent = errors[key];
						errEl.classList.add('cfm-visible');
					}
				}
			});
		}

		function clearAllValidation() {
			['cfm_name', 'cfm_surname', 'cfm_email', 'cfm_phone', 'cfm_message'].forEach(function (id) {
				var el = document.getElementById(id);
				if (el) {
					el.classList.remove('cfm-input-valid', 'cfm-input-error');
				}
				setFieldState(id, '');
			});
			var privErr = document.getElementById('cfm_privacy_error');
			if (privErr) {
				privErr.textContent = '';
				privErr.classList.remove('cfm-visible');
			}
		}

		function scrollToFirstError() {
			var first = form.querySelector('.cfm-input-error, input:invalid');
			if (first) {
				first.scrollIntoView({ behavior: 'smooth', block: 'center' });
				first.focus({ preventScroll: true });
			}
		}

		function setLoading(loading) {
			submitBtn.disabled = loading;
			if (loading) {
				submitBtn.classList.add('cfm-loading');
			} else {
				submitBtn.classList.remove('cfm-loading');
			}
		}

		function showResponse(type, message) {
			responseDiv.className = 'cfm-response ' + (type === 'success' ? 'cfm-success' : 'cfm-error-msg');
			responseDiv.textContent = message;
		}

		function clearResponse() {
			responseDiv.className = 'cfm-response';
			responseDiv.textContent = '';
		}

		// -----------------------------------------------------------------------
		// Utilities
		// -----------------------------------------------------------------------

		function isValidEmail(value) {
			// RFC-compliant enough for real-world use
			return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value);
		}
	});
})();
