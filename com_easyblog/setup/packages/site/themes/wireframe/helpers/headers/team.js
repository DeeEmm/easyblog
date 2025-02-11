<?php if (!$jsLoaded) { ?>
EasyBlog.require()
.script('site/teamblogs')
.done(function($){

	$('[data-team-item]').implement(EasyBlog.Controller.TeamBlogs.Item, {
		"returnUrl": "<?php echo $returnUrl;?>"
	});
});
<?php } ?>
