EasyBlog.require()
.script('site/dashboard/table')
.done(function($) {
	$('[data-eb-dashboard-categories]').implement(EasyBlog.Controller.Dashboard.Table);
});