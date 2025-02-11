<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_TITLE'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'notification_commentadmin', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_ADMIN'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_comment_all_members', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_ALL_USERS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_commentauthor', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_AUTHOR'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_commentmoderationauthor', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_PENDING_MODERATION_AUTHOR'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_commentsubscriber', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_SUBSCRIBERS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notification_commentlike', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_FOR_COMMENTS_LIKE'); ?>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_CONTENT_SETTINGS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'notification_commentruncate', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_TRUNCATE'); ?>

				<?php echo $this->fd->html('settings.text', 'notification_commenttruncate_limit', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_COMMENT_TRUNCATE_LIMIT', '', [
					'postfix' => 'COM_EASYBLOG_CHARACTERS',
					'size' => 5
				]); ?>
			</div>
		</div>
	</div>
</div>
