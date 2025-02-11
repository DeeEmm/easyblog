EasyBlog.ready(function($) {

	$('[data-custom-email]').on('change', function() {
		var enabled = $(this).val() == 1;

		if (enabled) {
			$('[data-custom-email-input]').removeClass('hide');
			return;
		}

		$('[data-custom-email-input]').addClass('hide');
	});

	$('[data-custom-email-logo]').on('change', function() {
		var enabled = $(this).val() == 1;

		if (enabled) {
			$('[data-email-logo-wrapper]').removeClass('hide');
			return;
		}

		$('[data-email-logo-wrapper]').addClass('hide');
	});

	$('[data-cron-secure]').on('change', function() {
		var enabled = $(this).val() == 1;

		if (enabled) {
			$('[data-cron-secure-key]').removeClass('hide');
			return;
		}

		$('[data-cron-secure-key]').addClass('hide');
	});

	$('[data-email-logo-restore-default-button]').on('click', function() {

		var wrapper = $(this).parents('[data-email-logo]');
		var imageWrapper = $('[data-email-logo-image]');

		EasyBlog.dialog({
			content: EasyBlog.ajax('admin/views/settings/confirmRestorelogos'),
			bindings: {
				'{restoreButton} click': function() {

					EasyBlog.ajax('admin/controllers/settings/restoreLogo', {'type': 'email'}).done(function() {
						var buttonArea = wrapper.find('[data-email-logo-restore-default-wrap]');
						var defaultThumbnail = wrapper.data('defaultEmailLogo');

						buttonArea.hide();
						imageWrapper.attr('src', defaultThumbnail);

						EasyBlog.dialog().close();
					});
				}
			}
		});
	});
});