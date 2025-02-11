EasyBlog.ready(function($) {

	$.Joomla('submitbutton' , function(task) {

		if (task == 'modules.discover') {
			window.location = 'index.php?option=com_easyblog&view=modules&layout=discover';
			return false;
		}

		if (task == 'modules.uninstall') {
			var selected = [];

			$('[data-table-grid]').find('input[name=cid\\[\\]]:checked').each(function(i , el ){
				selected.push($(el).val());
			});

			EasyBlog.dialog({
				content: EasyBlog.ajax('admin/views/dialogs/render', {
					'file' : 'admin/modules/dialog.delete'
				}),
				bindings: {
					"{submitButton} click": function() {
						$.Joomla('submitform', [task]);
					}
				}
			});

			return;
		}

		$.Joomla('submitform', [task]);
	});
});
