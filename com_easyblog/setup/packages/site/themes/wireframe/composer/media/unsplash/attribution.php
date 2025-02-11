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
<?php echo JText::_('COM_EB_UNSPLASH_PHOTO_BY'); ?> <a href="https://unsplash.com/@<?php echo $username; ?>?utm_source=<?php echo $appName; ?>&utm_medium=referral"><?php echo $name; ?></a> <?php echo JText::_('COM_EB_UNSPLASH_ON'); ?> <a href="https://unsplash.com/?utm_source=<?php echo $appName; ?>&utm_medium=referral">Unsplash</a>