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
<div class="eb-bar eb-bar--snackbar">
	<div class="t-d--flex t-align-items--c sm:t-flex-direction--c t-w--100">
		<div class="t-flex-grow--1 sm:t-mb--md">
			<h2 class="eb-head-title reset-heading"><?php echo $title;?></h2>
		</div>
		<?php if ($button) { ?>
		<div>
			<a href="<?php echo $button->link;?>" class="uk-button uk-button-primary uk-button-small" <?php echo $button->attribute; ?>>
				<?php if ($button->icon) { ?>
				<i class="<?php echo $button->icon;?>"></i>
				<?php } ?>

				&nbsp;<?php echo $button->text;?>
			</a>
		</div>
		<?php } ?>
	</div>
</div>
