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
	<height>250</height>
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
	<title><?php echo JText::_($template->id ? 'COM_EASYBLOG_UPDATE_TEMPLATE' : 'COM_EB_SAVE_AS_POST_TEMPLATE'); ?></title>
	<content>
		<form data-template-form method="post" action="<?php echo JRoute::_('index.php');?>">
			<div class="t-mb--lg">

				<div data-alert-placeholder></div>

				<div class="o-form-group t-border--danger">
					<label class="o-control-label eb-composer-field-label" for="template_title">
						<?php echo JText::_('COM_EASYBLOG_TITLE'); ?>
					</label>
					<div class="o-control-input">
						<input type="text" class="o-form-control" name="template_title" id="template_title" placeholder="<?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_TITLE_PLACEHOLDER');?>" value="<?php echo $template->title; ?>" autocomplete="off" data-template-title>
					</div>
				</div>

				<div class="t-mt--xs t-mb--md t-text--danger" data-error></div>

				<div class="o-checkbox t-mb--md">
					<input type="checkbox" name="global" id="template_global" value="1" data-template-global <?php echo $template->system ? 'checked="checked"' : ''; ?>>
					<label for="template_global">
						<?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_AS_GLOBAL'); ?>
					</label>
				</div>
				<?php if ($template->canLock()) { ?>
				<div class="o-checkbox t-mb--md">
					<input type="checkbox" name="lock" id="template_lock" value="1" data-template-lock <?php echo $template->isLocked() ? 'checked="checked"' : ''; ?>>
					<label for="template_lock">
						<?php echo JText::_('COM_EB_LOCK_THIS_POST_TEMPLATE'); ?>
					</label>
				</div>
				<?php } ?>
			</div>
			<input type="hidden" name="template_id" data-template-id value="<?php echo $template->id;?>" />
		</form>
	</content>
	<buttons>
		<?php if ($template->canDelete()) { ?>
			<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_DELETE_TEMPLATE_BUTTON', 'danger', [
				'attributes' => 'data-delete',
				'class' => 't-mr--auto'
			]); ?>
		<?php } ?>

		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CANCEL_BUTTON'); ?>

		<?php echo $this->html('dialog.submitButton', $template->id ? 'COM_EASYBLOG_UPDATE_TEMPLATE' : 'COM_EASYBLOG_CREATE_TEMPLATE', 'primary', ['attributes' => 'data-save']); ?>
	</buttons>
</dialog>

