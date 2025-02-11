EasyBlog.require()
.script("shared/foundry")
.done(function($){

    $('[data-oauth-signup]').on('click', function(){

        var client = $(this).data('client');
        var url = '<?php echo rtrim(JURI::root(), '/');?>/index.php?option=com_easyblog&task=oauth.request&client=' + client;

        var width = 450;
        var height = 450;

        if (client == 'twitter' || client == 'linkedin') {
            width = 500;
            height = 700;
        }

        var left = (screen.width / 2) - (width / 2);
        var top = (screen.height / 2) - (height / 2);

        // window.open(url, '', 'scrollbars=no,resizable=no, width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
		window.location = url;
    });
});

window.doneLogin = function(){
	window.location.href = '<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=autoposting', false );?>';
}
