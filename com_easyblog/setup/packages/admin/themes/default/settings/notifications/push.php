<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_PUSH_GENERAL', '', 'administrators/configuration/onesignal-push-notification'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_ONESIGNAL_ENABLE_INTEGRATIONS', 'onesignal_enabled'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'onesignal_enabled', $this->config->get('onesignal_enabled')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_ONESIGNAL_DISPLAY_WELCOME_MESSAGE', 'onesignal_show_welcome'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'onesignal_show_welcome', $this->config->get('onesignal_show_welcome')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_ONESIGNAL_APP_ID', 'onesignal_app_id'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'onesignal_app_id', $this->config->get('onesignal_app_id', ''), 'onesignal_app_id'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_ONESIGNAL_REST_KEY', 'onesignal_rest_key'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'onesignal_rest_key', $this->config->get('onesignal_rest_key', ''), 'onesignal_rest_key'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_ONESIGNAL_SAFARI_WEB_ID', 'onesignal_safari_id'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'onesignal_safari_id', $this->config->get('onesignal_safari_id', ''), 'onesignal_safari_id'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_ONESIGNAL_SUBDOMAIN', 'onesignal_subdomain'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'onesignal_subdomain', $this->config->get('onesignal_subdomain', ''), 'onesignal_subdomain'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
	</div>
</div>
