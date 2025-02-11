EasyBlog.ready(function($) {

	var container = $('[data-calendar-container]');
	var loader = $('[data-calendar-loader-template]').detach().html();

	// Append the loader initially
	container.html(loader);

	// When the page initially loads, render the calendar
	EasyBlog.ajax('site/views/calendar/render',{
		"timestamp": "<?php echo $timestamp;?>",
		"category": "<?php echo $category;?>",
		"archives": "<?php echo $archives;?>"
	}).done(function(output) {
		container.html(output);
	});


	$(document).on('click.eb.calendar.next', '[data-calendar-next],[data-calendar-previous]', function() {

		// Append loader
		container.html(loader);

		// Get the timestamp
		var timestamp = $(this).data('timestamp');

		EasyBlog.ajax('site/views/calendar/render', {
			"timestamp": timestamp,
			"category": "<?php echo $category;?>",
			"archives": "<?php echo $archives;?>"
		}).done(function(output, displayDate, newListUrl) {
			$('[data-calendar-container]').html(output);
			$('[data-calendar-topbar-date]').html(displayDate);
			$('[data-calendar-toggle-view]').attr('href', newListUrl);
		});
	});
});
