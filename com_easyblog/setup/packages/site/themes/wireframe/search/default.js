EasyBlog.require()
.script('site/search/filters')
.done(function($) {
	$('[data-eb-search-wrapper]').implement(EasyBlog.Controller.Search.Filters, {
		"activeCategoryId": "<?php echo $activeCategoryId; ?>"
	});
});
