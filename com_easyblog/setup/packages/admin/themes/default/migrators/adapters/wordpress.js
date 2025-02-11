EasyBlog.ready(function($) {

	$('[data-migrate-xmlwp]').on('click', function() {

		var file = $('[data-xml-wordpress]').val();

		if (file === null) {
			EasyBlog.dialog({
				"content": 'Please select xml file to proceed.',
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
		var xmlFile = $('[data-xml-wordpress]').val();
		var authorId = $('[data-author-id]').val();
		var current = $('[data-migrate-current]').val();

		var firstimagecover = $('[data-firstimage-cover]').val();
		var wpimagepathfrom = $('[data-wpimagepath-from]').val();
		var wpimagepathto = $('[data-wpimagepath-to]').val();
		var wraptext = $('[data-paragraph-wrap]').val();

		EasyBlog.ajax('admin/views/migrators/migrateArticle', {
			"authorId": authorId,
			"firstimagecover": firstimagecover,
			"wpimagepathfrom": wpimagepathfrom,
			"wpimagepathto": wpimagepathto,
			"wraptext": wraptext,
			"current": current,
			"xmlFile" : xmlFile,
			"component"	: "xml_wordpress"
		}, {
			append: function(selector, message) {
				$(selector).append(message);
			}
		}).done(function(result, current, hasMore) {

			if (result == 'continueProcessing' && hasMore) {
				$('[data-migrate-current]').val(current);

				// Run the migration again
				return window.migrateArticle();
			}

			if (!hasMore) {

				$('[data-progress-status]').append('<div>Migration completed</div>');

				// revert the button.
				$('[data-migrate-xmlwp]').removeAttr('disabled');
				$('[data-migrate-xmlwp]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_COMPLETED', true);?>');

				return false;
			}

			if (result == 'parseFailed') {
				$('[data-migrate-xmlwp]').removeAttr('disabled');
				$('[data-migrate-xmlwp]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_PARSE_FAILED', true);?>');
			}

			if (result == 'fileNotExist') {
				$('[data-migrate-xmlwp]').removeAttr('disabled');
				$('[data-migrate-xmlwp]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_FILE_DOES_NOT_EXIST', true);?>');
			}

			// Remove loading icon.
			$('[data-progress-loading]').addClass('hide');

			if (result == 'success') {
				$('[data-migrate-xmlwp]').removeAttr('disabled');
				$('[data-migrate-xmlwp]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_COMPLETED', true);?>');
			}

			if (result == 'noitem'){
				$('[data-migrate-xmlwp]').removeAttr('disabled');
				$('[data-migrate-xmlwp]').html('<?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYBLOG_NO_ITEM', true);?>');
			}
		});
	}

	window.divSrolltoBottomWordPressXML = function() {

		var objDiv = document.getElementById("progress-status6");
		objDiv.scrollTop = objDiv.scrollHeight;

		var objDiv2 = document.getElementById("stat-status6");
		objDiv2.scrollTop = objDiv2.scrollHeight;
	}

});
