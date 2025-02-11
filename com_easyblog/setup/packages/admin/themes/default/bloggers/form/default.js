EasyBlog.ready(function($) {
	
	$.Joomla('submitbutton' , function(task){
		if (task == 'bloggers.cancel') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=bloggers';
			return false;
		}

		$.Joomla( 'submitform', [task] );
	});

	$('#subuser dt span').on('click', function() {
		var id = $(this).attr('id');
		var className = 'user-' + id;

		$('#subuser dt').removeClass('open')
			.addClass('closed');

		$(this).parent().addClass('open');

		$('.tab-details').hide();
		$('.' + className).show();
	});

	var left = (screen.width/2)-( 300 /2);
	var top = (screen.height/2)-( 300 /2);

	$( '#facebook-login' ).bind( 'click' , function(){
		var url = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easyblog&c=oauth&task=request&type=<?php echo EBLOG_OAUTH_FACEBOOK;?>&call=doneLogin&id=<?php echo $user->id; ?>';
		window.open(url, "Facebook login", 'scrollbars=no,resizable=no, width=300,height=300,left=' + left + ',top=' + top );
	});

});

window.doneLogin = function(){
	window.location.href = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easyblog&c=user&id=<?php echo $user->id; ?>&task=edit';
}
