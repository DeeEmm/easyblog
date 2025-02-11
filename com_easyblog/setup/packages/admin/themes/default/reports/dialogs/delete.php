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
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},
		"{submitButton} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_DIALOG_REPORTS_DELETE_TITLE'); ?></title>
	<content>
		<p><?php echo JText::_('COM_EASYBLOG_DIALOG_REPORTS_DELETE_CONTENT');?></p>

		<form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">
			<input type="hidden" name="cid[]" value="<?php echo $id;?>" />
			<input type="hidden" name="return" value="<?php echo base64_encode('index.php?option=com_easyblog&view=reports');?>" />
			<?php echo $this->fd->html('form.action', 'reports.delete'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_DELETE_BUTTON', 'danger'); ?>
	</buttons>
</dialog>
