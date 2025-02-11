EasyBlog.ready(function($){

	$(document).on('click.upload', '[data-eb-upload-button]', function() {
		$('[data-eb-avatar-input]').click();
	});
	$(document).on('change', '[data-eb-avatar-input]', function() {
		var file = $('[data-eb-avatar-input]')[0].files[0];
		if (file){

			var tmp = file.name.split('.');
			var filename = file.name;
			var ext = tmp[tmp.length-1];

			if (filename.length > 28) {
				// manually shorten the filename
				filename =filename.replace('.' + ext, '');

				var firstPart = filename.substr(0, 10);
				var lastPart = filename.substr(-5);
				filename = firstPart + '...' + lastPart + '.' + ext;
			}

			$('[data-eb-avatar-filename]')
				.removeClass('t-hidden')
				.html(filename);
		}
	});
});
