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
<div class="eb-image eb-post-thumb<?php echo $fullWidthCover ? " is-full" : " is-" . $alignment; ?>" data-eb-entry-cover>
	<?php if (!$cropCover) { ?>
		<a
			<?php if ($isImage) { ?>
				class="eb-post-image eb-image-popup-button"
				href="<?php echo $cover->originalUrl; ?>"
				target="_blank"
			<?php }?>
			title="<?php echo $cover->title;?>"
			caption="<?php echo $cover->caption;?>"
			style="
				<?php if ($fullWidthCover) { ?>
				width: 100%;
				<?php } else { ?>
				width: <?php echo $width ? $width : '260';?>px;
				<?php } ?>"
		>
			<?php if ($postCover && $isImage) { ?>
				<?php if ($cover->isWebp) { ?>
					<picture>
						<source srcset="<?php echo $cover->url; ?>" type="image/webp">
						<img src="<?php echo $cover->fallbackUrl; ?>" alt="<?php echo $cover->alt; ?>" />
					</picture>
				<?php } else { ?>
					<img
						src="<?php echo $cover->url; ?>"
						alt="<?php echo $cover->alt; ?>"
						width="
							<?php if ($fullWidthCover) { ?>
								100%
							<?php } else { ?>
								<?php echo $width ? $width : '260' ?>px;
							<?php } ?>"
						height="200px"
					/>
				<?php } ?>

				<?php if ($cover->caption) { ?>
					<span class="eb-post-thumb-caption"><?php echo $cover->caption; ?></span>
				<?php } ?>

			<?php } else { ?>
				<?php if ($post->isEmbedCover()) { ?>
					<div class="o-aspect-ratio" style="--aspect-ratio: 16/9;">
						<?php echo $cover->videoEmbed; ?>
					</div>
				<?php } else { ?>
					<?php echo $cover->video; ?>
				<?php } ?>
			<?php } ?>
		</a>
	<?php } ?>

	<?php if ($cropCover) { ?>
		<a
			<?php if ($isImage) { ?>
				class="eb-post-image-cover eb-image-popup-button"
				href="<?php echo $cover->originalUrl; ?>"
				style="
				display: inline-block;
				background-image: url('<?php echo $cover->url; ?>');
				<?php if ($fullWidthCover) { ?>
				width: 100%;
				<?php } else { ?>
				width: <?php echo $width ? $width : '260';?>px;
				<?php } ?>
				height: <?php echo $height ? $height : '200';?>px;"
			<?php }?>
			title="<?php echo $cover->title; ?>"
			caption="<?php echo $cover->caption; ?>"
			target="_blank"
		></a>

		<?php if ($postCover && $isImage) { ?>
			<img
				class="hide"
				src="<?php echo $cover->url; ?>"
				alt="<?php echo $cover->alt; ?>"
				width="
					<?php if ($fullWidthCover) { ?>
						100%
					<?php } else { ?>
						<?php echo $width ? $width : '260' ;?>px;
					<?php } ?>"
				height="<?php echo $height ? $height : '200' ;?>px;"
			/>

			<?php if ($cover->caption) { ?>
				<span class="eb-post-thumb-caption"><?php echo $cover->caption; ?></span>
			<?php } ?>
		<?php } else { ?>
			<?php if ($post->isEmbedCover()) { ?>
				<div class="o-aspect-ratio" style="--aspect-ratio: 16/9;">
					<?php echo $cover->videoEmbed; ?>
				</div>
			<?php } else { ?>
				<?php echo $cover->video; ?>
			<?php } ?>
		<?php } ?>
	<?php } ?>
</div>
