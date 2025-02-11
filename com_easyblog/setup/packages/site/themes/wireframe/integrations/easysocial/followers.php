<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($legacy) { ?>
	<a href="javascript:void(0);" class="author-friend" data-es-followers-follow data-es-followers-id="<?php echo $user->id;?>">
		<span><?php echo JText::_('COM_EASYBLOG_FOLLOW_AUTHOR'); ?></span>
	</a>
<?php } else { ?>
	<?php echo ES::themes()->html('user.subscribe', $user); ?>
<?php } ?>
