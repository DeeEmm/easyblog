EasyBlog.ready(function($) {

	$('[data-post-print]').on('click', function(event) {
		var el = $(this),
			url = el.attr('href');


		window.open(url, 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');

		// Prevent bubbling up.
		event.preventDefault();
	});
});
