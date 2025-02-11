EasyBlog.ready(function($) {

	$('[data-blog-description]').on('change', function() {
		var enabled = $(this).val() == 1;

		if (!enabled) {
			$('[data-blog-description-options]').addClass('hide');
			return;
		}

		$('[data-blog-description-options]').removeClass('hide');
	});

});