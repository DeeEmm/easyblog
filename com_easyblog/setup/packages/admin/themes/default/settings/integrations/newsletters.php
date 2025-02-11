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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_MAILCHIMP_INTEGRATIONS', '', '/administrators/integrations/integrating-with-mailchimp'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_EASYBLOG_MAILCHIMP_INFO', 'http://eepurl.com/ori65', 'COM_EASYBLOG_SIGNUP_WITH_MAILCHIMP', 'btn-sm', JURI::base() . 'components/com_easyblog/themes/default/images/vendors/chimp.png'); ?>

				<?php echo $this->fd->html('settings.toggle', 'subscription_mailchimp', 'COM_EASYBLOG_MAILCHIMP_ENABLE'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MAILCHIMP_APIKEY', 'subscription_mailchimp_key'); ?>
					
					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'subscription_mailchimp_key', $this->config->get('subscription_mailchimp_key', '')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MAILCHIMP_LISTID', 'subscription_mailchimp_listid'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'subscription_mailchimp_listid', $this->config->get('subscription_mailchimp_listid', '')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'mailchimp_campaign', 'COM_EASYBLOG_MAILCHIMP_SEND_NOTIFICATION'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MAILCHIMP_SENDER_NAME', 'mailchimp_from_name'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'mailchimp_from_name', $this->config->get('mailchimp_from_name', '')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MAILCHIMP_SENDER_EMAIL', 'mailchimp_from_email'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'mailchimp_from_email', $this->config->get('mailchimp_from_email', '')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SENDYAPP', '', '/administrators/integrations/integrating-with-sendy'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_EASYBLOG_SENDY_INFO', 'http://sendy.co/?ref=WDAF8', 'COM_EASYBLOG_GET_SENDY_APP'); ?>

				<?php echo $this->fd->html('settings.toggle', 'subscription_sendy', 'COM_EASYBLOG_SENDY_ENABLE'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SENDY_URL', 'subscription_sendy_url'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'subscription_sendy_url', $this->config->get('subscription_sendy_url', '')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SENDY_LISTID', 'subscription_sendy_listid'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'subscription_sendy_listid', $this->config->get('subscription_sendy_listid', '')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
