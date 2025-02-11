EasyBlog.require()
.script('site/dashboard/table', 'site/dashboard/filters').done(function($) {
	$('[data-eb-dashboard-polls]').implement(EasyBlog.Controller.Dashboard.Filters);
	$('[data-eb-dashboard-polls]').implement(EasyBlog.Controller.Dashboard.Table, {'inputName': 'cid'});

	$('[data-dashboard-poll-view-result]').on('click', function() {
		var wrapper = $(this).closest('[data-dashboard-poll-item]');
		var pollId = wrapper.data('id');

		EasyBlog.dialog({
				"content": EasyBlog.ajax('site/views/polls/getResult', {
					"pollId": pollId
				})
			});
	});

	<?php if ($this->acl->get('polls_manage')) { ?>
	$('[data-dashboard-poll-delete]').on('click', function() {
		var wrapper = $(this).closest('[data-dashboard-poll-item]');
		var pollId = wrapper.data('id');

		EasyBlog.dialog({
				"content": EasyBlog.ajax('site/views/polls/confirmDelete', {
					"pollId": pollId
				})
			});
	});
	<?php } ?>

	var displayForm = function(wrapper) {
		var pollId = wrapper.data('id') === undefined ? 0 : wrapper.data('id');
		var isSaving = false;

		EasyBlog.dialog({
				"content": EasyBlog.ajax('site/views/polls/form', {
					"pollId": pollId
				}),
				"bindings": {
					'{saveButton} click': function(el, event) {
						if (isSaving) {
							return;
						}

						isSaving = true;

						var footer = $(el).closest('.eb-dialog-footer');
						var modal = footer.closest('.eb-dialog-modal');
						var content = modal.find('.eb-dialog-content');
						var form = content.find('[data-eb-poll-form]');
						var title = form.find('[data-eb-poll-form-title]').val();
						var multiple = form.find('[data-eb-poll-form-multiple]').find('input[type="hidden"]').val();
						var unvote = form.find('[data-eb-poll-form-unvote]').find('input[type="hidden"]').val();
						var expiry_date = form.find('[data-eb-poll-form-expiration]').find('[data-datetime]').val();
						var itemsWrapper = form.find('[data-eb-poll-form-item]');
						var errorWrapper = content.find('[data-poll-error]');

						// Always hide the error
						errorWrapper.addClass('t-hidden');

						// Format items into an array with objects
						var items = [];

						$.each(itemsWrapper, function(index, item) {
							var el = $(item);
							var itemId = el.data('id');
							var value = el.find('input').val();

							var obj = {
								'id': itemId ? itemId : 0,
								'content': value
							};

							items.push(obj);
						});

						postData = {};
						postData.title = title;
						postData.items = items;
						postData.multiple = parseInt(multiple);
						postData.unvote = parseInt(unvote);
						postData.expiry_date = expiry_date;

						// Display the loader on the button
						$(el).addClass('is-loading');

						EasyBlog.ajax('site/views/polls/save', {
							'formOption': 'savePoll',
							'pollId': pollId,
							'postData': JSON.stringify(postData),
							'reloadAfterSave': 1
						}).done(function() {

						}).fail(function(msg) {
							errorWrapper.find('[data-fd-alert-message]').html(msg);

							// Display the error
							errorWrapper.removeClass('t-hidden');
						}).always(function() {
							// Remove the loader
							$(el).removeClass('is-loading');

							isSaving = false;
						});
					}
				}
			});
	};

	<?php if ($this->acl->get('polls_edit')) { ?>
	$('[data-dashboard-poll-title]').on('click', function() {
		var wrapper = $(this).closest('[data-dashboard-poll-item]');
		displayForm(wrapper);
	});
	<?php } ?>

	<?php if ($this->acl->get('polls_create')) { ?>
	$('[data-dashboard-poll-compose]').on('click', function() {
		displayForm($(this));
	});
	<?php } ?>
});