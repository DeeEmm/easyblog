EasyBlog.ready(function($) {

$('[data-remove-button]').on('click', function() {

	EasyBlog.dialog({
		content: EasyBlog.ajax('admin/views/dialogs/render', {'file': 'admin/settings/dialog.remove.watermark'}),
		bindings: {
			'{restoreButton} click': function() {

				EasyBlog.ajax('admin/controllers/settings/restoreLogo', {
					'type': 'watermark'
				})
				.done(function() {
					$('[data-watermark-placeholder]').addClass('t-d--none');
					EasyBlog.dialog().close();
				});
			}
		}
	});
});
});
