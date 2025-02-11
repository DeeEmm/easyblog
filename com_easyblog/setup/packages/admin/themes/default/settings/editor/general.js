EasyBlog.ready(function($) {

	// Editor
	$('select[name=layout_editor]').on('change', function() {
		var selected = $(this).val();

		if (selected == 'composer') {
			$('[data-panel-composer]').removeClass('hide');
			return;
		}
		$('[data-panel-composer]').addClass('hide');
	});

	$('[data-enforce-fontstyle]').on('change', function() {
		var selected = $(this).val() == "1";

		if (selected) {
			$('[data-default-fontsize]').removeClass('hide');
			$('[data-default-fontfamily]').removeClass('hide');
			return;
		}
		$('[data-default-fontsize]').addClass('hide');
		$('[data-default-fontfamily]').addClass('hide');
	});

	// Changing comment settings
	$('[data-comment-option]').on('change', function() {
		var selected = $(this).val() == "1";

		if (selected) {
			$('[data-comment-option-default]').removeClass('hide');
			return;
		}

		$('[data-comment-option-default]').addClass('hide');
	});

	$('#layout_composer_history').on('click', function() {
		var checked = $(this).is(':checked');

		if (checked) {
			$('[data-revision-limit]').removeClass('hidden');
			$('[data-revision-limit-max]').removeClass('hidden');
		} else {
			$('[data-revision-limit]').addClass('hidden');
			$('[data-revision-limit-max]').addClass('hidden');
		}
	});

	$('[data-content-type-dropdown]').on('change', function() {
		var type = $(this).val();
		var inputs = $('[data-content-type]').addClass('hide');

		var input = $('[data-content-type="' + type + '"]').removeClass('hide');
	});
});
