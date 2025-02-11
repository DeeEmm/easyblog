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
	<width>400</width>
	<height>120</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-close-button]",
		"{form}": "[data-approve-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_ACCEPT_TEAMBLOG_REQUEST_DIALOG_TITLE');?></title>
	<content>
		<form data-approve-form action="<?php echo JRoute::_('index.php');?>" method="post">
			<p class="mt-10 ml-10 mr-10"><?php echo JText::_('COM_EASYBLOG_ACCEPT_TEAMBLOG_REQUEST_DIALOG_CONTENT'); ?></p>

			<?php foreach ($ids as $id) { ?>
				<input type="hidden" name="ids[]" value="<?php echo $id;?>" />
			<?php } ?>

			<?php echo $this->fd->html('form.action', 'teamblogs.approve'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_TEAMBLOGS_APPROVE_REQUEST'); ?>
	</buttons>
</dialog>
