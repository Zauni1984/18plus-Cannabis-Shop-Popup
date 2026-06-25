(function ($) {
	'use strict';

	$(function () {
		var $root = $('.wcpc-pdf-generator');
		if (!$root.length) return;

		var nonce   = $root.data('nonce');
		var ajaxUrl = window.ajaxurl;

		var $startBtn  = $('#wcpc-pdf-start');
		var $cancelBtn = $('#wcpc-pdf-cancel');
		var $scope     = $('#wcpc-pdf-scope');

		var $progress      = $('#wcpc-pdf-progress');
		var $progressFill  = $('#wcpc-pdf-progress-fill');
		var $progressStat  = $('#wcpc-pdf-progress-status');
		var $progressProc  = $('#wcpc-pdf-progress-processed');
		var $progressTotal = $('#wcpc-pdf-progress-total');
		var $progressSec   = $('#wcpc-pdf-progress-section');
		var $progressErr   = $('#wcpc-pdf-progress-error');

		var currentJobId = null;
		var pollTimer    = null;

		function showProgress() { $progress.removeAttr('hidden'); }
		function hideProgress() { $progress.attr('hidden', 'hidden'); }

		function setProgress(rec) {
			if (!rec) return;
			var pct = rec.total_products > 0
				? Math.round(100 * rec.processed / rec.total_products)
				: 0;
			$progressFill.css('width', pct + '%').attr('data-pct', pct);
			$progressStat.text(rec.status);
			$progressProc.text(rec.processed);
			$progressTotal.text(rec.total_products);
			$progressSec.text(rec.current_section || '');

			if (rec.status === 'error') {
				$progressErr.text(rec.error || 'Unbekannter Fehler').removeAttr('hidden');
			} else {
				$progressErr.attr('hidden', 'hidden');
			}
		}

		function call(action, data) {
			return $.post(ajaxUrl, $.extend({
				action: action,
				_wpnonce: nonce
			}, data || {}));
		}

		function start() {
			var raw = $scope.val() || 'all|';
			var parts = raw.split('|');
			var scopeType  = parts[0];
			var scopeValue = parts[1] || '';
			var scopeLabel = $scope.find('option:selected').data('label') || $scope.find('option:selected').text();

			$startBtn.prop('disabled', true);
			showProgress();
			$progressErr.attr('hidden', 'hidden');
			$progressStat.text('queued');
			$progressFill.css('width', '0%');

			call('wcpc_pdf_start', {
				scope_type:  scopeType,
				scope_value: scopeValue,
				scope_label: scopeLabel
			}).done(function (res) {
				if (res && res.success && res.data) {
					currentJobId = res.data.id;
					$cancelBtn.show();
					setProgress(res.data);
					tickOnce();
				} else {
					$startBtn.prop('disabled', false);
					hideProgress();
					alert('Fehler beim Start: ' + (res && res.data && res.data.error || 'unbekannt'));
				}
			}).fail(function () {
				$startBtn.prop('disabled', false);
				hideProgress();
				alert('Netzwerkfehler beim Start.');
			});
		}

		function tickOnce() {
			if (!currentJobId) return;
			call('wcpc_pdf_tick', { id: currentJobId }).done(function (res) {
				if (!res || !res.success) return finishWithError(res);
				var rec = res.data;
				setProgress(rec);
				if (rec.status === 'done') return done(rec);
				if (rec.status === 'error') return finishWithError(rec);
				if (rec.status === 'cancelled') return cancelled();
				// Tick again right away - server already processed one batch.
				pollTimer = setTimeout(tickOnce, 200);
			}).fail(function () {
				// Network blip - back off and try status poll.
				pollTimer = setTimeout(statusPoll, 2000);
			});
		}

		function statusPoll() {
			if (!currentJobId) return;
			call('wcpc_pdf_status', { id: currentJobId }).done(function (res) {
				if (res && res.success) {
					var rec = res.data;
					setProgress(rec);
					if (rec.status === 'done') return done(rec);
					if (rec.status === 'error') return finishWithError(rec);
					if (rec.status === 'cancelled') return cancelled();
					pollTimer = setTimeout(tickOnce, 500);
				} else {
					pollTimer = setTimeout(statusPoll, 3000);
				}
			}).fail(function () {
				pollTimer = setTimeout(statusPoll, 5000);
			});
		}

		function done(rec) {
			$startBtn.prop('disabled', false);
			$cancelBtn.hide();
			currentJobId = null;
			// Reload to refresh the saved-PDFs list.
			setTimeout(function () { window.location.reload(); }, 600);
		}

		function finishWithError(payload) {
			$startBtn.prop('disabled', false);
			$cancelBtn.hide();
			currentJobId = null;
			var rec = payload && payload.data ? payload.data : payload;
			setProgress(rec || { status: 'error', error: 'Unbekannter Fehler' });
		}

		function cancelled() {
			$startBtn.prop('disabled', false);
			$cancelBtn.hide();
			currentJobId = null;
			hideProgress();
		}

		$startBtn.on('click', function (e) {
			e.preventDefault();
			start();
		});

		$cancelBtn.on('click', function (e) {
			e.preventDefault();
			if (!currentJobId) return;
			call('wcpc_pdf_cancel', { id: currentJobId });
			cancelled();
		});

		$('.wcpc-pdf-delete').on('click', function () {
			var id = $(this).data('id');
			if (!id) return;
			if (!confirm('PDF wirklich löschen?')) return;
			var $row = $(this).closest('tr');
			call('wcpc_pdf_delete', { id: id }).done(function () {
				$row.fadeOut(200, function () { $(this).remove(); });
			});
		});

		// If there is an active job from a previous browser tab, resume polling.
		var $statusEl = $('#wcpc-pdf-progress-status');
		var existingStatus = $statusEl.text().trim();
		if (existingStatus === 'processing' || existingStatus === 'queued' || existingStatus === 'finalizing') {
			var $row = $('#wcpc-pdf-list tr').first();
			currentJobId = $row.data('job-id') || null;
			if (currentJobId) {
				$startBtn.prop('disabled', true);
				$cancelBtn.show();
				showProgress();
				statusPoll();
			}
		}
	});
})(jQuery);
