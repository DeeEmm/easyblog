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
<?php if (in_array($post->getType(), ['photo', 'standard', 'twitter', 'email', 'link'])) { ?>
<div class="eb-post-body type-<?php echo $post->getType(); ?>" data-blog-post-content>
	<?php if ($cover) { ?>
		<?php echo $this->html('post.list.cover', $post, $params); ?>
	<?php } ?>

	<?php echo $content; ?>
</div>
<?php } ?>

<?php if ($post->getType() == 'video') { ?>
<div class="eb-post-video">
	<?php foreach ($post->videos as $video) { ?>
	<div class="eb-responsive-video">
		<?php echo $video->html;?>
	</div>
	<?php } ?>
</div>
<?php } ?>