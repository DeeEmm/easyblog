EasyBlog.ready(function($) {

	var el = document.createElement('script');
	var url = window.location.href;
	var head = document.getElementsByTagName("head")[0];
	el.src = '//www.goemotify.com/api/2.0/reactions?url='+url+'&apikey=<?php echo $this->config->get('emotify_key');?>';
	el.type = 'text/javascript';
	head.appendChild(el);

});