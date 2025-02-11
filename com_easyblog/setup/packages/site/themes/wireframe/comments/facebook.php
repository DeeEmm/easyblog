<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script type="text/javascript">
EasyBlog.ready(function($) {
if (!window.FB) {

	if (!document.getElementById("fb-root")) {
		$("<div id='fb-root'></div>").prependTo("body");
	}

	var jssdk = document.getElementById("facebook-jssdk"),
		FBInited;

	// No JSSDK
	if (!jssdk) {

		var head = document.getElementsByTagName("head")[0],
			script = document.createElement("script");

			head.appendChild(script);
			script.id = "facebook-jssdk";
			script.src = "//connect.facebook.net/<?php echo $language[0];?>_<?php echo EBString::strtoupper($language[1]);?>/sdk.js#xfbml=1&appId=<?php echo $this->config->get('main_facebook_like_appid');?>&version=v3.0";


	// Has JSSDK, but no XFBML support.
	} else if (!FBInited) {

		if (!/xfbml/.test(jssdk.src)) {

			var _fbAsyncInit = window.fbAsyncInit;

			window.fbAsyncInit = function() {

				if ($.isFunction(_fbAsyncInit)) _fbAsyncInit();

	  				FB.XFBML.parse();
					// parseXFBML();
			}
		}

		FBInited = true;
	}
	
// For some reason even somewhere load this fb.init but still unable to load the comment
// Manually parse and renders XFBML markup in a document on the fly
} else {
	FB.XFBML.parse();
}
});
</script>

<div class="comments-facebook">
	<div class="fb-comments" data-numposts="10"
		data-colorscheme="<?php echo $this->config->get('comment_facebook_colourscheme');?>"
		data-width="100%"
		data-href="<?php echo EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $blog->id, false, true);?>"
	>
	</div>
</div>
