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
<div class="eb-composer-fieldset-header" data-eb-composer-block-section-header>
	<strong><?php echo JText::_($title);?></strong>

	<?php if ($info) { ?>
	<small style="text-transform: lowercase;">
		<span <?php echo $counterAttr; ?>><?php echo $counter; ?></span> <?php echo JText::_($counterText);?>
	</small>
	<?php } ?>

	<i class="eb-composer-fieldset-header__icon" data-panel-icon></i>
</div>