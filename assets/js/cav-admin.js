/*!
 * Cannabis Age Verifier – Admin UI
 */
(function () {
	'use strict';

	var presets = document.querySelector('[data-cav-age-presets]');
	var customWrap = document.querySelector('[data-cav-custom-age]');

	if (!presets || !customWrap) { return; }

	function syncCustom() {
		var checked = presets.querySelector('input[type="radio"]:checked');
		var isCustom = checked && checked.value === 'custom';
		if (isCustom) {
			customWrap.removeAttribute('hidden');
		} else {
			customWrap.setAttribute('hidden', '');
		}
		// Visual fallback for browsers without :has() support.
		var labels = presets.querySelectorAll('.cav-age-preset');
		for (var i = 0; i < labels.length; i++) {
			var input = labels[i].querySelector('input[type="radio"]');
			if (input && input.checked) {
				labels[i].classList.add('is-active');
			} else {
				labels[i].classList.remove('is-active');
			}
		}
	}

	presets.addEventListener('change', syncCustom);
	syncCustom();

	// Confirm before per-tab reset.
	var tabResetForms = document.querySelectorAll('[data-cav-reset]');
	for (var i = 0; i < tabResetForms.length; i++) {
		(function (form) {
			form.addEventListener('submit', function (ev) {
				var label = form.getAttribute('data-cav-reset') || '';
				var msg = 'Diesen Tab (' + label + ') wirklich auf Standardwerte zurücksetzen? Alle dort eingegebenen Werte gehen verloren.';
				if (!window.confirm(msg)) {
					ev.preventDefault();
				}
			});
		})(tabResetForms[i]);
	}

	// Confirm before global reset.
	var allReset = document.querySelector('[data-cav-reset-all]');
	if (allReset) {
		allReset.addEventListener('submit', function (ev) {
			var msg = 'ALLE Einstellungen wirklich auf Werkseinstellungen zurücksetzen? Dies betrifft sämtliche Tabs (Lizenz bleibt erhalten).';
			if (!window.confirm(msg)) { ev.preventDefault(); }
		});
	}
})();
