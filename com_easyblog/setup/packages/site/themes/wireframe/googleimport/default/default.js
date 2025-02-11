EasyBlog.require()
.library('dialog')
.script('composer/googleimport')
.done(function($) {

	// Implement post library
	$('[data-eb-googleimport-list-wrapper]').implement('EasyBlog.Controller.Composer.Googleimport.List');

});