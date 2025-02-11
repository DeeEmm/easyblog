EasyBlog.require()
.script('site/dashboard/table')
.done(function($) {
	$('[data-eb-dashboard-templates]').implement(EasyBlog.Controller.Dashboard.Table);
});