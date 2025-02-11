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
<dialog>
	<width>550</width>
	<height>300</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-template-form]",
		"{deleteButton}" : "[data-delete]",
		"{saveButton}": "[data-save]",
		"{errorMessage}": "[data-error]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_($template->id ? 'COM_EB_COMPOSER_BLOCKS_UPDATE_TEMPLATE' : 'COM_EB_COMPOSER_BLOCKS_SAVE_TEMPLATE'); ?></title>
	<content>
		<form data-template-form method="post" action="<?php echo JRoute::_('index.php');?>">
			<div class="t-mb--lg">

				<div class="o-form-group t-border--danger">
					<label class="o-control-label eb-composer-field-label" for="template_title">
						<?php echo JText::_('COM_EASYBLOG_TITLE'); ?>
					</label>
					<div class="o-control-input">
						<input type="text" class="o-form-control" name="template_title" id="template_title" placeholder="<?php echo JText::_('COM_EB_SAVE_BLOCK_TEMPLATE_TITLE_PLACEHOLDER');?>" value="<?php echo $template->title; ?>" autocomplete="off" data-template-title>
					</div>
				</div>

				<div class="t-mt--sm or t-mt--xs t-text--danger" data-error></div>

				<div class="o-form-group t-border--danger">
					<label class="o-control-label eb-composer-field-label" for="template_title">
						<?php echo JText::_('COM_EASYBLOG_DESCRIPTION'); ?>
					</label>
					<div class="o-control-input">
						<textarea class="o-form-control" name="template_title" id="template_title" placeholder="<?php echo JText::_('COM_EB_BLOCK_TEMPLATE_DESC_PLACEHOLDER');?>" value="" autocomplete="off" data-template-description><?php echo $template->description; ?></textarea>
					</div>
				</div>

				<div class="t-mt--sm or t-mt--xs t-text--danger" data-error></div>

				<div class="o-checkbox">
					<input type="checkbox" name="global" id="template_global" value="1" data-template-global <?php echo $template->global ? 'checked="checked"' : ''; ?>>
					<label for="template_global">
						<?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_AS_GLOBAL'); ?>
					</label>
				</div>
			</div>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CANCEL_BUTTON', 'default', ['class' => 't-mr--auto']); ?>

		<?php echo $this->html('dialog.submitButton', $template->id ? 'COM_EASYBLOG_SAVE_AS_NEW_TEMPLATE' : 'COM_EASYBLOG_CREATE_TEMPLATE', $template->id ? 'default' : 'primary', [
			'attributes' => 'data-save data-save-type="new"',
			'class' => $template->id ? 't-text--primary' : ''
		]); ?>

		<?php if ($template->id) { ?>
			<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_UPDATE_TEMPLATE', 'primary', [
				'attributes' => 'data-save data-save-type="update"'
			]); ?>
		<?php } ?>
	</buttons>
</dialog>

