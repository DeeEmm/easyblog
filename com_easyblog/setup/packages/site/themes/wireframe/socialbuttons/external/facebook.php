<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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
EasyBlog.require()
.script("site/bookmarklet")
.done(function($) {

	$('#<?php echo $placeholder;?>').bookmarklet('facebook', {
		"url": "<?php echo $url;?>",
		"send": "<?php echo $send;?>",
		"size": "<?php echo $size;?>",
		"verb": "<?php echo $verb;?>",
		"locale": "<?php echo $locale;?>",
		"theme": "<?php echo $fbTheme;?>",
		"tracking" : <?php echo $tracking ? 'true' : 'false';?>
	});
});
</script>
<div class="eb-facebook-like <?php echo $verb;?> <?php echo $this->config->get('main_facebook_like_send') == '1' ? 'has-sendbtn' : '' ?>">
	<span id="<?php echo $placeholder;?>"></span>
</div>