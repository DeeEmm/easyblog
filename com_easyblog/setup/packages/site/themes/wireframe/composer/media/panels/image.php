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
<div class="eb-nmm-info-panel__viewport">
	<div class="eb-nmm-info-panel__content">
		<form data-mm-form>
			<?php echo $this->html('media.preview', ['image' => $preview, 'class' => 'is-image']); ?>

			<div class="eb-nmm-panel-block">

				<?php if ($editable) { ?>
				<div class="t-lg-mb--lg">
					<a href="javascript:void(0);" class="btn btn-block btn-eb-default-o btn-nmm-upload" data-mm-edit data-key="<?php echo $file->key;?>">
						<?php echo JText::_('COM_EB_EDIT');?>
					</a>
				</div>
				<?php } ?>

				<?php echo $this->html('media.field', 'media.textbox', 'title', 'COM_EASYBLOG_IMAGE_MANAGER_TITLE', $file->title, 'data-mm-panel-title data-mm-panel-input'); ?>

				<?php if ($this->config->get('layout_editor') != 'composer' || $isLegacyPost) { ?>
					<?php echo $this->html('media.field', 'media.textbox', 'width', 'COM_EASYBLOG_MM_WIDTH', $params->get('width', $preferredVariation->width), 'data-mm-image-width data-mm-panel-input'); ?>
					<?php echo $this->html('media.field', 'media.textbox', 'height', 'COM_EASYBLOG_MM_HEIGHT', $params->get('height', $preferredVariation->height), 'data-mm-image-height data-mm-panel-input'); ?>
				<?php } ?>

				<?php echo $this->html('media.field', 'media.textarea', 'caption_text', 'COM_EASYBLOG_MM_CAPTION', $params->get('caption_text', ''), 'data-mm-panel-input', JText::_('COM_EASYBLOG_MM_CAPTIONS_PLACEHOLDER')); ?>

				<?php echo $this->html('media.field', 'media.textbox', 'alt_text', 'COM_EASYBLOG_MM_ALTERNATE_TEXT', $params->get('alt_text', ''), 'data-mm-panel-input', JText::_('COM_EASYBLOG_MM_ALT_PLACEHOLDER')); ?>

				<div class="o-form-group" data-eb-mm-panel-type="image-source">
					<label class="o-control-label" for="image-variation"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SOURCE');?></label>

					<?php echo $this->fd->html('form.dropdown', 'variation', $preferredVariation->key, $variationOptions, [
						'attr' => 'data-mm-variation data-mm-panel-input',
						'id' => 'image-variation',
						'baseClass' => 'o-form-control'
					]); ?>
				</div>

				<div class="o-form-group" data-eb-mm-panel-type="image-style">
					<label class="o-control-label" for="image-style"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE');?></label>
					<?php echo $this->fd->html('form.dropdown', 'style', $params->get('style', 'clear'), [
						'clear' => 'COM_EASYBLOG_BLOCKS_IMAGE_STYLE_CLEAR',
						'gray' => 'COM_EASYBLOG_BLOCKS_IMAGE_STYLE_GRAY',
						'polaroid' => 'COM_EASYBLOG_BLOCKS_IMAGE_STYLE_POLAROID',
						'solid' => 'COM_EASYBLOG_BLOCKS_IMAGE_STYLE_SOLID',
						'dashed' => 'COM_EASYBLOG_BLOCKS_IMAGE_STYLE_DASHED',
						'dotted' => 'COM_EASYBLOG_BLOCKS_IMAGE_STYLE_DOTTED'
					], [
						'attr' => 'data-mm-panel-input',
						'id' => 'image-style',
						'baseClass' => 'o-form-control'
					]); ?>
				</div>

				<div class="o-form-group" data-eb-mm-panel-type="image-alignment">
					<label class="o-control-label" for="image-alignment"><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT');?></label>

					<?php echo $this->fd->html('form.dropdown', 'alignment', $params->get('alignment', 'center'), [
						'left' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_LEFT',
						'center' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_CENTER',
						'right' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_RIGHT'
					], [
						'attr' => 'data-mm-panel-input',
						'id' => 'image-alignment',
						'baseClass' => 'o-form-control'
					]); ?>
				</div>

				<div class="o-form-group" data-eb-mm-panel-type="link-to">
					<label class="o-control-label" for="image-link"><?php echo JText::_('COM_EASYBLOG_MM_LINK_TO');?></label>

					<?php echo $this->fd->html('form.dropdown', 'link', 'none', [
						'none' => 'COM_EASYBLOG_MM_IMAGE_LINK_NONE',
						'lightbox' => 'COM_EASYBLOG_MM_IMAGE_LINK_POPUP',
						'custom' => 'COM_EASYBLOG_MM_IMAGE_LINK_CUSTOM_URL_SAME_WINDOW',
						'custom_new' => 'COM_EASYBLOG_MM_IMAGE_LINK_CUSTOM_URL_NEW_WINDOW'
					], [
						'attr' => 'data-mm-panel-input data-mm-preview-link',
						'id' => 'image-link',
						'baseClass' => 'o-form-control'
					]); ?>

					<input type="text" name="link_url" class="o-form-control input-sm t-lg-mt--md t-hidden" placeholder="http://site.com" data-mm-preview-custom-url/>
				</div>
			</div>

			<input type="hidden" name="ratio" value="<?php echo $preferredVariation->width / $preferredVariation->height;?>" data-mm-image-ratio />
			<input type="hidden" name="natural_ratio" value="<?php echo $preferredVariation->width / $preferredVariation->height;?>" data-mm-image-ratio-natural />
			<input type="hidden" name="url" value="<?php echo $preferredVariation->url;?>" data-mm-image-url />
			<input type="hidden" name="uri" value="<?php echo $file->uri;?>" />
			<input type="hidden" name="ratio_lock" value="1" />
		</form>
	</div>
</div>
