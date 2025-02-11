<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
	<height>250</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{deleteButton}": "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},
		"{deleteButton} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EB_POLL_DIALOG_DELETE_TITLE'); ?></title>
	<content>
		<div class="t-text--center t-d--flex t-align-items--cx t-justify-content--c t-h--100">
			<p>
				<?php echo JText::sprintf('COM_EB_POLL_DIALOG_DELETE_DESC', $poll->title); ?>
			</p>

			<form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">
				<input type="hidden" name="cid[]" value="<?php echo $poll->id; ?>" />

				<?php echo $this->fd->html('form.action', 'polls.delete'); ?>
			</form>
		</div>
	</content>
	<buttons alignment="center">
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_DELETE_BUTTON', 'danger'); ?>
	</buttons>
</dialog>
