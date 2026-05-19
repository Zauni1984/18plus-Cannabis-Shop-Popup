/*!
 * Cannabis Age Verifier – Frontend Popup
 * (c) BlockSocial UG (haftungsbeschränkt)
 * Vanilla JS, no dependencies. ~3kb gzipped.
 */
(function () {
	'use strict';

	if (typeof window.CAV_DATA !== 'object' || !window.CAV_DATA) {
		return;
	}

	var data = window.CAV_DATA;
	var root = document.getElementById('cav-root');
	if (!root) { return; }

	var html = document.documentElement;
	if (data.blockScroll) { html.classList.add('cav-locked'); }
	html.classList.remove('cav-loading');

	var form     = document.getElementById('cav-form');
	var errorEl  = document.getElementById('cav-error');
	var submitBtn = root.querySelector('[data-cav-submit]');
	var declineBtn = root.querySelector('[data-cav-decline]');
	var yesBtn   = root.querySelector('[data-cav-confirm-yes]');
	var noBtn    = root.querySelector('[data-cav-confirm-no]');

	function setError(msg) {
		if (!errorEl) { return; }
		errorEl.textContent = msg || '';
	}

	function setBusy(btn, busy) {
		if (!btn) { return; }
		if (busy) {
			btn.setAttribute('aria-busy', 'true');
			btn.disabled = true;
		} else {
			btn.removeAttribute('aria-busy');
			btn.disabled = false;
		}
	}

	function focusFirstInvalid() {
		var inputs = root.querySelectorAll('input');
		for (var i = 0; i < inputs.length; i++) {
			if (!inputs[i].value) { inputs[i].focus(); return; }
		}
	}

	function postVerify(payload, btn) {
		setError('');
		setBusy(btn, true);

		var controller = ('AbortController' in window) ? new AbortController() : null;
		var timeout = setTimeout(function () { if (controller) { controller.abort(); } }, 15000);

		return fetch(data.restUrl, {
			method: 'POST',
			credentials: 'same-origin',
			cache: 'no-store',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': data.nonce,
				'Accept': 'application/json'
			},
			body: JSON.stringify(payload),
			signal: controller ? controller.signal : undefined
		}).then(function (res) {
			clearTimeout(timeout);
			if (res.status === 429) { throw new Error('rate'); }
			return res.json().then(function (j) { return { ok: res.ok, body: j }; });
		}).then(function (r) {
			if (!r.ok) {
				throw new Error(r.body && r.body.message ? r.body.message : 'http');
			}
			if (r.body.verified) {
				// Smooth fade-out before allowing the page to be used.
				root.style.transition = 'opacity .35s ease';
				root.style.opacity = '0';
				setTimeout(function () {
					html.classList.remove('cav-locked');
					root.parentNode && root.parentNode.removeChild(root);
				}, 360);
			} else {
				// Hard redirect to education page.
				window.location.replace(r.body.redirect || data.redirectUrl);
			}
		}).catch(function (err) {
			clearTimeout(timeout);
			if (err && err.message === 'rate') {
				setError(data.i18n.rateLimited);
			} else {
				setError(data.i18n.networkError);
			}
		}).then(function () {
			setBusy(btn, false);
		});
	}

	function readDob() {
		if (!form) { return null; }
		var d = form.querySelector('input[name="day"]');
		var m = form.querySelector('input[name="month"]');
		var y = form.querySelector('input[name="year"]');
		if (!d || !m || !y) { return null; }
		return {
			day: parseInt(d.value, 10),
			month: parseInt(m.value, 10),
			year: parseInt(y.value, 10)
		};
	}

	function isPlausibleDob(dob) {
		if (!dob) { return false; }
		if (!dob.day || !dob.month || !dob.year) { return false; }
		if (dob.day < 1 || dob.day > 31) { return false; }
		if (dob.month < 1 || dob.month > 12) { return false; }
		var thisYear = new Date().getUTCFullYear();
		if (dob.year < 1900 || dob.year > thisYear) { return false; }
		var d = new Date(Date.UTC(dob.year, dob.month - 1, dob.day));
		if (d.getUTCFullYear() !== dob.year || d.getUTCMonth() !== dob.month - 1 || d.getUTCDate() !== dob.day) {
			return false;
		}
		return true;
	}

	function calcAge(dob) {
		var today = new Date();
		var age = today.getUTCFullYear() - dob.year;
		var m = (today.getUTCMonth() + 1) - dob.month;
		if (m < 0 || (m === 0 && today.getUTCDate() < dob.day)) { age--; }
		return age;
	}

	if (data.mode === 'dob' && form) {
		form.addEventListener('submit', function (ev) {
			ev.preventDefault();
			var dob = readDob();
			if (!isPlausibleDob(dob)) {
				setError(data.i18n.invalidDate);
				focusFirstInvalid();
				return;
			}
			// Cheap client-side prefilter; server is the source of truth.
			if (calcAge(dob) < data.minAge) {
				postVerify({ mode: 'dob', day: dob.day, month: dob.month, year: dob.year }, submitBtn);
				return;
			}
			postVerify({ mode: 'dob', day: dob.day, month: dob.month, year: dob.year }, submitBtn);
		});

		// Auto-tab between fields for fast UX
		var inputs = form.querySelectorAll('input[type="number"]');
		for (var i = 0; i < inputs.length; i++) {
			(function (idx, el) {
				el.addEventListener('input', function () {
					var max = parseInt(el.getAttribute('max'), 10) || 9999;
					if (el.value.length >= String(max).length && idx + 1 < inputs.length) {
						inputs[idx + 1].focus();
					}
				});
			})(i, inputs[i]);
		}

		if (declineBtn) {
			declineBtn.addEventListener('click', function () {
				postVerify({ mode: 'dob', day: 1, month: 1, year: new Date().getUTCFullYear() }, declineBtn);
			});
		}
	}

	if (data.mode === 'confirm') {
		if (yesBtn) { yesBtn.addEventListener('click', function () { postVerify({ mode: 'confirm', confirm: 'yes' }, yesBtn); }); }
		if (noBtn)  { noBtn.addEventListener('click',  function () { postVerify({ mode: 'confirm', confirm: 'no'  }, noBtn);  }); }
	}

	// Trap focus inside the modal for accessibility
	function trapFocus(ev) {
		if (ev.key !== 'Tab') { return; }
		var focusable = root.querySelectorAll('input, button, a[href]');
		if (!focusable.length) { return; }
		var first = focusable[0];
		var last  = focusable[focusable.length - 1];
		if (ev.shiftKey && document.activeElement === first) {
			last.focus(); ev.preventDefault();
		} else if (!ev.shiftKey && document.activeElement === last) {
			first.focus(); ev.preventDefault();
		}
	}
	root.addEventListener('keydown', trapFocus);

	// Initial focus on the first input.
	var firstInput = root.querySelector('input, button');
	if (firstInput) { try { firstInput.focus({ preventScroll: true }); } catch (e) { firstInput.focus(); } }
})();
