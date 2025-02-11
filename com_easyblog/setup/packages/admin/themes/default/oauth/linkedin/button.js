EasyBlog.ready(function($) {

	$('[data-linkedin-login-<?php echo $uid;?>]').on('click', function(){
		var left = (screen.width / 2) - (447 / 2);
		var top = (screen.height / 2) - (660 / 2);
		var url = '<?php echo $url;?>';

		// window.open(url, '', 'width=447,height=660,left=' + left + ',top=' + top);
		window.location = url;
	});

	window.doneLogin = function(){
		window.location.href = '<?php echo $return;?>';
	}
});
