
EasyBlog.ready(function($) {

	$('[data-composer-truncate-option]').on('change', function() {
		var media = $(this).data('composer-truncate-option');
		var value = $(this).val();
		var itemInput = $('[data-composer-truncate-items-' + media + ']');
		
		if (value != 'hidden') {

			itemInput.removeClass('hide');
			return;
		}

		itemInput.addClass('hide');
	});

	$('[data-truncate-type]').on('change', function() {
		var val = $(this).val();
		
		if (val == 'chars' || val == 'words') {
			$('[data-max-chars]').removeClass('hide');
			$('[data-max-tag]').addClass('hide');
		} else {
			$('[data-max-tag]').removeClass('hide');
			$('[data-max-chars]').addClass('hide');
		}
	});
});
