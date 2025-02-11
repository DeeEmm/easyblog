EasyBlog.ready(function($) {
	$.Joomla("submitbutton", function(action) {

		if (action == 'spools.reset') {

			if (!confirm('<?php echo JText::_('COM_EASYBLOG_EMAIL_CONFIRM_RESET', true);?>')) {
				return false;
			}
		}

		$.Joomla("submitform", [action]);
	});

	$(document).on('click.preview', '[data-mail-preview]', function(event) {
		event.preventDefault();
		event.stopPropagation();

		var button = $(this);
		var file = button.data('mail-preview');

		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/spools/templatePreview', {"file": file})
		});
	});
});
