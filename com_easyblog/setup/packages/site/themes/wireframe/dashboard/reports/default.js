EasyBlog.require()
.script('site/dashboard/table', 'site/dashboard/filters')
.done(function($) {

	$('[data-eb-dashboard-posts]').implement(EasyBlog.Controller.Dashboard.Filters);
	$('[data-eb-dashboard-posts]').implement(EasyBlog.Controller.Dashboard.Table);
});