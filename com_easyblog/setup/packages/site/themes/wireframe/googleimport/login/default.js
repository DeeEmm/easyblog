EasyBlog.ready(function($) {
	$('[data-eb-composer-oauth-google]').on('click', function() {
		var left = (screen.width/2)-( 600 /2);
		var top = (screen.height/2)-( 500 /2);

		var url = '<?php echo $url;?>';

		window.open(url, '', 'width=600,height=500,left=' + left + ',top=' + top);
	});

	window.doneLogin = function(){
		console.log('window.doneLogin');
		window.location.href = "<?php echo $returnUrl; ?>";
	}
});