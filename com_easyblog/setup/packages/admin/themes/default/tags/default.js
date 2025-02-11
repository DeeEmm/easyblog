EasyBlog.ready(function($) {

	$.Joomla("submitbutton", function(action) {

		if (action == 'tags.new') {

			window.location = 'index.php?option=com_easyblog&view=tags&layout=form';
			return;
		}
		
		$.Joomla("submitform", [action]);
	});

});
