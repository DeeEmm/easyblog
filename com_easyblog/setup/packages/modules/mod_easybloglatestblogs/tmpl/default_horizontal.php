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
<div class="mod-grid mod-grid-<?php echo $column;?>" style="width:<?php echo (100 / $columnCount);?>%;">
	<div class="mod-item">
		<?php require(JModuleHelper::getLayoutPath('mod_easybloglatestblogs', 'default_item_heading')); ?>

		<div class="eb-mod-context">
			<?php require(JModuleHelper::getLayoutPath('mod_easybloglatestblogs', 'default_item_content')); ?>
		</div>

		<?php require(JModuleHelper::getLayoutPath('mod_easybloglatestblogs', 'default_item_footer')); ?>
	</div>
</div>

<?php
if ($column < $columnCount) {
	$column++;
} else {
	$column = 1;
}
