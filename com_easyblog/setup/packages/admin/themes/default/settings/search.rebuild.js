jQuery(document).ready(function($) {

var pages = <?php echo json_encode($items); ?>;
var result = {
	'name': 'settings',
	'description': 'Settings database for search',
	'items': []
};

var requests = [];

$(pages).each(function(n, page) {

	requests.push($.ajax({
		"type": "GET",
		"url": '<?php echo JURI::root();?>/administrator/index.php?option=com_easyblog&view=settings&layout=' + page,
		"success": function(html) {
			d = html.replace(/(<\/?)html( .+?)?>/gi,'$1NOTHTML$2>',html)
			d = d.replace(/(<\/?)body( .+?)?>/gi,'$1NOTBODY$2>',d)

			// select the `notbody` tag and log for testing
			var y = $(d).find('notbody').html();

			y = $(y);

			var tabs = y.find('[data-tab-content]');

			tabs.each(function(i, tab) {
				var tab = $(tab);
				var tabId = tab.attr('id');
				var items = tab.find('.panel > .panel-body > .form-group');

				items.each(function(x, item) {
					var label = $(item).find('label');
					var labelText = $.trim(label.text());
					var labelUid = label.data('uid');

					if (!labelUid) {
						// console.log($(item).html());
						console.log('Label Error', 'Page: ' + page, 'Tab: ' + tab.attr('id'), 'Label: ' + $(item).find('label').html());

						alert('Error');
						return;
					}

					// If there are no labels, we should just skip this
					if (!labelText) {
						// console.log($(item).html());
						console.log('Error processing label', 'Tab: ' + tab.attr('id'), item);

						alert('Error');
						return;
					}

					var desc = $(item).find('i').data('content');

					var data = {
						"id": labelUid,
						"page": page,
						"tab": tabId,
						"label": labelText,
						"description": desc
					};

					result.items.push(data);
				});

			});
		}
	}));
});

$.when.apply(null, requests).done(function() {
	var resultString = JSON.stringify(result);

	// Finalize and send the data to the server
	EasyBlog.ajax('admin/views/settings/rebuildSearch', {
		"dataString": resultString
	}).done(function() {
		window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog';
	});
});


});
