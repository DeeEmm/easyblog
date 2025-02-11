
EasyBlog.ready(function($) {

	$.Joomla("submitbutton", function(action) {
		// Get selected list items.
		var selected    = new Array;

		$('[data-table-grid]').find('input[name=cid\\[\\]]:checked').each(function(i , el ){
			selected.push($(el).val());
		});

		$.Joomla("submitform", [action]);

	});

});
