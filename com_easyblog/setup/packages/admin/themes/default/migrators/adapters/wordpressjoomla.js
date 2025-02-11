EasyBlog.ready(function($){

	$('[data-migrate-joomlawp]').on('click', function() {
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

		// process the migration
		window.migrateArticle();

	});

	window.migrateArticle = function() {

		// Get the values from the form

		EasyBlog.ajax('admin/views/migrators/migrateArticle',
		{
			"blogId" : $('[data-blogid-wordpress]').val(),
			"component"	: "com_wordpress"
		},
		{
			append: function(selector, message) {
				$(selector).append(message);
			}
		})
		.done(function(hasMore)
		{
			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');
			$('[data-migrate-joomlawp]').removeAttr('disabled');
			$('[data-migrate-joomlawp]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_COMPLETED', true);?>');
		
			if (hasMore == 'noitem'){
				$('[data-progress-loading]').addClass('hide');
				$('[data-migrate-joomlawp]').removeAttr('disabled');
				$('[data-migrate-joomlawp]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_NO_ITEM', true);?>');
			}
		});
	}


	window.divSrolltoBottomWordPress = function()
	{
		var objDiv = document.getElementById("progress-status5");
	    objDiv.scrollTop = objDiv.scrollHeight;

		var objDiv2 = document.getElementById("stat-status5");
	    objDiv2.scrollTop = objDiv2.scrollHeight;
	}

});

