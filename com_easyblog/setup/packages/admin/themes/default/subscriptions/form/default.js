EasyBlog.ready(function($){
	$('[data-subscription-type]').on('change', function() {
		var value = $(this).val();

		$('[data-subscriptions]').addClass('hide');

		if (value == 'site') {
			return;
		}

		$('[data-subscriptions=' + value + ']').removeClass('hide');
		return;
	});

	$.Joomla('submitbutton', function(action) {
		console.log(action); 

		if (action == 'subscriptions.cancel') {
			window.location = '<?php echo JURI::base();?>index.php?option=com_easyblog&view=subscriptions';
			return;
		}

		if (action == 'subscriptions.create' || action == 'subscriptions.save' || action == 'subscriptions.apply') {
			return $.Joomla('submitform', [action]);
		}

		return;
	});

});