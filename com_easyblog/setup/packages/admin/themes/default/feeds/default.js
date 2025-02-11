EasyBlog.ready(function($){

	$('[data-feed-import]').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();

		var button = $(this);
		var id = $(this).data('id');
		var log = $(this).parent().find('[data-feed-import-log]');

		button.addClass('is-loading');

		EasyBlog.ajax('admin/views/feeds/download', {
			"id" : id
		})
		.done(function(result) {
			EasyBlog.dialog({
				title: 'Test Result',
				content: '<p style="padding: 20px;">' + result.message + '</p>',
				width: 450,
				height: 120
			});
		})
		.always(function() {
			button.removeClass('is-loading');
		});
	});



	$.Joomla("submitbutton", function(action) {

		if (action == 'feeds.add') {
			window.location = '<?php echo JURI::base();?>index.php?option=com_easyblog&view=feeds&layout=form';
			return false;
		}

		$.Joomla("submitform", [action]);
	});
});
