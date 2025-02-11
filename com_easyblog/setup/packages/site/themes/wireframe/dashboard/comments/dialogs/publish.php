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
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_PUBLISH_COMMENT_DIALOG_TITLE'); ?></title>
	<content>
		<p class="mt-5">
			<?php echo JText::_('COM_EASYBLOG_PUBLISH_COMMENT_DIALOG_CONTENT');?>
		</p>

		<form data-form-response method="post" action="<?php echo JRoute::_( 'index.php' );?>">
			<?php foreach ($ids as $id) {?>
				<input type="hidden" name="ids[]" value="<?php echo $id;?>" />
			<?php } ?>
			<?php echo $this->fd->html('form.action', 'comments.publish'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CANCEL_BUTTON'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_PUBLISH_COMMENTS_BUTTON'); ?>
	</buttons>
</dialog>
