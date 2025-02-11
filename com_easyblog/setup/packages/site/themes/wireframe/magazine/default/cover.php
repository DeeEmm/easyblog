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
<?php if (EB::image()->isImage($post->getImage()) || $post->isExternalImageCover($post->getImage())) { ?>
	<a class="eb-mag-blog-image" href="<?php echo $post->getPermalink(); ?>" style="background-image: url('<?php echo $post->getImage($imageSize);?>');" aria-label="<?php echo $this->fd->html('str.escape', $post->title);?>"></a>
<?php } else { ?>
	<?php if ($post->isEmbedCover()) { ?>
		<div class="o-aspect-ratio" style="--aspect-ratio: <?php echo $this->config->get('cover_aspect_ratio'); ?>;">
			<?php echo $post->getEmbedCover(); ?>
		</div>
	<?php } else { ?>
		<?php echo EB::media()->renderVideoPlayer($post->getImage(), ['width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false], false); ?>
	<?php } ?>
<?php } ?>
