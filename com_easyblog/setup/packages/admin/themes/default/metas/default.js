EasyBlog.ready(function($) {

	$.Joomla('submitbutton', function(action) {

		if (action == 'meta.cancel') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog';
			return false;
		}

		if (action == 'meta.restore') {
			EasyBlog.dialog({
				"content": EasyBlog.ajax('admin/views/metas/updateMetaConfirmation')
			});
			
			return false;
		};

		$.Joomla('submitform', [action]);
	});

});
