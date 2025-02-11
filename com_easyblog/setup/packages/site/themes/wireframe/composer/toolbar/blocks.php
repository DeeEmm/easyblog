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
<div class="btn-group" data-toolbar-blocks-wrapper>
	<button type="button" class="btn eb-comp-toolbar__nav-btn" data-toolbar-blocks
		data-eb-provide="tooltip"
		data-html="1"
		data-title="<?php echo JText::_('COM_EASYBLOG_INSERT_BLOCK');?><?php echo $this->html('composer.shortcut', ['shift', 'i']); ?>"
		data-placement="bottom"
	>
		<i class="fdi fa fa-layer-group"></i>
	</button>
</div>
