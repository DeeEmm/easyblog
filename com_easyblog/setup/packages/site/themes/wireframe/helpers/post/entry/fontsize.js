EasyBlog.ready(function($) {

	// Bind event's on the font size changer.
	$('[data-font-resize]').on('click', function() {

		// General font size
		var content = $('[data-blog-content]'),
			current = content.css('font-size'),
			num = parseFloat(current, 10),
			unit = current.slice(-2),
			operation = $(this).data('operation');

		// <p> tag size
		var pTag = content.find('p'),
			pNum = parseFloat(pTag.css('font-size'), 10);

		// <span> size
		var spanTag = content.find('span'),
			spanNum = parseFloat(spanTag.css('font-size'), 10);

		// <h> header tag
		var header = content.find(':header'),
			headerNum = parseFloat(header.css('font-size'), 10);

		// block link description text
		var linksBlock = $('[data-type="links"]'),
			linksDesc = linksBlock.find('.media-content');

		if (operation == 'increase') {
			num = num * 1.4;
			pNum = pNum * 1.4;
			spanNum = spanNum * 1.4;
			headerNum = headerNum * 1.4;
		}

		if (operation == 'decrease') {
			num = num / 1.4;
			pNum = pNum / 1.4;
			spanNum = spanNum / 1.4;
			headerNum = headerNum / 1.4;
		}

		content.css('font-size', num + unit);
		pTag.css('font-size', pNum + unit);
		spanTag.css('font-size', spanNum + unit);
		header.css('font-size', headerNum + unit);
		linksDesc.css('font-size', num + unit);
	});
});
