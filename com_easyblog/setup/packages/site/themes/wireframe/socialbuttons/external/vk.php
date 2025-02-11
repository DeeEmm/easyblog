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
	
	$('#<?php echo $placeholder;?>').bookmarklet('vk', {
		"text": "<?php echo $title;?>",
		"apiKey": "<?php echo $apiKey;?>",
		"placeholder": "<?php echo $placeholder;?>",
		"url": "<?php echo $url;?>",
		"size": "<?php echo $size;?>",
		"description": "<?php echo $desc;?>",
		"image": <?php echo $image ? '"' . $image . '"' : 'false';?>
	});
});
</script>
<div class="eb-social-button vk">
	<span id="<?php echo $placeholder;?>"></span>
</div>