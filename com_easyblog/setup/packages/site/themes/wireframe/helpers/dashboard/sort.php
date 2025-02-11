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
<a href="javascript:void(0);" class="eb-db-sort-link" data-table-grid-sort data-column="<?php echo $column; ?>" data-ordering="<?php echo ($ordering == 'desc' || $ordering == '') ? 'asc' : 'desc'; ?>">
	<?php echo $label; ?>

	<?php if ($currentSort && $currentSort == $column) { ?>
		<i class="fa <?php echo $icon;?>"></i>
	<?php } ?>
</a>
