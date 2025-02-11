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
<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
	<div class="space-y-md">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING_OPTIONS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'main_remotepublishing_mailbox_format', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT', [
						'html' => 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_HTML_OPTION',
						'plain' => 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_PLAINTEXT_OPTION'
				]); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SELECT_USER', 'main_remotepublishing_mailbox_userid'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.user', 'main_remotepublishing_mailbox_userid', $this->config->get('main_remotepublishing_mailbox_userid'), 'main_remotepublishing_mailbox_userid', ['columns' => 12]); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'main_remotepublishing_mailbox_syncuser', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAP_USERS_EMAIL'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_remotepublishing_mailbox_type', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTYPE', [
						'intro' => 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTTYPE_INTRO',
						'content' => 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTTYPE_CONTENT'
				]); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_CATEGORY', 'main_remotepublishing_mailbox_categoryid'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.browseCategory', 'main_remotepublishing_mailbox_categoryid', $this->config->get('main_remotepublishing_mailbox_categoryid'), 'main_remotepublishing_mailbox_categoryid', ['columns' => 12]); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.dropdown', 'main_remotepublishing_mailbox_publish', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_STATE', [
						'0' => 'COM_EASYBLOG_UNPUBLISHED_OPTION',
						'1' => 'COM_EASYBLOG_PUBLISHED_OPTION',
						'2' => 'COM_EASYBLOG_SCHEDULED_OPTION',
						'3' => 'COM_EASYBLOG_DRAFT_OPTION'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_remotepublishing_mailbox_privacy', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_PRIVACY', [
						'0' => 'COM_EASYBLOG_PRIVACY_ALL_OPTION',
						'1' => 'COM_EASYBLOG_PRIVACY_REGISTERED_OPTION'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_remotepublishing_mailbox_frontpage', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FRONTPAGE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_remotepublishing_mailbox_image_attachment', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_ENABLE_ATTACHMENT'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_remotepublishing_mailbox_blogimage', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_ENABLE_BLOGIMAGE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_remotepublishing_autoposting', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_AUTOPOST'); ?>
			</div>
		</div>
	</div>
</div>
