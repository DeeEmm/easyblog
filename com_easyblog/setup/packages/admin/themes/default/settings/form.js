EasyBlog.ready(function($) {

	$.Joomla("submitbutton", function(task) {

		if (task == 'export') {
			window.location.href = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=settings&format=raw&layout=export&tmpl=component';
			return;
		}

		if (task == 'import') {
			EasyBlog.dialog({
				"content": EasyBlog.ajax('admin/views/settings/import')
			});

			return;
		}

		$.Joomla("submitform", [task]);
	});

	window.switchFBPosition = function() {
		if( $('#main_facebook_like_position').val() == '1' )
		{
			$('#fb-likes-standard').hide();
			if( $('#standard').attr('checked') == true)
				$('#button_count').attr('checked', true);
		}
		else
		{
			$('#fb-likes-standard').show();
		}
	}

	<?php if ($activeTab) { ?>
		$('[data-form-tabs][href=#<?php echo $activeTab;?>]')
			.click();
	<?php } ?>

	// Append the settings search to the toolbar
	jQuery(document).on('fd.easyblog.search.settings', function(e, search, popup) {

		EasyBlog.ajax('admin/views/settings/search', {
			'text': search
		}).done(function(output) {
			popup.html(output).removeClass('t-hidden');
		});
	});


	<?php if ($goto) { ?>
	var element = $('#<?php echo $goto;?>');
	var wrapper = element.parents('.form-group');

	wrapper.css({
		'background': '#fff9c4',
		'transition': 'background 1.0s ease-in-out'
	});

	var resetBackground = function() {
		wrapper.css({
			'background': 'none'
		});
	};

	setInterval(function() {
		resetBackground();
	}, 5000);

	setTimeout(() => {
		element[0].scrollIntoView({
			behavior: 'smooth',
			block: 'center'
		});
	}, 1000);

	<?php } ?>
});
