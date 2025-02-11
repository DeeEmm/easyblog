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
<?php echo $this->fd->html('email.heading', 'COM_EASYBLOG_MAIL_TEMPLATE_TEAM_APPROVED_HEADING', 'COM_EASYBLOG_MAIL_TEMPLATE_TEAM_APPROVED_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? JText::sprintf('COM_EASYBLOG_NOTIFICATION_TEAM_REQUEST_APPROVED', '<b>Preview Team</b>') : JText::sprintf('COM_EASYBLOG_NOTIFICATION_TEAM_REQUEST_APPROVED', '<b>' . $teamName . '</b>'), 'clear'); ?>

<?php echo $this->fd->html('email.button', 'COM_EASYBLOG_MAIL_TEMPLATE_VIEW_TEAM', $templatePreview ? 'javascript:void(0);' : $teamLink); ?>
