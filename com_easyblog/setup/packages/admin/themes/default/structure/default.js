EasyBlog.ready(function($) {

	$(document).on('click.close.button', '[data-eb-close-toolbarnotice]', function(event) {
		EasyBlog.ajax('admin/controllers/system/dismissToolbarNotice');

		$('[data-eb-toolbar-notice]').addClass('t-hidden');
	});

	$('body').addClass('com_easyblog si-theme--light');

	<?php if ($prefix) { ?>
	$('html').addClass('<?php echo $prefix;?>');
	<?php } ?>

	<?php if (FH::isJoomla4()) { ?>
	$('[data-fd-structure]')
		.removeClass('is-loading')
		.addClass('is-done-loading');

	$('[data-fd-body]').removeClass('t-hidden');
	<?php } ?>
});
