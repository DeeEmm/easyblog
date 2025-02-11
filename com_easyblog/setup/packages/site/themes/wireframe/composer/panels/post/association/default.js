EasyBlog.require()
.script('composer/panels/association')
.done(function($) {
	$('[data-composer-association]').addController('Composer.Panels.Association');
});

EasyBlog.ready(function($){
	window.insertAssoc = function(id, codeid) {
		EasyBlog.ajax('site/views/composer/getPostName', {
			"id" : id
		}).done(function(name) {
			$('input#assoc-postname' + codeid).val(name);
			$('input#assoc-postid' + codeid).val(id);
			
			EasyBlog.dialog().close();
		});
	}
});