<?php if (!$jsLoaded) { ?>
EasyBlog.require()
.script('site/authors')
.done(function($){
    $('[data-author-item]').implement(EasyBlog.Controller.Authors.Item);
});
<?php } ?>
