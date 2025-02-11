
EasyBlog.ready(function($){

    $('[data-meta-description]').on('keyup', function()
    {
        var length = $(this).val().length;

        $('[data-meta-counter]').html(length);
    });
});