
EasyBlog.ready(function($) {

	$.Joomla("submitbutton", function(action) {

		if ((action == 'bloggers.purgeAll')) {
			if (confirm('<?php echo JText::_("COM_EB_USER_DOWNLOAD_PURGE_ALL_CONFIRMATION", true); ?>')) {
				$.Joomla('submitform', [action]);
				return;
			}
		}

		if ((action == 'bloggers.removeRequest')) {
			if (confirm('<?php echo JText::_("COM_EB_USER_DOWNLOAD_DELETE_CONFIRMATION", true); ?>')) {
				$.Joomla('submitform', [action]);
				return;
			}
		}
		
		return;
	});
});
