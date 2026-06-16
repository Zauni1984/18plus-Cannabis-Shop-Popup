(function () {
	'use strict';

	function initFlipbook(root) {
		var pages = root.querySelectorAll('.wcpc-flipbook__page');
		if (!pages.length) return;

		var current = 0;
		var total = pages.length;
		var currentEl = root.querySelector('[data-wcpc-current]');
		var totalEl = root.querySelector('[data-wcpc-total]');
		if (totalEl) totalEl.textContent = total;

		function show(index) {
			if (index < 0 || index >= total) return;
			pages.forEach(function (p, i) {
				p.classList.remove('is-active', 'is-prev', 'is-next');
				if (i === index) p.classList.add('is-active');
				else if (i < index) p.classList.add('is-prev');
				else p.classList.add('is-next');
			});
			current = index;
			if (currentEl) currentEl.textContent = (index + 1);
		}

		var prevBtn = root.querySelector('[data-wcpc-prev]');
		var nextBtn = root.querySelector('[data-wcpc-next]');
		if (prevBtn) prevBtn.addEventListener('click', function () { show(Math.max(0, current - 1)); });
		if (nextBtn) nextBtn.addEventListener('click', function () { show(Math.min(total - 1, current + 1)); });

		document.addEventListener('keydown', function (e) {
			if (e.target.matches('input, textarea, select')) return;
			if (e.key === 'ArrowLeft')  show(Math.max(0, current - 1));
			if (e.key === 'ArrowRight') show(Math.min(total - 1, current + 1));
		});

		// Swipe.
		var startX = null;
		root.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
		root.addEventListener('touchend', function (e) {
			if (startX === null) return;
			var dx = e.changedTouches[0].clientX - startX;
			if (Math.abs(dx) > 50) {
				if (dx < 0) show(Math.min(total - 1, current + 1));
				else show(Math.max(0, current - 1));
			}
			startX = null;
		});

		show(0);
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.wcpc-flipbook').forEach(initFlipbook);
	});
})();
