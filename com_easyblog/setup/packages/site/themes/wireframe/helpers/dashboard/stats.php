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
<div>
	<a href="<?php echo $url ? $url : 'javascript:void(0);';?>" class="db-post-item">
		<div class="t-flex-grow--1 t-min-width--0 t-pr--md">
			<div class="t-d--flex t-w-100 t-align-items--c">
				<div class="t-mr--sm">
					<i class="<?php echo $icon;?> text-gray-500"></i>
				</div>
				<div class="t-min-width--0 t-flex-grow--1 t-overflow--hidden">
					<div class="t-text--truncate"><?php echo JText::_($label);?></div>
				</div>
			</div>
		</div>
		<div class="ml-auto">
			<div>
				<b><?php echo $count;?></b>
			</div>
		</div>
	</a>
</div>
