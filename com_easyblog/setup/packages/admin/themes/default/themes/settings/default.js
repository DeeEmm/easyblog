EasyBlog.ready(function($) {

	$.Joomla('submitbutton', function(task) {

		if (task == 'cancel') {
			window.location = '<?php echo JURI::base();?>index.php?option=com_easyblog&view=themes';
			return;
		}

		$.Joomla('submitform', [task]);
	});
});