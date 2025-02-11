EasyBlog.ready(function($) {
	$('[data-mm-preview-link]').on('change', function() {
		var value = $(this).val();

		if (value == 'custom' || value == 'custom_new') {
			$('[data-mm-preview-custom-url]').removeClass('t-hidden');
			return;
		}

		$('[data-mm-preview-custom-url]').addClass('t-hidden');
	});

	// When a variation is changed
	$('[data-mm-variation]').on('change', function() {
		var selected = $(this).find(':selected');
		var width = selected.data('width');
		var height = selected.data('height');
		var url = selected.data('url');
		var ratio = width / height;

		var inputUrl = $('[data-mm-image-url]');
		var inputWidth = $('[data-mm-image-width]');
		var inputHeight = $('[data-mm-image-height]');
		var inputRatio = $('[data-mm-image-ratio]');
		var inputNaturalRatio = $('[data-mm-image-ratio-natural]');

		inputRatio.val(ratio);
		inputNaturalRatio.val(ratio);
		inputUrl.val(url);
		inputWidth.val(width);
		inputHeight.val(height);

	});
});