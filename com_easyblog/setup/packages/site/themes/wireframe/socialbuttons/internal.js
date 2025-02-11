EasyBlog.require()
.script('site/bookmarks')
.done(function($) {
	$('[data-eb-bookmarks]').implement('EasyBlog.Controller.Site.Bookmarks');
});