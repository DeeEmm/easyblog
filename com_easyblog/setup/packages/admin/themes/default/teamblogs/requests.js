EasyBlog.ready(function($) {
	function accept(ids) {

		if (typeof(ids) == 'number') {
			ids = [ids];
		}

		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/teamblogs/confirmAccept', {"ids" : ids})
		});
	};

	function reject(ids) {
		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/teamblogs/confirmReject', {"ids" : ids})
		});
	};

	$.Joomla("submitbutton", function(action) {
		var selected = [];

		$('input[name=cid\\[\\]]:checked').each(function() {
			var value = $(this).val();
			selected.push(value);
		});

		if (action == 'teamblogs.approve') {
			accept(selected);
			return;
		}

		if (action == 'teamblogs.reject') {
			reject(selected);
			return;
		}

	});
});
