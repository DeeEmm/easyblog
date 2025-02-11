EasyBlog.ready(function($) {

	$('[data-captcha-type]').on('change', function() {
		var selected = $(this).val();

		var allOptions = $('[data-captcha]');
		var options = $('[data-captcha=' + selected + ']');
			
		allOptions.addClass('hidden');
		
		if (options.length > 0) {
			options.removeClass('hidden');
		}
	});
});