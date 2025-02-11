EasyBlog.ready(function($) {

	var displayForm = function(id) {
		var pollId = id === undefined ? 0 : id;
		var isSaving = false;

		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/polls/form', {
				"pollId": pollId
			}),
			"bindings": {
				'{saveButton} click': function(el, event) {
					if (isSaving) {
						return;
					}

					isSaving = true;

					el = $(el);

					var footer = el.closest('.eb-dialog-footer');
					var modal = footer.closest('.eb-dialog-modal');
					var content = modal.find('.eb-dialog-content');
					var form = content.find('[data-eb-poll-form]');
					var errorWrapper = content.find('[data-poll-error]');
					var postData = {};
					var title = form.find('[data-eb-poll-form-title]').val();
					var multiple = form.find('[data-eb-poll-form-multiple]').find('input[type="hidden"]').val();
					var unvote = form.find('[data-eb-poll-form-unvote]').find('input[type="hidden"]').val();
					var expiry_date = form.find('[data-eb-poll-form-expiration]').find('[data-datetime]').val();
					var itemsWrapper = form.find('[data-eb-poll-form-item]');

					// Format items into an array with objects
					var items = [];

					$.each(itemsWrapper, function(index, item) {
						var _el = $(item);
						var itemId = _el.data('id');
						var value = _el.find('input').val();

						var obj = {
							'id': itemId ? itemId : 0,
							'content': value
						};

						items.push(obj);
					});

					postData.title = title;
					postData.items = items;
					postData.multiple = parseInt(multiple);
					postData.unvote = parseInt(unvote);
					postData.expiry_date = expiry_date;

					// Always hide the error
					errorWrapper.addClass('t-hidden');

					// Display the loader on the button
					el.addClass('is-loading');

					EasyBlog.ajax('site/views/polls/save', {
						'formOption': 'savePoll',
						'pollId': pollId,
						'postData': JSON.stringify(postData),
						'reloadAfterSave': 1
					}).done(function(poll) {

					}).fail(function(msg) {
						errorWrapper.find('[data-fd-alert-message]').html(msg);

						// Display the error
						errorWrapper.removeClass('t-hidden');
					}).always(function() {
						// Remove the loader
						el.removeClass('is-loading');

						isSaving = false;
					});
				}
			}
		});
	};

	$('[data-eb-poll-item]').on('click', function(el) {
		var id = $(this).data('id');

		displayForm(id);
	});

	$.Joomla("submitbutton", function(task) {
		if (task == 'polls.create') {
			displayForm();

			return false;
		}

		$.Joomla('submitform', [task]);
	});
});
