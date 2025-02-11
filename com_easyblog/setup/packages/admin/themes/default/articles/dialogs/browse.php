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
	<width>700</width>
	<height>500</height>
	<selectors type="json">
	{
		"{cancelButton}": "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EB_BROWSE'); ?></title>
	<content type="text"><?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=articles&tmpl=component&browse=1&browsefunction=insertArticle</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
	</buttons>
</dialog>
