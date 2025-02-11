EasyBlog.ready(function($) {

	$.Joomla('submitbutton', function(task) {
		if (task == 'fields.add') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=fields&layout=form'; 

			return false;
		}

		$.Joomla('submitform', [task]);
	});
});
