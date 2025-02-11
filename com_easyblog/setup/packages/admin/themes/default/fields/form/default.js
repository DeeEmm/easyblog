
EasyBlog.require()
.done(function($) {
	$.Joomla('submitbutton', function(task) {
		if (task == 'fields.cancel') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=fields';
			return false;
		}

		$.Joomla('submitform', [task]);
	});


	$('[data-field-type]').on('change', function() {
		var selected = $(this).val();

		if (selected != '') {

			EasyBlog.ajax('admin/views/fields/getForm', {
				"id": "<?php echo $field->id;?>",
				"type": selected
			}).done(function(output){

				$('[data-field-form]').html(output);
			});

			return;
		}
	})
});