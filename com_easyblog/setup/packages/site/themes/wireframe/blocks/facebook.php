<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script>window.fbAsyncInit = function() {
	FB.init({
	xfbml      : true,
	version    : 'v3.2'
	});
	}; (function(d, s, id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement(s); js.id = id;
	js.src = "https://connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
<div class="eb-fb-embedded-wrapper" style="max-width: 720px">
	<div class="fb-<?php echo $type; ?>" 
		data-href="<?php echo $block->data->source; ?>"

		<?php if ($type == 'video') { ?>
		data-width="auto"
		data-allowfullscreen="true"
		<?php } ?>
		>
	</div>
</div>
