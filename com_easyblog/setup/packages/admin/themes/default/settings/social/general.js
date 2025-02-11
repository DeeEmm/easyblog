EasyBlog.ready(function($) {

	$('[data-social-button-type]').on('change', function() {
		var type = $(this).val();

		// Hide everything by default
		$('[data-social-group]').addClass('t-hidden');

		// Only show selected
		$('[data-social-group=' + type).removeClass('t-hidden');
	});

});
