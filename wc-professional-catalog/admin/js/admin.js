(function ($) {
	'use strict';

	$(function () {
		// Color pickers.
		if ($.fn.wpColorPicker) {
			$('.wcpc-color').wpColorPicker();
		}

		// Media frame for logo.
		var frame;
		$('#wcpc-logo-pick').on('click', function (e) {
			e.preventDefault();
			if (frame) { frame.open(); return; }
			frame = wp.media({
				title: 'Logo wählen',
				button: { text: 'Verwenden' },
				multiple: false,
				library: { type: 'image' }
			});
			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				$('#wcpc_logo_id').val(attachment.id);
				$('#wcpc-logo-preview').attr('src', attachment.url).show();
			});
			frame.open();
		});

		$('#wcpc-logo-remove').on('click', function (e) {
			e.preventDefault();
			$('#wcpc_logo_id').val(0);
			$('#wcpc-logo-preview').attr('src', '').hide();
		});
	});
})(jQuery);
