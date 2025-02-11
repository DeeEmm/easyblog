EasyBlog.ready(function($){

   $('[data-use-version]').on('click', function()
    {
        var versionId = $(this).data('version-id'),
            blogId = $(this).data('blog-id');

        EasyBlog.dialog({
            content: EasyBlog.ajax( 'site/views/dashboard/useThisVersion', {'versionId': versionId,'blogId': blogId})
             });

    });
});
