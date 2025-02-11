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
<div class="eb-help-resize">
	<span><?php echo JText::_('COM_EASYBLOG_FONT_SIZE'); ?>:</span>
	<a href="javascript:void(0);" data-font-resize data-operation="increase" data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_FONT_LARGER', true);?>" data-fd-tooltip-placement="top">
		&plus;
	</a>
	<a href="javascript:void(0);" data-font-resize data-operation="decrease" data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_FONT_SMALLER', true); ?>" data-fd-tooltip-placement="top">
		&ndash;
	</a>
</div>