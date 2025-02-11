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
	<width>470</width>
	<height>200</height>
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
	<title><?php echo JText::_('COM_EB_UPDATE_MISSING_META_DIALOG_TITLE');?></title>
	<content>
		<form data-approve-form action="<?php echo JRoute::_('index.php');?>" method="post">
			<p class="mt-10 ml-10 mr-10"><?php echo JText::_('COM_EB_UPDATE_MISSING_META_DIALOG_CONTENT'); ?></p>

			<div class="ml-10 mr-10">
				<?php echo $this->fd->html('form.dropdown', 'meta_type', '', [
					'blogger' => 'Blogger',
					'category' => 'Category',
					'tag' => 'Tag'
				]); ?>
			</div>

			<?php echo $this->fd->html('form.action', 'meta.restoreMeta'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_UPDATE_BUTTON'); ?>
	</buttons>
</dialog>
