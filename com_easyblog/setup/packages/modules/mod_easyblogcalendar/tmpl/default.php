<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod mod-easyblogcalendar<?php echo $modules->getWrapperClass();?>">
	<div class="eb-mod-cal" data-calendar-module-container>
	</div>

	<div style="display: none;" data-module-calendar-loader-template>
		<div class="eb-empty eb-calendar-loader" data-calender-loader>
			<i class="fdi fa fa-refresh fa-spin"></i> <span><?php echo JText::_('COM_EASYBLOG_CALENDAR_LOADING_CALENDAR'); ?></span>
		</div>
	</div>
</div>
<script type="text/javascript">
EasyBlog.require()
.done(function($) {
	var loader = $('[data-module-calendar-loader-template]').detach().html();
	var container = $('[data-calendar-module-container]');

	// Append the loader initially
	container.html(loader);

	// When the page initially loads, render the calendar
	EasyBlog.ajax('site/views/calendar/render',{
	}).done(function(output) {
		container.html(output);
	});

	$(document).on('click.eb.calendar.next', '[data-calendar-next],[data-calendar-previous]', function() {

		// Append loader
		container.html(loader);

		// Get the timestamp
		var timestamp = $(this).data('timestamp');

		EasyBlog.ajax('site/views/calendar/render', {
			"timestamp": timestamp
		}).done(function(output) {
			$('[data-calendar-module-container]').html(output);
		});
	});
});
</script>