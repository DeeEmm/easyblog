
<?php if ($this->acl->get('custom_css')) { ?>
EasyBlog.require().library("ace")
.done(function($){
    var editor = ace.edit("customcss");
    editor.setTheme("ace/theme/github");
    editor.getSession().setMode("ace/mode/css");


    $('#customcss').height('400px');
    editor.resize();

    editor.getSession().on('change', function(){
    	$('[data-custom-css]').val(editor.getSession().getValue());
    });

});

<?php } ?>
