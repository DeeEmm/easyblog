EasyBlog.ready(function($){

	$('[data-migrate-blogger]').on('click', function() {

		if ($('[data-xml-blogger]').val() === null) {
		    EasyBlog.dialog(
                    {
                        content :'Please select xml file to proceed.',

                    });
		    return;
		}

		if($('[data-author-id]').val() == '') {
			EasyBlog.dialog(
                    {
                        content     : 'Please enter your user id in Blog Import As field.',

                    });
		    return;
		}

		if ($('[data-easyblog-category]').val() == '') {
			EasyBlog.dialog(
                    {
                        content     : 'Please select category.',

                    });
		    return;
		}

		// Disable the button from being clicked twice
		$(this).attr('disabled', "true");

		// Update the buttons message
		$(this).html('<i class="fdi fa fa-cog fa-spin"></i> <?php echo JText::_('COM_EASYBLOG_UPDATING', true);?>');

		// Hide the no progress message
		$('[data-progress-empty]').addClass('hide');

		// Ensure that the progress is always reset to empty just in case the user runs it twice.
		$('[data-progress-status]').html('');

		// clear the stats.
		$('[data-progress-stat]').html('');

		//show the loading icon
		$('[data-progress-loading]').removeClass('hide');

		// //process the migration
		window.migrateArticle();
	});

window.migrateArticle = function() {

		// Get the values from the form
		var categoryId = $('[data-easyblog-category]').val(),
			authorId = $('[data-author-id]').val(),
			xmlFile = $('[data-xml-blogger]').val();

		EasyBlog.ajax('admin/views/migrators/migrateArticle',
		{
			"component"	: "xml_blogger",
			"authorId"	: authorId,
			"categoryId" : categoryId,
			"xmlFile": xmlFile
		},
		{
			append: function(selector, message) {
				$(selector).append(message);
			}
		})
		.done(function(hasMore)
		{
			// If there's still items to render, run a recursive loop until it doesn't have any more items;
			if (hasMore == true) {
				window.migrateArticle();
				return;
			}

			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');

			$('[data-migrate-blogger]').removeAttr('disabled');
			$('[data-migrate-blogger]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_COMPLETED', true);?>');

			if (hasMore == 'parseFailed') {
				$('[data-migrate-blogger]').removeAttr('disabled');
				$('[data-migrate-blogger]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_PARSE_FAILED', true);?>');
			}

			if (hasMore == 'fileNotExist') {
				$('[data-migrate-blogger]').removeAttr('disabled');
				$('[data-migrate-blogger]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_FILE_DOES_NOT_EXIST', true);?>');
			}

			if (hasMore == 'noitem'){
				$('[data-migrate-blogger]').removeAttr('disabled');
				$('[data-migrate-blogger]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_NO_ITEM', true);?>');
			}

		});
	}

});
