
EasyBlog.require()
.library(
	'datetimepicker'
)
.script('shared/datetime')
.done(function($) {

	$('[data-created]').addController('EasyBlog.Controller.Post.Datetime', {
		format: "<?php echo JText::_('COM_EASYBLOG_MOMENTJS_DATE_DMY24H'); ?>",
		minDate: "<?php echo $post->created; ?>"
	});

	$.Joomla('submitbutton', function(task) {
		if (task == 'comment.cancel') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=comments';
			return false;
		}

		$.Joomla('submitform', [task]);
	});
});
 