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
<div class="eb-entry-nav fd-cf">
	<?php if (!empty($navigation->next)) { ?>
	<div class="eb-entry-nav-prev">
		<a href="<?php echo $navigation->next->link;?>"> 
			<i class="fdi fa fa-angle-left pull-left"></i>
			<?php echo $navigation->next->title;?>
		</a>
	</div>
	<?php } ?>

	<?php if (!empty($navigation->prev)) { ?>
	<div class="eb-entry-nav-next">
		<a href="<?php echo $navigation->prev->link;?>">
			<i class="fdi fa fa-angle-right pull-right"></i>
			<?php echo $navigation->prev->title;?>
		</a>
	</div>
	<?php } ?>
</div>
