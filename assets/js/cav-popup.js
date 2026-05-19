/*!
 * Cannabis Age Verifier – Frontend Popup
 * (c) BlockSocial UG (haftungsbeschränkt)
 * Vanilla JS, no dependencies. ~3kb gzipped. Cache-safe.
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
	var isDialog = (typeof root.showModal === 'function');

	// Defensive reparent + open: themes occasionally wrap wp_footer in
	// a transformed/filtered container which breaks position:fixed for
	// non-dialog fallbacks. Even for the real <dialog>, reparenting to
	// <body> avoids quirks where some browsers refuse to elevate a
	// dialog nested too deeply.
	if (root.parentNode !== document.body) {
		document.body.appendChild(root);
	}

	function ensureOpen() {
		if (isDialog) {
			if (!root.open) { try { root.showModal(); } catch (e) { root.setAttribute('open', ''); } }
		} else {
			root.setAttribute('open', '');
		}
	}
	function closeDialog() {
		if (isDialog && typeof root.close === 'function' && root.open) {
			try { root.close(); } catch (e) { /* noop */ }
		}
		root.removeAttribute('open');
	}

	// Prevent the visitor from dismissing the modal via Esc / browser UI.
	if (isDialog) {
		root.addEventListener('cancel', function (e) { e.preventDefault(); });
	}

	// Defense in depth: also read the companion cookie here in case the
	// anti-flash bootstrap was stripped by some plugin / minifier.
	function readFlag() {
		var name = (data.flagCookie || 'cav_age_verified_js').replace(/[^a-zA-Z0-9_]/g, '');
		var re = new RegExp('(?:^|;\\s*)' + name + '=([^;]+)');
		var m = document.cookie.match(re);
		return m ? decodeURIComponent(m[1]) : '';
	}

	var flag = readFlag();

	if (flag === '1') {
		// Already verified as adult. Close + remove dialog.
		html.classList.add('cav-verified');
		html.classList.remove('cav-popup-on');
		closeDialog();
		if (root.parentNode) { root.parentNode.removeChild(root); }
		return;
	}

	if (flag === '0') {
		// Recent minor verdict. Hard-redirect synchronously.
		window.location.replace(data.redirectUrl);
		return;
	}

	// No verdict yet. Make sure the dialog is open (idempotent with
	// the anti-flash bootstrap) and scroll is locked.
	ensureOpen();
	if (data.blockScroll) { html.classList.add('cav-popup-on'); }

	var form       = document.getElementById('cav-form');
	var errorEl    = document.getElementById('cav-error');
	var submitBtn  = root.querySelector('[data-cav-submit]');
	var declineBtn = root.querySelector('[data-cav-decline]');
	var yesBtn     = root.querySelector('[data-cav-confirm-yes]');
	var noBtn      = root.querySelector('[data-cav-confirm-no]');

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

	function dismissPopup() {
		root.style.transition = 'opacity .35s ease';
		root.style.opacity = '0';
		setTimeout(function () {
			html.classList.remove('cav-popup-on');
			html.classList.add('cav-verified');
			closeDialog();
			if (root.parentNode) { root.parentNode.removeChild(root); }
		}, 360);
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
				dismissPopup();
			} else {
				window.location.replace(r.body.redirect || data.redirectUrl);
			}
		}).catch(function (err) {
			clearTimeout(timeout);
			setError(err && err.message === 'rate' ? data.i18n.rateLimited : data.i18n.networkError);
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

	if (data.mode === 'dob' && form) {
		form.addEventListener('submit', function (ev) {
			ev.preventDefault();
			var dob = readDob();
			if (!isPlausibleDob(dob)) {
				setError(data.i18n.invalidDate);
				focusFirstInvalid();
				return;
			}
			postVerify({ mode: 'dob', day: dob.day, month: dob.month, year: dob.year }, submitBtn);
		});

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

	var firstInput = root.querySelector('input, button');
	if (firstInput) { try { firstInput.focus({ preventScroll: true }); } catch (e) { firstInput.focus(); } }
})();
