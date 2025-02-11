EasyBlog.ready(function($) {

	$(document)
		.on('keydown', function(event) {
			var keycode = event.keyCode;

			if (keycode == 121) {
				$('body').toggleClass('is-editor-fullscreen');
			}

			if (keycode == 27 && $('body').find('is-editor-fullscreen')) {
				$('body').removeClass('is-editor-fullscreen');
			}
		});

	jQuery(document)
		.on('change.theme.files', '[data-files-selection]', function() {

			var dropdown = $(this);
			var selected = dropdown.val();

			if (selected == '') {
				return;
			}

			window.location = '<?php echo JURI::base();?>index.php?option=com_easyblog&view=themes&layout=editor&element=<?php echo $element;?>&id=' + selected;
		});

	$.Joomla('submitbutton', function(task) {

		if (task == 'cancel') {
			window.location = '<?php echo JURI::base();?>index.php?option=com_easyblog&view=themes';
			return;
		}

		if (task == 'revert') {
			EasyBlog.dialog({
				"content": EasyBlog.ajax('admin/views/themes/confirmRevert', {"id": "<?php echo $id;?>", "element" : "<?php echo $element;?>"})
			});

			return;
		}

		$.Joomla('submitform', [task]);
	});
});
