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

<?php echo $this->fd->html('email.heading', 'COM_EASYBLOG_MAIL_TEMPLATE_POST_REJECTED_HEADING', 'COM_EASYBLOG_MAIL_TEMPLATE_POST_REJECTED_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_POST_REJECTED_MESSAGE', 'Blog Title') : JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_POST_REJECTED_MESSAGE', $blogTitle), 'clear', ['spacer' => false]); ?>

<?php echo $this->fd->html('email.content', 'COM_EASYBLOG_MAIL_TEMPLATE_POST_REJECTED_REASON', 'clear'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? 'This is a reject message' : $rejectMessage, 'box'); ?>

<?php echo $this->fd->html('email.button', 'COM_EASYBLOG_MAIL_TEMPLATE_REVIEW_AND_EDIT_POST', $templatePreview ? 'javascript:void(0);' : $blogEditLink, 'primary'); ?>
