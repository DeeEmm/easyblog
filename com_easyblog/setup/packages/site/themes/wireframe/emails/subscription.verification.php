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
<?php echo $this->fd->html('email.heading', 'COM_EB_MAIL_TEMPLATE_SUBSCRIBE_VERIFICATION_HEADING', 'COM_EB_MAIL_TEMPLATE_SUBSCRIBE_VERIFICATION_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? JText::sprintf('COM_EB_MAIL_TEMPLATE_SUBSCRIBE_VERIFICATION_CONTENT', "Preview Site") : JText::sprintf('COM_EB_MAIL_TEMPLATE_SUBSCRIBE_VERIFICATION_CONTENT', $target), 'clear'); ?>

<?php echo $this->fd->html('email.button', 'COM_EB_CONFIRM_SUBSCRIPTION', $templatePreview ? 'javascript:void(0);' : $subscribeLink, 'primary'); ?>

<?php echo $this->fd->html('email.content', '<small>' . JText::_('COM_EB_NOTIFICATION_SUBSCRIBE_VERIFICATION_NOTICE') . '</small>', 'clear'); ?>
