<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($showCover) { ?>
<div class="eb-post-thumb<?php echo $fullWidthCover ? " is-full" : " is-" . $alignment; ?>">
	<?php if ($isImage) { ?>
		<a
			href="<?php echo $post->getPermalink();?>"
			class="<?php echo $cropCover ? 'eb-post-image-cover' : 'eb-post-image'; ?>"
			title="<?php echo $cover->title;?>"
			caption="<?php echo $cover->caption;?>"
			style="
				<?php if ($fullWidthCover) { ?>
				width: 100%;
				<?php } else { ?>
				width: <?php echo $width ? $width : '260';?>px;
				<?php } ?>
				<?php if ($cropCover) { ?>
					background-image: url('<?php echo $cover->url;?>');
					height: <?php echo $height ? $height : '200';?>px;
				<?php } ?>"
		>
			<?php if (!$cropCover) { ?>
				<?php if ($isWebp) { ?>
					<picture>
						<source srcset="<?php echo $cover->url;?>" type="image/webp">
						<img src="<?php echo $cover->fallbackUrl;?>" alt="<?php echo $cover->alt;?>" />
					</picture>
				<?php } else { ?>
					<img src="<?php echo $cover->url;?>" alt="<?php echo $cover->alt;?>" />
				<?php } ?>
			<?php } ?>

			<?php if ($cover->caption) { ?>
				<span class="eb-post-thumb-caption"><?php echo $cover->caption; ?></span>
			<?php } ?>
		</a>
	<?php } else { ?>
		<div class="eb-post-image" title="<?php echo $cover->title;?>" caption="<?php echo $cover->caption;?>"
			style="
				<?php if ($fullWidthCover) { ?>
				width: 100%;
				<?php } else { ?>
				width: <?php echo $width ? $width : '260';?>px;
				<?php } ?>"
		>
			<?php if ($post->isEmbedCover()) { ?>
				<div class="o-aspect-ratio" style="--aspect-ratio: <?php echo $coverAspectRatio; ?>;">
					<?php echo $cover->videoEmbed; ?>
				</div>
			<?php } else { ?>
				<?php echo $cover->video; ?>
			<?php } ?>
		</div>
	<?php } ?>
</div>
<?php } ?>
