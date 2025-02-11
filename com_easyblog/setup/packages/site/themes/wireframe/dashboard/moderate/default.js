
EasyBlog.require()
.script('site/dashboard/table')
.done(function($){
	$('[data-eb-dashboard-moderate]').implement(EasyBlog.Controller.Dashboard.Table);
});