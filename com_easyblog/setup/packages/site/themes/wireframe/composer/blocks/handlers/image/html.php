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
<div class="eb-composer-placeholder eb-composer-placeholder-image text-center"
	<?php if ($this->acl->get('upload_image')) { ?>
	data-eb-composer-image-placeholder
	data-key="_cG9zdA--"
	data-type="image"
	data-plupload-multi-selection="0"
	<?php } ?>
>

	<div data-plupload-drop-element>
		<i class="eb-composer-placeholder-icon fdi fa fa-camera"></i>

		<?php if ($this->acl->get('upload_image')) { ?>
		<b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_PLACEHOLDER_TITLE'); ?></b>
		<p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_PLACEHOLDER_DESC'); ?></p>
		<?php } else { ?>
		<b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_BROWSE_FOR_IMAGE'); ?></b>
		<p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_BROWSE_FOR_IMAGE_DESC'); ?></p>
		<?php } ?>

		<div class="eb-composer-place-options" data-eb-composer-place-options>
			<div class="eb-composer-place-options__item">
				<button type="button" class="btn btn--sm btn-eb-default-o" data-eb-insert-url-button>
					<?php echo JText::_('COM_EASYBLOG_BLOCKS_ENTER_URL'); ?>
				</button>
			</div>
			<div class="eb-composer-place-options__item">
				<button type="button" class="btn btn--sm btn-eb-default-o" data-eb-mm-browse-button data-eb-mm-start-uri="_cG9zdA--" data-eb-mm-filter="image">
					<?php echo JText::_('COM_EASYBLOG_BLOCKS_BROWSE_MEDIA'); ?>
				</button>
			</div>

			<?php if ($this->acl->get('upload_image')) { ?>
			<div class="eb-composer-place-options__item">
				<button type="button" class="btn btn--sm btn-eb-primary" data-plupload-browse-button>
					<i class="fdi fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_BLOCKS_UPLOAD_IMAGE_FILE'); ?>
				</button>
			</div>
			<?php } ?>

		</div>

		<div class="eb-composer-place-url" data-eb-image-url-form>
			<div class="o-input-group o-input-group--sm">
				<input type="text" class="o-form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_IMAGE_BLOCK_URL_PLACEHOLDER');?>" data-eb-image-url-textbox />

				<span class="o-input-group__btn">
					<button type="button" class="btn btn--sm btn-eb-primary-o" data-eb-image-url-add>
						<i class="fdi fa fa-check"></i>
					</button>
					<button type="button" class="btn btn--sm btn-eb-default-o" data-eb-image-url-cancel>
						<i class="fdi fa fa-times"></i>
					</button>
				</span>
			</div>
		</div>


		<?php echo $this->output('site/composer/progress'); ?>

		<?php echo $this->output('site/composer/blocks/error'); ?>
	</div>
</div>
