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
})();
