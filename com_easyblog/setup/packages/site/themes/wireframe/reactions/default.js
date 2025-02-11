EasyBlog.require()
.script('site/reactions')
.done(function($) {

	$('[data-reactions]').implement(EasyBlog.Controller.Reactions, {
		"allowed": <?php echo $canReact ? 'true' : 'false'; ?>,
		"disallowedMessage": "<?php echo JText::_('COM_EB_PLEASE_LOGIN_TO_REACT');?>"
	});
});