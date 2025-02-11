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

<?php echo $this->fd->html('email.heading', 'COM_EASYBLOG_MAIL_TEMPLATE_SUBSCRIPTION_CONFIRMATION_HEADING', 'COM_EASYBLOG_MAIL_TEMPLATE_SUBSCRIPTION_CONFIRMATION_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content',
	$templatePreview ? JText::sprintf('COM_EASYBLOG_NOTIFICATION_SUBSCRIBE_SITE', '<a href="javascript:void(0);">Preview Site</a>') : JText::sprintf('COM_EASYBLOG_NOTIFICATION_SUBSCRIBE_' . strtoupper($type), '<a href="' . $targetlink  . '">' . $target . '</a>'), 'clear', ['spacer' => false]); ?>

<?php echo $this->fd->html('email.content', '<small>' . JText::_('COM_EASYBLOG_NOTIFICATION_SUBSCRIBE_CONFIRMATION_NOTICE') . '</small>', 'clear'); ?>
