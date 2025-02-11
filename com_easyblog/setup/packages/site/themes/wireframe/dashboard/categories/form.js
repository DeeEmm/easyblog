
EasyBlog.ready(function($) {

    $('#private').on('change', function() {
        var val = $(this).val(),
            el = $('[data-category-access]');

        if (val == 2) {
            $(el).removeClass('hide');
        } else {
            $(el).addClass('hide');
        }
    });

    $('#category_acl_type').on('change', function() {
		var val = $(this).val(),
			el2 = $('[data-category-acl-select]'),
			el3 = $('[data-category-acl-specific]');

		if (val == '2') {
			$(el2).removeClass('hide');
			$(el3).addClass('hide');
		} else {
			$(el2).addClass('hide');
			$(el3).removeClass('hide');
		}
	});

});
