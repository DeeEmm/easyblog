
EasyBlog.ready(function($){

	// Cropping settings for listings
	$('[data-cover-featured-crop]').on('change', function() {
		var value = $(this).val();

		// If cropping is disabled, we shouldn't display the height settings.
		if (value == 0) {
			$('[data-cover-featured-height]').addClass('hide');

			return;
		}

		$('[data-cover-featured-height]').removeClass('hide');
	});


	// Cropping settings for listings
	$('[data-cover-crop]').on('change', function() {
		var value = $(this).val();

		// If cropping is disabled, we shouldn't display the height settings.
		if (value == 0) {
			$('[data-cover-height]').addClass('hide');

			return;
		}

		$('[data-cover-height]').removeClass('hide');
	});

	// Cropping settings for entry
	$('[data-cover-crop-entry]').on('change', function() {
		var value = $(this).val();

		// If cropping is disabled, we shouldn't display the height settings.
		if (value == 0) {
			$('[data-cover-height-entry]').addClass('hide');

			return;
		}

		$('[data-cover-height-entry]').removeClass('hide');
	});

	// When full width is checked
	$('[data-cover-full-width]').on('change', function() {

		var checked = $(this).is(':checked');
		var widthSettings = $('[data-cover-width]');
		var alignSettings = $('[data-cover-alignment]');
		var widthInput = $(this).parents('[data-cover-full-width-wrapper]').siblings('[data-cover-width-input]');

		if (checked) {
			widthSettings.attr('disabled', 'disabled');
			alignSettings.addClass('hide');
			widthInput.addClass('hide');

			return;
		}

		widthInput.removeClass('hide');
		alignSettings.removeClass('hide');
		widthSettings.removeAttr('disabled');
	});


	// Entry full width settings
	$('[data-cover-full-width-entry]').on('change', function() {
		var checked = $(this).is(':checked');
		var widthSettings = $('[data-cover-width-entry]');
		var alignSettings = $('[data-cover-alignment-entry]');
		var widthInput = $(this).parents('[data-cover-full-width-wrapper]').siblings('[data-cover-width-input]');

		if (checked) {
			widthSettings.attr('disabled', 'disabled');
			alignSettings.addClass('hide');
			widthInput.addClass('hide');

			return;
		}

		widthInput.removeClass('hide');
		alignSettings.removeClass('hide');
		widthSettings.removeAttr('disabled');
	});
});