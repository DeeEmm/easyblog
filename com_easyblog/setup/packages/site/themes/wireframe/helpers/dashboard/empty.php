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
<div class="eb-dashboard-empty">
	<div class="eb-dashboard-empty__content">
		<?php if ($icon) { ?>
			<?php echo $this->fd->html('icon.font', 'eb-dashboard-empty__icon t-mb--lg ' . $icon); ?>
		<?php } ?>

		<div class="eb-dashboard-empty__text">
			<b><?php echo JText::_($title);?></b>

			<?php if ($description) { ?>
			<p>
				<?php echo JText::_($description); ?>
			</p>
			<?php } ?>

			<?php if ($button) { ?>
			<div>
				<?php echo $button; ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>