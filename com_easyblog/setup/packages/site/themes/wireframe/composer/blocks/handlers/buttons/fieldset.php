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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EB_PROPERTIES'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.text', 'link', 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_LINK', $data->link, 'data-button-link'); ?>

		<div class="o-form-group">
			<label class="o-control-label eb-composer-field-label">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_SIZE'); ?>
			</label>
			<div class="o-control-input">
				<?php echo $this->fd->html('form.dropdown', 'buttonSize', '', [
					'btn-xs' => 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_SIZE_XSMALL',
					'btn-sm' => 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_SIZE_SMALL',
					'' => 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_SIZE_STANDARD',
					'btn-lg' => 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_SIZE_LARGE'
				], ['attr' => 'data-eb-composer-block-button-size']); ?>
			</div>
		</div>

		<div class="o-form-group">
			<label class="o-control-label eb-composer-field-label">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_OPEN_TARGET'); ?>
			</label>
			<div class="o-control-input">
				<?php echo $this->fd->html('form.dropdown', 'target', '', [
					'' => 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_ATTRIBUTE_TARGET_NONE',
					'_blank' => 'New Window or Tab',
					'_self' => 'Current Window',
					'_parent' => 'Parent Window',
					'_top' => 'Top Frame'
				], ['attr' => 'data-button-target']); ?>
			</div>
		</div>

		<?php echo $this->html('composer.field.checkbox', 'nofollow', 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_ATTRIBUTE_NOFOLLOW', $data->nofollow, array('data-button-nofollow')); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_STYLE'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div class="eb-swatch swatch-grid">
				<div class="row">
					<?php foreach ($buttons as $button) { ?>
					<div class="col-sm-4">
						<div class="eb-swatch-item-button eb-composer-button-preview" data-style="btn-<?php echo $button;?>" data-eb-composer-button-swatch-item>
							<div class="eb-swatch-preview">
								<span class="btn btn-<?php echo $button;?>"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_COLOR_BUTTON'); ?></span>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_COLOR_' . strtoupper($button) . '_TYPE');?></span>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
