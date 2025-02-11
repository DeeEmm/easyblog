

EasyBlog.ready(function($){

	$('.blog-comment-tabs').find('a').bind('click' , function() {
		$(this).parents('ul').children('li').removeClass('active');
		$(this).parents('ul').children('li').addClass('inactive');
		$(this).parent().addClass('active').removeClass('inactive');

		var activeId = $(this).parent().attr('id');

		$('.blog-comment-contents').children().hide();
		$('.blog-comment-contents').find('.blog-comment-' + activeId).show();

		if (activeId == 'system-disqus') {

			// determine if this disqus script load on the page or not
			if (typeof(disqus_loaded) == "undefined" || !disqus_loaded) {
				
			    disqus_loaded = 1;

			    EasyBlog.ajax('site/views/comments/getDisqusCommentHTML', {})
			    .done(function(output) {
			    	$('.blog-comment-system-disqus').html(output);
			    });
			}
		}

		// Temporarily hardcode for Komento
		// Use Komento's jQuery to trigger instead of using EasyBlog's jQuery due to version difference
		// Using EBjQ to trigger (on FD4.0) will not trigger the resize event properly on KMTjQ binded event (on FD3.1)
		if (activeId == 'system-komento' && Komento) {

			// We do setTimeout 1ms here is because of a standard practice where element shoudl be rendered by browser first after calling .show() before performing any action that relates to calculation of width and height
			setTimeout(function() {
				Komento.$(window).trigger('resize');
			}, 1);
		}
	});
});
