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
<?php echo $this->fd->html('email.heading', 'COM_EASYBLOG_MAIL_TEMPLATE_NEW_COMMENT_HEADING', 'COM_EASYBLOG_MAIL_TEMPLATE_NEW_COMMENT_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_NEW_COMMENT_MESSAGE', 'John Doe', '13th August 2017') : JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_NEW_COMMENT_MESSAGE', $commentAuthor, $commentDate), 'clear'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s' : $commentContent, 'box'); ?>

<?php echo $this->fd->html('email.spacer'); ?>

<?php echo $this->fd->html('email.button', 'COM_EASYBLOG_NOTIFICATION_VIEW_COMMENT', $templatePreview ? 'javascript:void(0);' : $commentLink, 'primary'); ?>
