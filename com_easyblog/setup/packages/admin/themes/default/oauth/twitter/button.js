EasyBlog.ready(function($) {

    $('[data-twitter-login-<?php echo $uid;?>]').on('click', function() {
        var left = (screen.width/2)-( 600 /2);
        var top = (screen.height/2)-( 500 /2);
        var url = '<?php echo $url;?>';

        // window.open(url, '', 'width=600,height=500,left=' + left + ',top=' + top);
		window.location = url;
    });

    window.doneLogin = function(){
        window.location.href = '<?php echo $return;?>';
    }
});
