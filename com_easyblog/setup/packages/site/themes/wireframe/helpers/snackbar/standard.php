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
<div class="eb-bar eb-bar--filter-bar">
	<div class="t-d--flex t-align-items--c sm:t-flex-direction--c t-w--100">
		<div class="t-flex-grow--1 sm:t-mb--md">
			<b><?php echo $title; ?></b>
		</div>

		<?php if ($actions) { ?>
		<div class="t-d--flex sm:t-flex-direction--c sm:t-w--100">
			<?php foreach ($actions as $action) { ?>
				<?php echo $action; ?>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>
