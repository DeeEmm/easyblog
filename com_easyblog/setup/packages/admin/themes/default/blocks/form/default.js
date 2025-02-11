EasyBlog.ready(function($) {
	$.Joomla("submitbutton", function(task) {

		if (task == 'blocks.cancel') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=blocks';
			return false;
		}

		$.Joomla("submitform", [task]);
	});
});