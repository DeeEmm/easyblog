
EasyBlog.ready(function($) {

	// Update author ordering
	$('[data-author-ordering]').on('click', function() {

		var button = $(this);
		var userId = button.data('id');
		var orderId = button.siblings('[data-ordering-id]').val();

		EasyBlog.ajax('admin/controllers/bloggers/orderingUpdate', {
			"userId" : userId,
			"orderId" : orderId
		});
	});

    $.Joomla("submitbutton", function(action) {

        if (action == 'bloggers.create') {
            window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=bloggers&layout=form';
            return false;
        }

		if (action == 'resetOrdering') {

			EasyBlog.dialog({
				"content": EasyBlog.ajax('admin/views/bloggers/confirmResetOrdering')
			});
            
            return false;
		}    

        $.Joomla('submitform', [action]);
    });
});
