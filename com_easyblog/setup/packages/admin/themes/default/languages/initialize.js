
EasyBlog
.require()
.done(function($){

    EasyBlog.ajax('admin/views/languages/getLanguages', {})
        .done(function() {
            window.location     = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easyblog&view=languages';
        })
        .fail(function(html, message) {
            $('[data-table-grid]').replaceWith(html);
        });
});