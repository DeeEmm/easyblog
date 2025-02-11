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
<div class="article-intro-image">
	<div class="eb-post-thumb<?php echo $fullWidthCover ? " is-full" : " is-" . $alignment; ?> mb-0">
		<?php if (!$cropCover) { ?>
			<a
				<?php if ($isImage) { ?>
					href="<?php echo $cover->originalUrl; ?>"
					target="_blank"
				<?php }?>
				class="eb-post-image"
				title="<?php echo $cover->title; ?>"
				caption="<?php echo $cover->caption;; ?>"
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
					<?php echo $cover->video; ?>
				<?php } ?>
			</a>
		<?php } ?>

		<?php if ($cropCover) { ?>
			<?php if ($postCover && $isImage) { ?>
				<a href="<?php echo $cover->originalUrl; ?>"
					target="_blank"
					class="eb-post-image-cover"
					title="<?php echo $cover->title; ?>"
					caption="<?php echo $cover->caption; ?>"
					style="
						background-image: url('<?php echo $cover->url; ?>');
						<?php if ($fullWidthCover) { ?>
						width: 100%;
						<?php } else { ?>
						width: <?php echo $width ? $width : '260';?>px;
						<?php } ?>
						height: <?php echo $height ? $height : '200';?>px;"
				></a>

				<?php if ($cover->caption) { ?>
					<span class="eb-post-thumb-caption"><?php echo $cover->caption; ?></span>
				<?php } ?>

			<?php } else { ?>
				<a
				class="eb-post-image"
				title="<?php echo $cover->title;?>"
				caption="<?php echo $cover->caption;?>"
				style="
					<?php if ($fullWidthCover) { ?>
					width: 100%;
					<?php } else { ?>
					width: <?php echo $width ? $width : '260';?>px;
					<?php } ?>"
				>
					<?php echo $cover->video; ?>
				</a>
			<?php } ?>
		<?php } ?>
	</div>
</div>
