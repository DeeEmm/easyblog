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
	<width>420</width>
	<height>150</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{alert}": "[data-folder-form-error]",
		"{folderName}": "[data-folder-name]",
		"{submitButton}" : "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		init: function() {
			// Focus on the input
			this.folderName().focus();
		},
		"{closeButton} click": function() {
			this.parent.close();
		},
		"{folderName} keyup": function(element, args) {
			var event = args[0];

			if (event.keyCode == 13) {
				this.submitButton().click();
			}
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_DIALOG_MM_CREATE_NEW_FOLDER'); ?></title>
	<content>
		<div class="t-lg-mb--lg"><?php echo JText::_('COM_EASYBLOG_DIALOG_MM_CREATE_NEW_FOLDER_DESC');?></div>

		<div class="o-form-group">
			<input placeholder="<?php echo JText::_('COM_EASYBLOG_ENTER_A_NAME_FOR_YOUR_FOLDER');?>" class="o-form-control" data-folder-name />
			<div class="text-error t-hidden" data-folder-form-error></div>
		</div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>

		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_CREATE_FOLDER_BUTTON'); ?>
	</buttons>
</dialog>
