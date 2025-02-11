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
	<width>500</width>
	<height>180</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function()
		{
			this.parent.close();
		},
		"{submitButton} click" : function()
		{
			this.form().submit();
		}
	}
	</bindings>
	<title>
		<?php echo JText::_('COM_EASYBLOG_ADD_NEW_SUBSCRIBER_DIALOG_TITLE');?>
	</title>
	<content>
		<p class="mb-20"><?php echo JText::_('COM_EASYBLOG_ADD_NEW_SUBSCRIBER_DIALOG_CONTENT');?></p>

		<form method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal mt-20" data-form-response>

			<div class="form-group">
				<label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTIONS_FULLNAME'); ?></label>
				<div class="col-cell">
					<input class="form-control input-sm" type="text" id="name" name="name" size="45" value="" data-subscribe-name />
				</div>
			</div>

			<div class="form-group">
				<label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></label>
				<div class="col-cell">
					<input type="text" id="email" name="email" class="form-control input-sm" size="45" value="" data-subscribe-email />
				</div>
			</div>
			<?php echo $this->fd->html('form.hidden', 'type', $type);?>
			<?php echo $this->fd->html('form.action', 'subscriptions.create'); ?>
		</form>

	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_ADD_BUTTON'); ?>
	</buttons>
</dialog>
