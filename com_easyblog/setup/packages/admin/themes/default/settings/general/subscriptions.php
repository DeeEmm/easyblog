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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_TITLE', 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_sitesubscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SITE_SUBSCRIPTIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_subscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOG_SUBSCRIPTIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_bloggersubscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOGGER_SUBSCRIPTIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_categorysubscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_CATEGORY_SUBSCRIPTIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_teamsubscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TEAM_SUBSCRIPTIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_allowguestsubscribe', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_TO_SUBSCRIBE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_subscription_confirmation', 'COM_EASYBLOG_SETTINGS_NOTIFY_USER_SUBSCRIPTIONS_CONFIRMATION'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_subscription_admin_notification', 'COM_EASYBLOG_SETTINGS_NOTIFY_ADMIN_NEW_SUBSCRIPTIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_subscription_author_notification', 'COM_EASYBLOG_SETTINGS_NOTIFY_AUTHOR_NEW_SUBSCRIPTIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_subscription_author_post_notification', 'COM_EASYBLOG_SETTINGS_NOTIFY_AUTHOR_POST_NEW_SUBSCRIPTIONS'); ?>
			</div>
		</div>
	</div>

	<div class="space-y-md">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_AGREEMENT', 'COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_AGREEMENT_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_subscription_agreement', 'COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_REQUIRE_USER_TO_AGREE'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_AGREEMENT_MESSAGE', 'main_subscription_agreement_message'); ?>

					<div class="col-md-7">
						<textarea name="main_subscription_agreement_message" id="main_subscription_agreement_message" class="form-control"><?php echo $this->config->get('main_subscription_agreement_message');?></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
