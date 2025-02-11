EasyBlog.ready(function($) {

	$.Joomla('submitbutton', function(task) {

		if (task == 'cancel') {
			window.location = '<?php echo JURI::base();?>index.php?option=com_easyblog&view=blogs&layout=templates';
			return;
		}

		Joomla.submitform([task]);
	});

	$('[data-thumbnail-restore-default-button]').on('click', function() {

		var wrapper = $(this).parents('[data-post-template]');
		var imageWrapper = $('[data-thumbnail-image]');
		var id = wrapper.data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('admin/views/blogs/confirmDeleteThumbnails'),
			bindings: {
				'{restoreButton} click': function() {

					EasyBlog.ajax('admin/controllers/blogs/deleteTemplateThumbnails', {'id' : id}).done(function() {
						var buttonArea = wrapper.find('[data-thumbnail-restore-default-wrap]');
						var defaultThumbnail = wrapper.data('defaultThumbnail');

						buttonArea.hide();
						imageWrapper.attr('src', defaultThumbnail);

						EasyBlog.dialog().close();
					});
				}
			}
		});
	});
});