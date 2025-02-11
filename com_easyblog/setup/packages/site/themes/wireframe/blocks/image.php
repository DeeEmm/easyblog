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
<div class="eb-image<?php echo $classnames; ?>"<?php echo $imageContainerAttr; ?>>
	<div class="eb-image-figure<?php echo $imageFigureIsResponsive; ?>"<?php echo $imageFigureAttr; ?>>
		<?php if (!empty($block->data->popup_url)) { ?>
			<a class="eb-image-viewport eb-image-popup-button"
				href="<?php echo $block->data->popup_url;?>"
				title="<?php echo isset($block->data->caption_text) && $block->data->caption_text ? $block->data->caption_text : '';?>">
		<?php } ?>

		<?php if (empty($block->data->popup_url)) { ?>
			<a class="eb-image-viewport"<?php echo $imageLinkAttr; ?>>
		<?php } ?>
			<?php if (!isset($block->data->extension) || isset($block->data->extension) && $block->data->extension !== 'webp') { ?>
				<img src="<?php echo $block->data->url;?>" <?php echo isset($block->data->alt_text) && $block->data->alt_text ? 'alt="' . $block->data->alt_text . '"' : '';?>
				<?php echo $imageElementAttr; ?>/>
			<?php } ?>
			<?php if (isset($block->data->extension) && $block->data->extension == 'webp') { ?>
				<picture>
					<source srcset="<?php echo $block->data->url;?>" type="image/webp">
					<img src="<?php echo $block->data->fallbackItem->url;?>" <?php echo isset($block->data->alt_text) && $block->data->alt_text ? 'alt="' . $block->data->alt_text . '"' : '';?>
					<?php echo $imageElementAttr; ?>/>
				</picture>
			<?php } ?>
			</a>
	</div>
	<?php if (isset($block->data->caption_enabled) && $block->data->caption_enabled) { ?>
		<?php if (isset($block->data->caption_text) && !empty($block->data->caption_text)) { ?>
		<div class="eb-image-caption"<?php echo $imageCaptionAttr; ?>>
			<span><?php echo $block->data->caption_text; ?></span>
		</div>
		<?php } ?>
	<?php } ?>
</div>