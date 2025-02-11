EasyBlog.require()
.script('site/dashboard/table')
.done(function($) {
	$('[data-eb-dashboard-requests]').implement(EasyBlog.Controller.Dashboard.Table);
});
