EasyBlog.ready(function($) {

	$('[data-delete-post]').on('click', function(){
		var id = $(this).data('id');

		EasyBlog.dialog({
			'content': EasyBlog.ajax('admin/views/reports/confirmDelete', {
							"id": id
						})
		});
	});

	$('[data-unpublish-post]').on('click', function(){
		var id = $(this).data('id');

		EasyBlog.dialog({
			'content': EasyBlog.ajax('admin/views/reports/confirmUnpublish', {
							"id": id
						})
		});
	});

	$.Joomla("submitbutton", function(action){

		$.Joomla("submitform", [action]);
	});
});
