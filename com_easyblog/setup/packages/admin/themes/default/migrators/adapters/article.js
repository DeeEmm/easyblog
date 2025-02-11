EasyBlog.ready(function($) {

	$('[data-migrate-joomla]').on('click', function() {

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
		var categoryId = $('[data-migrate-article-category]').val(),
			authorId = $('[data-migrate-article-author]').val(),
			state = $('[data-migrate-article-state]').val(),
			ebcategory = $('[data-easyblog-category]').val(),
			myblog = $('[data-myblog]').val(),
			migrateComment = $( "#migrate_jomcomment" ).val();

		EasyBlog.ajax('admin/views/migrators/migrateArticle',
		{
			"component"	: "com_content",
			"authorId"	: authorId,
			"categoryId" : categoryId,
			"state": state,
			"ebcategory":ebcategory,
			"myblog": myblog,
			"migrateComment": migrateComment
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

			$('[data-migrate-joomla]').removeAttr('disabled');
			$('[data-migrate-joomla]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_COMPLETED', true);?>');

			if (hasMore == 'noitem'){
				$('[data-migrate-joomla]').removeAttr('disabled');
				$('[data-migrate-joomla]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_NO_ITEM', true);?>');
			}

		});
	}
});
