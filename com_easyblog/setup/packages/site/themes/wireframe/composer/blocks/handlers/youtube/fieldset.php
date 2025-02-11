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
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_BLOCKS_YOUTUBE_URL'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div style="margin: 0 auto;" class="o-input-group">
				<input type="text" value="" class="o-form-control" data-youtube-fieldset-url />
				<span class="o-input-group__btn">
					<a href="javascript:void(0);" class="btn btn-eb-default-o" data-youtube-fieldset-update-url><?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON'); ?></a>
				</span>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GENERAL_ATTRIBUTES'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'youtube_nocookie', 'COM_EB_COMPOSER_BLOCKS_YOUTUBE_NOCOOKIE', $this->config->get('youtube_nocookie'), ['data-youtube-fieldset-nocookie']); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'youtube_fluid', 'COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_FLUID_LAYOUT', true, ['data-youtube-fieldset-fluid']); ?>

		<div class="o-form-group hide" data-youtube-fieldset-width-fieldset>
			<label class="o-control-label eb-composer-field-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_WIDTH'); ?></label>

			<div class="o-control-input">
				<div class="o-input-group">
					<input type="text" data-youtube-fieldset-width value="" class="o-form-control text-center" />
					<span class="o-input-group__addon"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PIXELS');?></span>
				</div>
			</div>
		</div>

		<div class="o-form-group hide" data-youtube-fieldset-height-fieldset>
			<label class="o-control-label eb-composer-field-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_HEIGHT'); ?></label>

			<div class="o-control-input">
				<div class="o-input-group">
					<input type="text" data-youtube-fieldset-height value="" class="o-form-control text-center" />
					<span class="o-input-group__addon"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PIXELS');?></span>
				</div>
			</div>
		</div>

		<div class="o-form-group">
			<label class="o-control-label eb-composer-field-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED'); ?></label>

			<div class="o-control-input">
				<?php echo $this->fd->html('form.dropdown', 'youtube_related', '', [
					'any' => 'COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED_ANY_CHANNEL',
					'same' => 'COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED_SAME_CHANNEL'
				], ['attr' => 'data-youtube-fieldset-related']); ?>
			</div>
		</div>
	</div>
</div>
