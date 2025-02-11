EasyBlog.ready(function($) {

	$.Joomla('submitbutton', function(task) {

		if (task == 'subscriptions.form') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=subscriptions&layout=form';
			return;
		}

		$.Joomla('submitform', [task]);
	});
});
