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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING', '', '/administrators/remote-publishing/email-publishing'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST', 'test-button'); ?>

					<div class="col-md-7">
						<button type="button" class="btn btn-default btn-sm" data-test-mailbox><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST_BUTTON');?></button>
						<span data-mailbox-test-result></span>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'main_remotepublishing_mailbox', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING'); ?>

				<?php echo $this->fd->html('settings.text', 'main_remotepublishing_mailbox_prefix', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PREFIX'); ?>

				<?php echo $this->fd->html('settings.text', 'main_remotepublishing_mailbox_run_interval', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_RUN_INTERVAL', '', [
					'postfix' => 'COM_EASYBLOG_MINUTES',
					'size' => 5,
					'class' => 'text-center'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'main_remotepublishing_mailbox_fetch_limit', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FETCH_LIMIT', '', [
					'postfix' => 'COM_EASYBLOG_EMAILS',
					'size' => 5,
					'class' => 'text-center'
				]); ?>
			</div>
		</div>
	</div>

	<div class="space-y-md">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_SERVER_SETTINGS', '', '/administrators/remote-publishing/email-publishing'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'main_remotepublishing_mailbox_provider', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PROVIDER', [
					'' => 'COM_EASYBLOG_MAILBOX_PROVIDER_SELECT_PROVIDER',
					'gmail' => 'COM_EASYBLOG_MAILBOX_PROVIDER_GMAIL',
					'hotmail' => 'COM_EASYBLOG_MAILBOX_PROVIDER_HOTMAIL',
					'others' => 'COM_EASYBLOG_MAILBOX_PROVIDER_OTHERS'
				], '', 'data-mail-provider'); ?>

				<?php echo $this->fd->html('settings.text', 'main_remotepublishing_mailbox_username', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_USERNAME', '', [
					'attributes' => 'data-mailbox-username'
				]); ?>

				<?php echo $this->fd->html('settings.password', 'main_remotepublishing_mailbox_password', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PASSWORD', '', array('attributes' => 'data-mailbox-password')); ?>

				<?php echo $this->fd->html('settings.text', 'main_remotepublishing_mailbox_remotesystemname', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SYSTEM_NAME', '', [
					'attributes' => 'data-mailbox-address'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'main_remotepublishing_mailbox_port', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PORT', '', [
					'attributes' => 'data-mailbox-port',
					'size' => ''
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_remotepublishing_mailbox_service', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE', [
					'imap' => 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_IMAP',
					'pop3' => 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_POP3'
				], '', 'data-mailbox-type'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_remotepublishing_mailbox_ssl', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SSL', '', 'data-mailbox-ssl'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_remotepublishing_mailbox_validate_cert', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_VALIDATE_CERT', '', 'data-mailbox-validate-ssl'); ?>

				<?php echo $this->fd->html('settings.text', 'main_remotepublishing_mailbox_mailboxname', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAILBOX_NAME', '', [
					'attributes' => 'data-mailbox-name',
					'size' => ''
				]); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FROM_WHITE_LIST', 'main_remotepublishing_mailbox_from_whitelist'); ?>

					<div class="col-md-7">
						<textarea class="form-control" id="main_remotepublishing_mailbox_from_whitelist" name="main_remotepublishing_mailbox_from_whitelist" data-mailbox-whitelist><?php echo $this->config->get('main_remotepublishing_mailbox_from_whitelist');?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
