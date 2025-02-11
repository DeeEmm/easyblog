EasyBlog.require()
.script('site/dashboard/table')
.done(function($) {
	$('[data-eb-dashboard-block-templates]').implement(EasyBlog.Controller.Dashboard.Table);
});
