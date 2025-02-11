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
<?php if ($isImage) { ?>
	<div class="eb-post-simple__thumb">
		<div class="o-aspect-ratio" style="--aspect-ratio: <?php echo $coverAspectRatio; ?>;">
		<a class="eb-post-simple__image" href="<?php echo $post->getPermalink(); ?>" style="background-image: url('<?php echo $cover->url; ?>');">
			<?php if ($post->isFeatured()) { ?>
			<span class="eb-post-simple__label">
				<i class="fdi fa fa-bookmark"></i>
			</span>
			<?php } ?>
		</a>
		</div>
	</div>
<?php } else { ?>
	<?php if ($post->isEmbedCover()) { ?>
		<?php echo $cover->videoEmbed; ?>
	<?php } else { ?>
		<?php echo $cover->video; ?>
	<?php } ?>
<?php } ?>
