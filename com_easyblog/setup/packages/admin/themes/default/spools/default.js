EasyBlog.ready(function($) {

	$.Joomla("submitbutton", function(action) {

		if (action=='purge') {
			if (!confirm('<?php echo JText::_('COM_EASYBLOG_CONFIRM_PURGE', true);?>')) {
				return false;
			}
		}

		$.Joomla("submitform", [action]);
	});

	$('[data-mailer-preview]').on('click', function() {

		EasyBlog.dialog({
			content: EasyBlog.ajax('admin/views/spools/preview', {"id" : $(this).data('id')})
		});
	});

});
