EasyBlog.require()
.script('site/dashboard/table', 'site/dashboard/filters')
.done(function($) {
	$('[data-eb-dashboard-tags]').implement(EasyBlog.Controller.Dashboard.Filters);
    $('[data-eb-dashboard-tags]').implement(EasyBlog.Controller.Dashboard.Table);
});
