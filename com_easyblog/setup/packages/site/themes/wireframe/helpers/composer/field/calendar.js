EasyBlog
.require()
.library(
	'datetimepicker',
	'moment/<?php echo $calendarLocale;?>'
)
.script('shared/datetime')
.done(function($) {

var wrapper = $('[data-calendar-wrapper=<?php echo $hash;?>]');


var addController = function(wrapper) { 
	wrapper.addController('EasyBlog.Controller.Post.Datetime', {
		format: "<?php echo JText::_($calendarFormat); ?>",
		emptyText: "<?php echo JText::_($calendarEmptyText); ?>",
		language: "<?php echo $calendarLocale;?>",
		currentDate: "<?php echo EB::date()->toSql(); ?>",
		errorMessage: "<?php echo JText::_('COM_EB_CALENDAR_FIELD_ERROR_MESSAGE'); ?>",
		incompleteMessage: "<?php echo JText::_('COM_EB_CALENDAR_FIELD_INCOMPLETE_MESSAGE'); ?>"
	});
};

addController(wrapper);

});