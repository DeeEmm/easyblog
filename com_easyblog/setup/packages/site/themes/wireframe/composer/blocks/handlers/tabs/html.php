<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<ul class="nav nav-tabs" role="tablist" data-tabs-list>
	<li class="active"><a href="#firsttab" role="tab" data-bp-toggle="tab" contenteditable="true"><?php echo JText::_('COM_EASYBLOG_BLOCK_TABS_DEFAULT_TITLE'); ?></a></li>
</ul>
<div class="tab-content" data-tabs-content>
	<div class="tab-pane active" id="firsttab" contenteditable="false">
		<div class="ebd-nest" data-type="block" data-tab-wrapper="">
			<?php echo EB::blocks()->renderEditableBlock(EB::blocks()->createBlock('text', array(), array('nested' => true)));?>
		</div>
	</div>
</div>
