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
<a href="<?php echo $url; ?>" class="btn btn-eb-<?php echo $type; ?> <?php echo $type === 'google' ? 'btn-default' : '';?> <?php echo $class; ?> <?php echo $block ? 'flex w-full' : ''; ?>" <?php echo $attributes;?>>

	<?php if ($icon) { ?>
	<i class="<?php echo $icon; ?>"></i>
	<?php } ?>

	<?php if (!$icon && $imageIcon) { ?>
		<img src="<?php echo $imageIcon; ?>" width="16" height="16" />
	<?php } ?>

	<?php if ($text) { ?>
	&nbsp; <?php echo $text; ?>
	<?php } ?>
</a>
