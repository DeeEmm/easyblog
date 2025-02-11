
EasyBlog.ready(function($){

	$('[data-avatars-author]').on('change', function() {
		var enabled = $(this).val() == "1";

		if (enabled) {
			$('[data-avatars-author-settings]').removeClass('hide');
			return;
		}

		$('[data-avatars-author-settings]').addClass('hide');
	});

	var avatarSource = $('[data-avatar-source]');

	if (avatarSource.val() == 'phpbb') {
		$('[data-phpbb-path]').removeClass('hidden');
	}

	avatarSource.on('change', function(){
		var source = $(this).val();

		if (source == 'phpbb') {
			$('[data-phpbb-path]').removeClass('hidden');

			return;
		}

		$('[data-phpbb-path]').addClass('hidden');
	});
	
	// Pagination settings
	$('[data-list-length-inherit]').on('change', function() {
		var checkbox = $(this);
		var checked = checkbox.is(':checked');
		var paginationWrapper = checkbox.parents('[data-list-length-wrapper]').siblings('[data-list-length-input]');
		var paginationInput = paginationWrapper.find('input');
		var total = paginationInput.val();

		if (checked) {

			if (total == '') {
				paginationInput.val('0');
			}

			paginationWrapper.addClass('hide');
			return;
		}

		if (total == 0) {
			paginationInput.val('20');
		}

		paginationWrapper.removeClass('hide');
	});

	$('[data-schema-logo-restore-default-button]').on('click', function() {

		var wrapper = $(this).parents('[data-schema-logo]');
		var imageWrapper = $('[data-schema-logo-image]');

		EasyBlog.dialog({
			content: EasyBlog.ajax('admin/views/settings/confirmRestorelogos'),
			bindings: {
				'{restoreButton} click': function() {

					EasyBlog.ajax('admin/controllers/settings/restoreLogo', {'type': 'schema'}).done(function() {
						var buttonArea = wrapper.find('[data-schema-logo-restore-default-wrap]');
						var defaultThumbnail = wrapper.data('defaultSchemaLogo');

						buttonArea.hide();
						imageWrapper.attr('src', defaultThumbnail);

						EasyBlog.dialog().close();
					});
				}
			}
		});
	});
});