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
<?php echo $this->html('post.entry.cover', $post, ['showCover' => $showCover, 'showCoverPlaceholder' => $showCoverPlaceholder]); ?>

<?php if (!empty($post->toc)) { ?>
	<?php echo $post->toc; ?>
<?php } ?>

<?php if ($post->getType() == 'link') { ?>
<div class="eb-post-headline">
	<div class="eb-post-headline-source">
		<a href="<?php echo $post->getAsset('link')->getValue(); ?>" target="_blank"><?php echo $post->getAsset('link')->getValue();?></a>
	</div>
</div>
<?php } ?>

<?php echo $content; ?>

<?php if (!$preview && $requireLogin) { ?>
	<?php echo $this->html('post.entry.restricted', $post); ?>
<?php } ?>