EasyBlog
.require()
.script('site/dashboard/filters', 'site/dashboard/table')
.done(function($) {
	$('[data-eb-dashboard-comments]').implement(EasyBlog.Controller.Dashboard.Filters);
	$('[data-eb-dashboard-comments]').implement(EasyBlog.Controller.Dashboard.Table);
});