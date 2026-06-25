/* WC Professional Catalog - lightweight lightbox (vanilla, ~2 KB). */
(function () {
	'use strict';

	var overlay, imgEl, captionEl, prevBtn, nextBtn, counterEl;
	var group = [];
	var idx = 0;

	function ensureOverlay() {
		if (overlay) return;
		overlay = document.createElement('div');
		overlay.className = 'wcpc-lightbox';
		overlay.innerHTML =
			'<button type="button" class="wcpc-lightbox__close" aria-label="Close">&times;</button>' +
			'<button type="button" class="wcpc-lightbox__nav wcpc-lightbox__prev" aria-label="Prev">&lsaquo;</button>' +
			'<figure class="wcpc-lightbox__figure">' +
				'<img class="wcpc-lightbox__img" alt="" />' +
				'<figcaption class="wcpc-lightbox__caption"></figcaption>' +
			'</figure>' +
			'<button type="button" class="wcpc-lightbox__nav wcpc-lightbox__next" aria-label="Next">&rsaquo;</button>' +
			'<div class="wcpc-lightbox__counter"></div>';
		document.body.appendChild(overlay);
		imgEl     = overlay.querySelector('.wcpc-lightbox__img');
		captionEl = overlay.querySelector('.wcpc-lightbox__caption');
		prevBtn   = overlay.querySelector('.wcpc-lightbox__prev');
		nextBtn   = overlay.querySelector('.wcpc-lightbox__next');
		counterEl = overlay.querySelector('.wcpc-lightbox__counter');

		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) close();
		});
		overlay.querySelector('.wcpc-lightbox__close').addEventListener('click', close);
		prevBtn.addEventListener('click', function () { show(idx - 1); });
		nextBtn.addEventListener('click', function () { show(idx + 1); });

		document.addEventListener('keydown', function (e) {
			if (!overlay.classList.contains('is-open')) return;
			if (e.key === 'Escape') close();
			if (e.key === 'ArrowLeft')  show(idx - 1);
			if (e.key === 'ArrowRight') show(idx + 1);
		});
	}

	function buildGroup(groupId, current) {
		var nodes = groupId
			? document.querySelectorAll('[data-wcpc-lightbox="1"][data-wcpc-group="' + groupId + '"]')
			: [current];
		group = Array.prototype.map.call(nodes, function (a) {
			return { href: a.getAttribute('href'), title: a.getAttribute('data-wcpc-title') || '' };
		});
		idx = Math.max(0, Array.prototype.indexOf.call(nodes, current));
	}

	function show(i) {
		if (!group.length) return;
		idx = ((i % group.length) + group.length) % group.length;
		var item = group[idx];
		imgEl.src = item.href;
		captionEl.textContent = item.title;
		captionEl.style.display = item.title ? '' : 'none';
		counterEl.textContent = (idx + 1) + ' / ' + group.length;
		var multi = group.length > 1;
		prevBtn.style.display = multi ? '' : 'none';
		nextBtn.style.display = multi ? '' : 'none';
		counterEl.style.display = multi ? '' : 'none';
	}

	function open(current) {
		ensureOverlay();
		var groupId = current.getAttribute('data-wcpc-group') || '';
		buildGroup(groupId, current);
		show(idx);
		overlay.classList.add('is-open');
		document.documentElement.classList.add('wcpc-lightbox-locked');
	}

	function close() {
		if (!overlay) return;
		overlay.classList.remove('is-open');
		document.documentElement.classList.remove('wcpc-lightbox-locked');
	}

	document.addEventListener('click', function (e) {
		var a = e.target.closest && e.target.closest('a[data-wcpc-lightbox="1"]');
		if (!a) return;
		e.preventDefault();
		open(a);
	});
})();
