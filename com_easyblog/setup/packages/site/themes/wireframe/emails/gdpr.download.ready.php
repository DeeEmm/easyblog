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
<?php echo $this->fd->html('email.heading', 'COM_EB_MAIL_TEMPLATE_GDPR_DOWNLOAD_HEADING', 'COM_EB_MAIL_TEMPLATE_GDPR_DOWNLOAD_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', 'COM_EB_MAIL_TEMPLATE_GDPR_DOWNLOAD_READY', 'clear'); ?>

<?php echo $this->fd->html('email.button', 'COM_EB_GDPR_DOWNLOAD', $templatePreview ? 'javascript:void(0);' : $downloadLink, 'primary'); ?>
