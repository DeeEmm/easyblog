EasyBlog.require()
.done(function($) {

    $.Joomla("submitbutton", function(task) {

        if (task != 'migrators.purge' || confirm('<?php echo JText::_('COM_EASYBLOG_CONFIRM_PURGE_HISTORY', true); ?>')) {
            $.Joomla("submitform", [task]);
        }
    });
});
