
EasyBlog.ready(function($){

	$.Joomla( 'submitbutton' , function(task){

		if (task == 'teamblogs.cancel') {
			window.location     = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=teamblogs';
			return false;
		}

		$.Joomla( 'submitform', [task] );
	});


	window.insertGroup = function(id, name) {
		var elementId	= 'member-' + id;

		if( $('#' + elementId).html() == null) {
			$('#groups-container').append('<span id="' + elementId + '" class="group-item"><a class="remove_item" href="javascript:void(0);" onclick="removeGroup(\'' + elementId + '\');">X</a><input type="hidden" name="groups[]" value="' + id + '" /><span class="normal-member">' + name + '</span></span>');
			$.Joomla("squeezebox").close();
		}
		else
		{
			alert('User is already added');
		}
	}

	window.removeGroup = function( elementId, groupId )
	{
		$('#'+elementId).remove();

		if($('#deletegroups').val() == '')
		{
			$('#deletegroups').val( groupId );
		}
		else
		{
			var groups = $('#deletegroups').val();
			$('#deletegroups').val( groups  + ',' + groupId );
		}
	}

	window.submitbutton = function( action )
	{
		if ( typeof( tinyMCE ) == 'object' ) {
			if ( $('#write_description').is(":visible") ) {
				tinyMCE.execCommand('mceToggleEditor', false, 'write_description');
			}
		}

		submitform( action );
	}

});
