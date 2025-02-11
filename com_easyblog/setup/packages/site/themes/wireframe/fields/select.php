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
<?php if ($params->get('multiple')) { ?>
	<ul>
	<?php foreach ($items as $item) { ?>
		<li><?php echo isset($item->title) ? strip_tags($item->title) : strip_tags($item->value);?></li>
	<?php } ?>
	</ul>
<?php } else { ?>
	<?php echo isset($items[0]->title) ? strip_tags($items[0]->title) : strip_tags($items[0]->value);?>
<?php } ?>
