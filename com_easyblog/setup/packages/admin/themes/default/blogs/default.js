
EasyBlog.require()
.library('moment', 'daterangepicker')
.done(function($) {
	var moment = $.moment;

	$('[data-date-range]').daterangepicker({
		<?php if ($startDate) { ?>
		"startDate": "<?php echo $startDate;?>"
		<?php } ?>

		<?php if ($endDate) { ?>
		,"endDate": "<?php echo $endDate;?>"
		<?php } ?>
	});

	$('[data-date-range]').on('apply.daterangepicker', function(event, picker) {
		var start = picker.startDate.format('DD-MM-YYYY');
		var end = picker.endDate.format('DD-MM-YYYY');

		$('[data-date-start]').val(start);
		$('[data-date-end]').val(end);

		submitform();
	});

	// Override submit form for dropdown filter
	submitFilterForm = function(e) {
		var val = $('[data-eb-filter-state]').find(":selected").val();
		var dateForm = $('[data-eb-date-form]');

		// Always hide the date form first
		dateForm.addClass('hide');

		// toggle date input
		if (val === 'date') {
			dateForm.removeClass('hide');
			return;
		}

		// Reset date value
		datePicker.each(function() {
			$(this).data("DateTimePicker").setDate(null);
		});		

		submitform();
	}

	// Reset the date filter
	$('[data-filter-reset-date]').on('click', function() {

		// Reset the date
		datePicker.each(function() {
			$(this).data("DateTimePicker").setDate(null);
		});
	});

	// Auto posting
	$('[data-post-autopost]').on('click', function() {

		var button = $(this);
		var id = button.data('id');
		var type = button.data('type');

		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/blogs/confirmAutopost', {"type":type, "id" : id})
		});
	});

	<?php if ($browse) { ?>
	$('[data-post-title]').on('click', function(){
		var item = $(this).parents('[data-item]'),
			title = item.data('title'),
			id = item.data('id');

		parent.<?php echo $browseFunction;?>(id, title);
	});
	<?php } ?>

	$.Joomla("submitbutton", function(action) {

		// Empty Trash
		if (action == 'blogs.empty') {
			EasyBlog.dialog({
				"content": EasyBlog.ajax('admin/views/blogs/emptyTrash')
			});
			
			return false;
		}

		if (action == 'blogs.create') {
			window.location = '<?php echo EB::composer()->getComposeUrl(); ?>';
			return false;
		}

		if (action != 'remove' || confirm('<?php echo JText::_('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
			$.Joomla("submitform", [action]);
		}
	});

	$('[data-notify-item]').on('click', function() {
		var id = $(this).data('blog-id');

		EasyBlog.dialog({
			"content": EasyBlog.ajax('admin/views/blogs/confirmNotify', {"id" : id})
		});
	});

});
