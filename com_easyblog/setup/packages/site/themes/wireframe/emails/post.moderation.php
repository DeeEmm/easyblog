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

<?php echo $this->fd->html('email.heading', 'COM_EASYBLOG_MAIL_TEMPLATE_NEW_POST_PENDING_REVIEW_HEADING', 'COM_EASYBLOG_MAIL_TEMPLATE_NEW_POST_PENDING_REVIEW_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', 'COM_EB_MAIL_TEMPLATE_NEW_POST_PENDING_REVIEW_CONTENT', 'clear'); ?>

<?php echo $this->fd->html('email.blog',
	$templatePreview ? 'Blog Title' : $blogTitle,
	$templatePreview ? $lipsum : $blogIntro,
	$templatePreview ? '13th August 2021' : $blogDate,
	$templatePreview ? 'John Doe' : $blogAuthor,
	$templatePreview ? 'javascript:void(0);' : $blogAuthorLink,
	$templatePreview ? rtrim(JURI::root(), '/') . '/media/com_easyblog/images/avatars/author.png' : $blogAuthorAvatar
); ?>

<?php echo $this->fd->html('email.spacer'); ?>

<?php echo $this->fd->html('email.button', 'COM_EASYBLOG_NOTIFICATION_REVIEW_POST', $templatePreview ? 'javascript:void(0);' : $blogLink); ?>
