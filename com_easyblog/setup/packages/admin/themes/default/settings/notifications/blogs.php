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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_TITLE'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'notification_autosubscribe', 'COM_EB_SETTINGS_NOTIFICATIONS_AUTOSUBSCRIBE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_blogadmin', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_ADMIN'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_allmembers', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_ALL_MEMBERS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_blogsubscriber', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_SUBSCRIBERS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_categorysubscriber', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_BLOGS_CATEGORIES'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_sitesubscriber', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_SITE_SUBSCRIBERS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_teamsubscriber', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_TEAM_SUBSCRIBERS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_approval', 'COM_EB_SETTINGS_NOTIFICATIONS_APPROVAL'); ?>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_CONTENT_SETTINGS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'notification_blog_truncate', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_TRUNCATE'); ?>

				<?php echo $this->fd->html('settings.text', 'notification_blog_truncate_limit', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_BLOG_TRUNCATE_LIMIT', '', [
					'postfix' => 'COM_EASYBLOG_CHARACTERS',
					'size' => 5
				]); ?>

				<?php echo $this->fd->html('settings.text', 'main_mailtitle_length', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TITLE_LENGTH', '', [
					'postfix' => 'COM_EASYBLOG_CHARACTERS',
					'size' => 5
				]); ?>
			</div>
		</div>
	</div>
</div>
