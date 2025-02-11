EasyBlog.ready(function($) {

	function accept(ids) {

		if (typeof(ids) == 'number') {
			ids = [ids];
		}

		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/blogs/confirmAccept', {"ids" : ids})
		});
	};

	function reject(ids) {
		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/blogs/confirmReject', {"ids" : ids})
		});
	};

	function remove(ids) {
		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/blogs/confirmRemovePending', {"ids" : ids})
		});
	};

	$.Joomla("submitbutton", function(action) {
		var selected = [];

		$('input[name=cid\\[\\]]:checked').each(function() {
			var value = $(this).val();
			selected.push(value);
		});

		if (action == 'blogs.approve') {
			accept(selected);
			return;
		}

		if (action == 'blogs.reject') {
			reject(selected);
			return;
		}

		if (action == 'pending.remove') {
			remove(selected);
			return;
		}

	});

	$('[data-blog-accept]').on('click', function(event) {
		event.stopPropagation();
		var id = $(this).data('id');

		accept(id);
	});

	$('[data-blog-reject]').on('click', function(event) {
		event.stopPropagation();
		var id = $(this).data('id');

		reject(id);
	});
});
