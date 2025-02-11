EasyBlog.ready(function($) {
	$.Joomla("submitbutton", function(action) {
		$.Joomla('submitform', [action]);
	});
});
