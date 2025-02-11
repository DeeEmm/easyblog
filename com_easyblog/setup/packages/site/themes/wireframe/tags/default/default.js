EasyBlog.ready(function($) {

	$('[data-tags-sorting]').on('change', function() {
		var dropdown = $(this);
		var value = $(this).val();
		var option = dropdown.find('option[value=' + value + ']');
		var url = option.data('url');
		
		if (!url) {
			return;
		}
		
		window.location = url;
	});
});