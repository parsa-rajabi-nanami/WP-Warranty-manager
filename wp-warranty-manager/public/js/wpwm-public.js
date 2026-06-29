(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


	jQuery(document).ready(function ($) {
		$('#wp-warranty-form').on('submit', function (e) {

			e.preventDefault();

			let form = $(this);
			let code = form.find('input[name=\"warranty_code\"]').val();

			form.addClass('wp-warranty-loading');

			$('.wp-warranty-message')
				.stop(true, true)
				.hide()
				.html(wpWarranty.msg_checking)
				.fadeIn(200);
			$.ajax({
				url: wpWarranty.ajax_url,
				type: 'POST',
				data: {
					action: 'wp_activate_warranty',
					nonce: wpWarranty.nonce,
					warranty_code: code
				},
				success: function (response) {

					form.removeClass('wp-warranty-loading');

					if (response.success) {

						$('.wp-warranty-message')
							.stop(true, true)
							.hide()
							.html(response.data.message)
							.fadeIn(200);

					} else {

						$('.wp-warranty-message')
							.stop(true, true)
							.hide()
							.html(response.data.message)
							.fadeIn(200);

					}
				},
				error: function () {

					form.removeClass('wp-warranty-loading');

					$('.wp-warranty-message')
						.stop(true, true)
						.hide()
						.html(wpWarranty.msg_error)
						.fadeIn(200);

				}
			});
		});
	});
})(jQuery);
