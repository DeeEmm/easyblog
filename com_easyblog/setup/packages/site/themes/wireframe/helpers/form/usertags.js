EasyBlog.require()
.script('shared/usertags')
.done(function($) {

	$('[data-form-user-wrapper]').addController('EasyBlog.Controller.Html.Usertags');
});
