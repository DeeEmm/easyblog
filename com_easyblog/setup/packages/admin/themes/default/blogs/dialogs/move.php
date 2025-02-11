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
	<width>550</width>
	<height>200</height>
	<selectors type="json">
	{
		"{submitButton}" : "[data-submit-button]",
		"{cancelButton}" : "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_MOVE_TO_NEW_CATEGORY_TITLE'); ?></title>
	<content>
		<p class="mt-10 mb-20 ml-10 mr-10"><?php echo JText::_('COM_EASYBLOG_MOVE_TO_NEW_CATEGORY_CONTENT'); ?></p>

		<div class="ml-10 mr-10">
			<?php echo EB::populateCategories('', '', 'select', 'move_category', '', false, true, false, array(), 'class="form-control"'); ?>
		</div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_MOVE_POSTS_BUTTON'); ?>
	</buttons>
</dialog>
