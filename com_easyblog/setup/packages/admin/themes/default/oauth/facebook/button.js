EasyBlog.ready(function($) {

    $('[data-facebook-login-<?php echo $uid;?>]').on('click', function() {

        // Width for the popup
        var width = 300;
        var height = 300;

        // Get the top and left
        var top = (screen.height / 2) - (height / 2);
        var left = (screen.width / 2) - (width / 2);
        var url = '<?php echo $url;?>';

        // window.open(url, '', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
		window.location = url;
    });

    window.doneLogin = function() {
        window.location.href = '<?php echo $return;?>';
    }
});
