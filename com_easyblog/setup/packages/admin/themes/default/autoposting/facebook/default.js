EasyBlog.require()
.done(function($) {

	$('#integrations_facebook_introtext_message').on('click', function() {
		var checked = $(this).is(':checked');

		if (checked) {
			$('[data-oauth-contentSource]').removeClass('hidden');
			$('[data-oauth-contentLength]').removeClass('hidden');
		} else {
			$('[data-oauth-contentSource]').addClass('hidden');
			$('[data-oauth-contentLength]').addClass('hidden');
		}
	});
});
