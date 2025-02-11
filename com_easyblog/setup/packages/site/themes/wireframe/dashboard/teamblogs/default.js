EasyBlog.require()
.script('site/dashboard/table', 'site/author/suggest', 'site/dashboard/teamblogs')
.done(function($) {
	$('[data-eb-dashboard-teams]').implement(EasyBlog.Controller.Dashboard.Table);
});