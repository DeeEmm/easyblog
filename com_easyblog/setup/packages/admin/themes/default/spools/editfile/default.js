EasyBlog.require()
.done(function($) {

	$.Joomla('submitbutton', function(task) {

		if (task == 'cancel') {
			window.location = '<?php echo JURI::base();?>index.php?option=com_easyblog&view=spools&layout=editor';

			return;
		}

		$.Joomla('submitform', [task]);
		return false;
	});
});