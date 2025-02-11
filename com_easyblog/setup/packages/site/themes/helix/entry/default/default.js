EasyBlog.require()
.script('site/posts/posts', 'site/posts/readingprogress')
.done(function($) {

	<?php if ($preview) { ?>
		// prevent all anchor from click when this is a preview page.
		$("a:not([data-preview-toolbar] a, [data-blog-preview-userevision], [data-tabs-list] a)").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
		});

	<?php } ?>

	// Implement post library
	$('[data-blog-post]').implement(EasyBlog.Controller.Posts, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>
	});

	<?php if ($this->config->get('main_show_reading_progress')) { ?>
		$('[data-blog-post]').implement(EasyBlog.Controller.Posts.Readingprogress, {
			"autoload" : <?php echo $this->entryParams->get('pagination_style') == 'autoload' ? 'true' : 'false'; ?>
		});
	<?php } ?>
});
