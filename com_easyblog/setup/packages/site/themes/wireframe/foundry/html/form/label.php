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
<label for="<?php echo $id;?>" class="col-md-<?php echo $columns;?> <?php echo $classes; ?>" data-uid="<?php echo $uniqueId;?>">
	<a id="<?php echo $uniqueId;?>"></a>

	<?php echo $text;?>

	<?php if ($tooltip) { ?>
	<i data-fd-popover data-fd-popover-trigger="hover" data-fd-popover-placement="top" data-fd-popover-title="<?php echo $helpTitle; ?>" data-fd-popover-content="<?php echo $helpContent;?>" class="fdi fa fa-question-circle fa-fw text-gray-500 pull-right"></i>
	<?php } ?>
</label>