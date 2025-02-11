EasyBlog.ready(function($) {

	// We don't want the user to click the button.
	$('[data-bookmark-button]').on('click', function(event) {
		event.stopPropagation();
		event.preventDefault();
	});
});