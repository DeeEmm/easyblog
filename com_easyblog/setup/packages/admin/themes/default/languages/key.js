EasyBlog.ready(function($) {
	$.Joomla("submitbutton", function(task) {
		if (task == 'savekey') {
			$.Joomla('submitform', ['settings.saveApi']);
		}

		$.Joomla('submitform', [task]);
	});

});
