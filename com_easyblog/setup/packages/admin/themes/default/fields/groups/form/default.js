EasyBlog.ready(function($) {

	$.Joomla('submitbutton', function(task) {
		if (task == 'fields.cancelGroup') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=fields&layout=groups';
			return false;
		}

		//let clear the warning 1st.
		$("input[name='title']").closest('div.form-group').removeClass('has-error');
		$('[data-title-error]').addClass('hide');

		//simple validation here.
		if ($("input[name='title']").val() == '') {
			$("input[name='title']").closest('div.form-group').addClass('has-error');
			$('[data-title-error]').removeClass('hide');
			return;
		}

		$.Joomla('submitform', [task]);
	});
});
