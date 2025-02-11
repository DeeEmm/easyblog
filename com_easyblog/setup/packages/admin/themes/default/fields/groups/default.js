EasyBlog.ready(function($) {

	$.Joomla('submitbutton', function(task) {
		if (task == 'fields.addGroup') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=fields&layout=groupForm'; 

			return false;
		}
		
		$.Joomla('submitform', [task]);

	});
});
