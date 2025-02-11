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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SETTINGS_TITLE'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'custom_email_as_admin', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_USE_CUSTOM_EMAILS_AS_ADMIN', '', 'data-custom-email'); ?>

				<div class="form-group <?php echo $this->config->get('custom_email_as_admin') ? '' : 'hide';?>" data-custom-email-input>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS', 'notification_email'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'notification_email', $this->config->get('notification_email', '')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'custom_email_logo', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_USE_CUSTOM_EMAILS_LOGO', '', 'data-custom-email-logo'); ?>

				<div class="form-group <?php echo $this->config->get('custom_email_logo') ? '' : 'hide'; ?>" data-email-logo-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_CUSTOM_EMAILS_LOGO', 'email_logo'); ?>

					<div class="col-md-7" data-email-logo data-id="" data-default-email-logo="<?php echo EB::getLogo('email', true); ?>">
						<div class="mb-20">
							<div class="eb-img-holder">
								<div class="eb-img-holder__remove" data-email-logo-restore-default-wrap <?php echo EB::hasOverrideLogo('email') ? '' : 'style="display: none;'; ?>>
									<a href="javascript:void(0);" class="" data-email-logo-restore-default-button>
										<i class="fdi fa fa-times"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_REMOVE'); ?>
									</a>
								</div>
								<img src="<?php echo EB::getLogo('email'); ?>" width="200" data-email-logo-image />
							</div>
						</div>
						<div>
							<input type="file" name="email_logo" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_SETTINGS_NOTIFICATIONS_CRONJOB'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_mailqueueonpageload', 'COM_EASYBLOG_SETTINGS_WORKFLOW_MAILSPOOL_SENDMAIL_ON_PAGE_LOAD'); ?>

				<?php echo $this->fd->html('settings.text', 'main_mail_total', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_TOTAL_EMAILS_AT_A_TIME', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_TOTAL_EMAILS_AT_A_TIME_DESC', [
					'postfix' => 'COM_EASYBLOG_EMAILS',
					'size' => 5
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_cron_secure', 'COM_EB_SETTINGS_NOTIFICATIONS_CRONJOB_USE_SECURE', '', 'data-cron-secure'); ?>

				<div class="form-group <?php echo $this->config->get('main_cron_secure') ? '' : 'hide';?>" data-cron-secure-key>
					<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_NOTIFICATIONS_CRONJOB_SECURE_KEY', 'main_cron_secure_key'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'main_cron_secure_key', $this->config->get('main_cron_secure_key', '')); ?>

						<div class="help-block">
							<?php echo JText::_('COM_EB_SETTINGS_NOTIFICATIONS_SECURE_CRON_KEY_INFO'); ?>
						</div>

						<?php if ($this->config->get('main_cron_secure_key')) { ?>
						<div class="row">
							<div class="col-sm-12">
								<div class="input-group">
									<span class="input-group-addon"><i class="fdi fa fa-globe"></i></span>
									<input type="text" class="form-control text-center" value="<?php echo JURI::root() . 'index.php?option=com_easyblog&task=cron&phrase=' . $this->config->get('main_cron_secure_key');?>">
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_SENDER_SETTINGS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_NAME', 'notification_from_name'); ?>
					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'notification_from_name', $this->config->get('notification_from_name', $this->jconfig->get('fromname')), 'notification_from_name'); ?>
					</div>
				</div>
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAILS_SEND_FROM_EMAIL', 'notification_from_email'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'notification_from_email', $this->config->get('notification_from_email', $this->jconfig->get('mailfrom')), 'notification_from_email'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
