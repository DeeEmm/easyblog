
EasyBlog.require()
.done(function($) {
	$.Joomla('submitbutton', function(task) {
		if (task == 'meta.cancel') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=metas';
			return false;
		}

		$.Joomla('submitform', [task]);
	});
});
