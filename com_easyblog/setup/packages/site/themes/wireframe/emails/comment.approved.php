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

<?php echo $this->fd->html('email.heading', 'COM_EB_MAIL_TEMPLATE_NEW_COMMENT_APPROVED_HEADING', 'COM_EB_MAIL_TEMPLATE_NEW_COMMENT_APPROVED_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? JText::sprintf('COM_EB_MAIL_TEMPLATE_NEW_COMMENT_MESSAGE_APPROVED', 'Blog title', 'https://site.com') : JText::sprintf('COM_EB_MAIL_TEMPLATE_NEW_COMMENT_MESSAGE_APPROVED', $blogTitle, $blogLink), 'clear'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? 'Lorem Ipsum is simply dummy text of the printing and typesetting industry' : $commentContent, 'box'); ?>

<?php echo $this->fd->html('email.button', 'COM_EASYBLOG_NOTIFICATION_VIEW_COMMENT', $templatePreview ? 'javascript:void(0);' : $commentLink, 'primary'); ?>
