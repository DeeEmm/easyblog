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
<div class="eb-social-button pinterest">
	<div id="<?php echo $placeholder;?>">
	<a href="https://pinterest.com/pin/create/button/?url=<?php echo $url;?>&media=<?php echo $media;?>&description=<?php echo urlencode($title);?>"
		data-pin-do="buttonPin"
		data-pin-count="<?php echo $size == 'small' ? 'beside' : 'above';?>"
		data-pin-lang="en"
		target="_blank"
	>
		<span class="sr-only">pinterest</span>
	</a>
	</div>
</div>

<?php if ($loadScript) { ?>
<script>
EasyBlog.require()
.script('site/posts/pinterest')
.done(function() {

	EasyBlog.pinterest.init();

});
</script>
<?php } ?>
