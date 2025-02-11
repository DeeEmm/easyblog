EasyBlog.ready(function($) {

	$('[data-tnc-article]').on('change', function() {
		var enabled = $(this).val() == 1;
		var textArea = $('[data-tnc-text]');
		var articleSelection = $('[data-tnc-article-selection]');

		if (enabled) {
			textArea.addClass('hidden');
			articleSelection.removeClass('hidden');
			return;
		}

		textArea.removeClass('hidden');
		articleSelection.addClass('hidden');
		return;
	});
});