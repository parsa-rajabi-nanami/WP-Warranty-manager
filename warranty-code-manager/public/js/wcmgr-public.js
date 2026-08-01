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
		$('#wcmgr-warranty-form').on('submit', function (e) {

			e.preventDefault();

			let form = $(this);
			let code = form.find('input[name=\"warranty_code\"]').val();

			form.addClass('wcmgr-warranty-loading');

			$('.wcmgr-warranty-message')
				.stop(true, true)
				.hide()
				.html(warrantyCodeManager.msg_checking)
				.fadeIn(200);
			$.ajax({
				url: warrantyCodeManager.ajax_url,
				type: 'POST',
				data: {
					action: 'wcmgr_activate_warranty',
					nonce: warrantyCodeManager.nonce,
					warranty_code: code
				},
				success: function (response) {

					form.removeClass('wcmgr-warranty-loading');

					if (response.success) {

						$('.wcmgr-warranty-message')
							.stop(true, true)
							.hide()
							.html(response.data.message)
							.fadeIn(200);

					} else {

						$('.wcmgr-warranty-message')
							.stop(true, true)
							.hide()
							.html(response.data.message)
							.fadeIn(200);

					}
				},
				error: function () {

					form.removeClass('wcmgr-warranty-loading');

					$('.wcmgr-warranty-message')
						.stop(true, true)
						.hide()
						.html(warrantyCodeManager.msg_error)
						.fadeIn(200);

				}
			});
		});
	});
})(jQuery);
