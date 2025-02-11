EasyBlog.ready(function($) {
	var selector = '[data-eb-input-value-<?php echo $idx; ?>]';
	var wrapper = $(selector);

	var select = wrapper.find('[data-eb-input-select]');
	var input = wrapper.find('[data-eb-input-value]');

	select.on('change', function() {
		var val = $(this).val();

		// Show text input
		if (val == 'custom') {
			input.removeClass('hide');
			input.val("0");
		} else {
			input.addClass('hide');
			input.val("-1");
		}
	});
});