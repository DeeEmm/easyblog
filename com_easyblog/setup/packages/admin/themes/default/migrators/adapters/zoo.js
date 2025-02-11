

EasyBlog.ready(function($) {


	$('[data-migrate-zoo]').on('click', function() {

		if( $('[data-applicationid-zoo]').val() === '0')
		{
			 EasyBlog.dialog(
                    {
                        content     : 'Please select application to proceed.',

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
		var applicationId = $('[data-applicationid-zoo]').val()

		EasyBlog.ajax('admin/views/migrators/migrateArticle',
		{
			"applicationId"	: applicationId,
			"component"		: "com_zoo"
		},
		{
			append: function(selector, message) {
				$(selector).append(message);
			}
		})
		.done(function(result)
		{
			if (result == 'parseFailed') {
				$('[data-migrate-zoo]').removeAttr('disabled');
				$('[data-migrate-zoo]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_PARSE_FAILED', true);?>');
			}

			if (result == 'fileNotExist') {
				$('[data-migrate-zoo]').removeAttr('disabled');
				$('[data-migrate-zoo]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_FILE_DOES_NOT_EXIST', true);?>');
			}

			// If there's still items to render, run a recursive loop until it doesn't have any more items;

			if (result == 'next') {
				window.migrateArticle();
				return;
			}

			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');
			
			if (result == 'success') {
				$('[data-migrate-zoo]').removeAttr('disabled');
				$('[data-migrate-zoo]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_COMPLETED', true);?>');
			}

			if (result == 'noitem'){
				$('[data-migrate-zoo]').removeAttr('disabled');
				$('[data-migrate-zoo]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_NO_ITEM', true);?>');
			}
		});
	}

});
