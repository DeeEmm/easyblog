EasyBlog.ready(function($) {

	<?php if (!$browse) { ?>
		$.Joomla("submitbutton", function(action) {
			if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
				$.Joomla("submitform", [action]);
			}
		});
	<?php } ?>

	$.Joomla('submitbutton', function(task) {

		if (task == 'add') {
			window.location = 'index.php?option=com_easyblog&view=teamblogs&layout=form';

			return false;
		}

		$.Joomla('submitform', [task]);
	});
});
