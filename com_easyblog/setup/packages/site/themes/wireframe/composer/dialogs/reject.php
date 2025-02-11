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
	<width>600</width>
	<height>350</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-close-button]",
		"{reason}": "[data-reason]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_REJECT_BLOG_POST_DIALOG_TITLE'); ?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post">
			<p class="ml-10 mr-10 mt-10 mb-20"><?php echo JText::_('COM_EASYBLOG_REJECT_BLOG_POST_DIALOG_CONTENT'); ?></p>

			<div class="ml-10 mr-10">
				<textarea rows="10" class="form-control" name="message" data-reason placeholder="<?php echo JText::_('COM_EASYBLOG_REJECT_BLOG_POST_PLACEHOLDER');?>"></textarea>
			</div>

			<?php echo $this->fd->html('form.action', 'pending.reject'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CANCEL_BUTTON'); ?>

		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_REJECT_BUTTON', 'danger'); ?>
	</buttons>
</dialog>
