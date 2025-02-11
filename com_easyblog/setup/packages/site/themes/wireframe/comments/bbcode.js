window.EasyBlogBBCodeSettings = {

	previewParserVar: 'data',
	markupSet: [
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_BOLD', true);?>', key:'B', openWith:'[b]', closeWith:'[/b]', className:'markitup-bold'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_ITALIC', true);?>', key:'I', openWith:'[i]', closeWith:'[/i]', className:'markitup-italic'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_UNDERLINE', true);?>', key:'U', openWith:'[u]', closeWith:'[/u]', className:'markitup-underline'},
		{separator:'---------------' },
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_URL', true);?>', key: 'L', openWith: '[url=[![Link:]!]]', closeWith: '[/url]', beforeInsert: function(h ) {},className: 'markitup-url'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_PICTURE', true);?>', key:'P', replaceWith:'[img][![Url]!][/img]', className:'markitup-picture'},
		{separator:'---------------' },
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_BULLETS', true);?>', openWith:'[list]\\n', closeWith:'\\n[/list]', className:'markitup-bullet'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_NUMERIC', true);?>', openWith:'[list=[![Starting number]!]]\\n', closeWith:'\\n[/list]', className:'markitup-numeric'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_LIST', true);?>', openWith:'[*] ', className:'markitup-list'},
		{separator:'---------------' },
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_QUOTES', true);?>', openWith:'[quote]', closeWith:'[/quote]', className:'markitup-quote'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_CLEAN', true);?>', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } , className:'markitup-clean'},
		{separator:'---------------' },
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_HAPPY', true);?>', openWith:':D', className:'markitup-happy'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_SMILE', true);?>', openWith:':)', className:'markitup-smile'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_SURPRISED', true);?>', openWith:':o', className:'markitup-surprised'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_TONGUE', true);?>', openWith:':p', className:'markitup-tongue'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_UNHAPPY', true);?>', openWith:':(', className:'markitup-unhappy'},
		{name:'<?php echo JText::_('COM_EASYBLOG_BBCODE_WINK', true);?>', openWith:';)', className:'markitup-wink'}
	]
};


EasyBlog.require()
.script('site/vendors/markitup')
.done(function($) {
	$('#comment').markItUp(window.EasyBlogBBCodeSettings);
});