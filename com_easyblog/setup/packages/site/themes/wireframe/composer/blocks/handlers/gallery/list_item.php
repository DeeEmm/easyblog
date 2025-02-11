<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-list-item eb-gallery-list-item" data-eb-gallery-list-item>
	<div class="row-table">
		<div class="col-cell cell-tight eb-gallery-list-item-handle"><i class="fdi fa fa-ellipsis-v"></i></div>
		<div class="col-cell cell-tight eb-gallery-list-item-icon"><img data-eb-gallery-list-item-icon /></div>
		<div class="col-cell cell-ellipse"><span class="eb-gallery-list-item-name" data-eb-gallery-list-item-title></div>
		<div class="col-cell cell-tight">
			<span class="label label-primary eb-gallery-list-item-primary-label"><?php echo JText::_('COM_EASYBLOG_PRIMARY_LABEL'); ?></span>
		</div>
	</div>
</div>