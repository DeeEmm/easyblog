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
	<height>100</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},
		"{submitButton} click" : function()
		{
			$('[data-notify-form]').submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_BLOGS_DIALOG_RENOTIFY_TITLE');?></title>
	<content>
		<p><?php echo JText::_('COM_EASYBLOG_BLOGS_DIALOG_RENOTIFY_CONTENT'); ?></p>

		<form data-notify-form method="post">
			<?php echo $this->fd->html('form.action', 'blogs.notify'); ?>

			<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_PROCEED_BUTTON'); ?>
	</buttons>
</dialog>
