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

		"{submitButton} click" : function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_DIALOG_AUTOPOST_TO_SOCIAL_SITES'); ?></title>
	<content>
		<p class="mt-5">
			<?php echo JText::sprintf('COM_EASYBLOG_DIALOG_AUTOPOST_TO_SOCIAL_SITES_CONTENT', $type);?>
		</p>

		<form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">
			<?php echo $this->fd->html('form.action', 'posts.autopost'); ?>
			<input type="hidden" name="type" value="<?php echo $type;?>" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CANCEL_BUTTON'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_AUTOPOST_BUTTON'); ?>
	</buttons>
</dialog>
