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
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GENERAL_ATTRIBUTES'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<div class="o-form-group">
			<label class="o-control-label eb-composer-field-label">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODE_LANGUAGE'); ?>
			</label>
			<div class="o-control-input">
				<?php echo $this->fd->html('form.dropdown', 'codeLanguages', 'html', $languages, ['attr' => 'data-code-mode']); ?>
			</div>
		</div>

		<div class="o-form-group">
			<label class="o-control-label eb-composer-field-label">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODE_FONT_SIZE'); ?>
			</label>
			<div class="o-control-input">
				<?php echo $this->fd->html('form.dropdown', 'codeFontsize', '12', [
					'10' => '10px',
					'11' => '11px',
					'12' => '12px',
					'13' => '13px',
					'14' => '14px',
					'16' => '16px',
					'18' => '18px',
					'20' => '20px',
					'24' => '24px'
				], ['attr' => 'data-code-fontsize']); ?>
			</div>
		</div>

		<div class="o-form-group">
			<label class="o-control-label eb-composer-field-label">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODE_THEME'); ?>
			</label>
			<div class="o-control-input">
				<?php echo $this->fd->html('form.dropdown', 'codeTheme', 'ace/theme/github', $themes, ['attr' => 'data-code-theme']); ?>
			</div>
		</div>

		<div class="o-form-group">

			<?php echo $this->html('composer.field.label', 'COM_EB_COMPOSER_HEIGHT', 'code-height'); ?>

			<div class="o-control-input">
				<input type="text" name="height" id="code-height" class="o-form-control text-center" value="<?php echo $data->height;?>"
					data-code-height style="width: 60px;display: inline-block;"
				/> px
			</div>
		</div>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'gutter', 'COM_EASYBLOG_COMPOSER_BLOCKS_CODE_SHOW_GUTTER', $data->show_gutter, 'data-code-gutter'); ?>
	</div>
</div>
