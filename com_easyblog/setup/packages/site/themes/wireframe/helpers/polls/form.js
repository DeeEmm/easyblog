EasyBlog.ready(function($) {
	var addNewItem = function (el) {
		var form = el.closest('[data-eb-poll-form]');
		var last = form.find('[data-eb-poll-form-item]').last();
		var wrapper = form.find('[form-horizontal]');
		var cloned = last.clone();

		// Reset the newly item
		cloned.attr('data-id', '');
		cloned.find('input').val('');

		// Add the new item into the list
		cloned.insertAfter(last).find('input').focus();
	};

	$('[data-eb-poll-form-add-button]').on('click', function() {
		addNewItem($(this));
	});

	$(document).off('keydown').on('keydown', '[data-eb-poll-form-item] input', function(event) {
		var isEnterKey = event.keyCode == 13 ? true : false;

		if (!isEnterKey) {
			return;
		}

		addNewItem($(this));
	});

	var selectPollOption = $('[data-eb-poll-form-select]');
	var createPollOption = $('[data-eb-poll-form-create]');

	var selectPollSection = $('[data-eb-poll-form-select-section]');
	var pollContentSection = $('[data-eb-poll-form-content-section]');
	var pollSettingsSection = $('[data-eb-poll-form-settings-section]');

	selectPollOption.on('click', function() {
		selectPollSection.removeClass('t-hidden');
		pollContentSection.addClass('t-hidden');
		pollSettingsSection.addClass('t-hidden');
	});

	createPollOption.on('click', function() {
		selectPollSection.addClass('t-hidden');
		pollContentSection.removeClass('t-hidden');
		pollSettingsSection.removeClass('t-hidden');
	});

	<?php if ($isComposer) { ?>
	$('[data-unassociated-post-polls-list]').on('change', function() {
		var el = $(this);
		var value = el.val();
		
	});

	<?php } ?>

	$(document).on('click', '[data-eb-poll-form-remove-button]', function() {
		var el = $(this);
		var form = el.closest('[data-eb-poll-form]');
		var items = form.find('[data-eb-poll-form-item]');

		if (items.length < 2) {
			return;
		}

		var item = el.closest('[data-eb-poll-form-item]');

		// Remove the selected item
		item.remove();
	});
});