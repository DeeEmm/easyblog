
EasyBlog.ready(function($)
{
	$('[data-adsense-appearence]').on('change', function()
	{
		var selected = $(this).val();

		if (selected == 'userspecified') {
			$('[data-adsense-appearence-help]').removeClass('hide');

			return;
		}

		$('[data-adsense-appearence-help]').addClass('hide');
	});
});