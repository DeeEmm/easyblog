EasyBlog.ready(function($){

	var checkRules = function(type) {
		var value = type == 'yes' ? 1 : 0;

		$('[data-acl-value]')
			.val(value)
			.trigger('change');
	}

	$.Joomla("submitbutton", function(action) {

		if (action == 'acl.enable') {
			checkRules('yes');

			return false;
		} else if(action == 'acl.disable') {
			checkRules('no');

			return false;
		}

		if (action == 'cancel') {
			window.location = '<?php echo JRoute::_('index.php?option=com_easyblog&view=acls', false);?>';
			return;
		}

		$.Joomla("submitform", [action]);
	});

	$('[data-acl-value]').on('change', function() {

		var value = $(this).val();
		var className = value == "1" ? 'acl-yes' : 'acl-no'; 
		var container = $(this).parents('[data-acl-container]');

		container.removeClass('acl-yes acl-no')
			.addClass(className);
	});
});
