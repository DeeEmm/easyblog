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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SOCIAL_BUTTONS', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SOCIAL_BUTTONS_INFO', '/administrators/integrations/social-integrations'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'social_button_type', 'COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_TYPE', [
					'disabled' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_NONE',
					'internal' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_SIMPLE',
					'external' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_RESPECT_SOCIAL_SITE',
					'addthis' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_ADDTHIS',
					'sharethis' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_SHARETHIS'
				], '', 'data-social-button-type'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel <?php echo $this->config->get('social_button_type') == 'internal' ? '' : 't-hidden';?>" data-social-group="internal">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_INTERNAL_BUTTONS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'social_button_internal_size', 'COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_BUTTON_SIZE', [
					'small' => 'Small (Only Icon)',
					'large' => 'Large (Icon with text)'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_button_facebook', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_button_twitter', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_button_linkedin', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_LINKEDIN_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_button_pinterest', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_PINIT_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_button_pocket', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_POCKET_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_button_reddit', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_REDDIT_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_button_vk', 'COM_EASYBLOG_SETTINGS_VK_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_button_xing', 'COM_EASYBLOG_SETTINGS_XING_ENABLE'); ?>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_button_type') == 'internal' ? '' : 't-hidden';?>" data-social-group="internal">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_SETTINGS_YOURLS_SHORTENER', '', '/administrators/configuration/yourls'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'social_yourls_shortener', 'COM_EB_SETTINGS_ENABLE_YOURLS_SHORTENER'); ?>
				<?php echo $this->fd->html('settings.text', 'social_yourls_url', 'COM_EB_SETTINGS_YOURLS_URL'); ?>
				<?php echo $this->fd->html('settings.text', 'social_yourls_token', 'COM_EB_SETTINGS_YOURLS_SECRET_TOKEN'); ?>
				<?php echo $this->fd->html('settings.toggle', 'social_yourls_onload', 'COM_EB_SETTINGS_SHORTEN_URL_ON_PAGE_LOAD'); ?>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_button_type') == 'external' ? '' : 't-hidden';?>" data-social-group="external">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTONS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'social_button_size', 'COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_BUTTON_SIZE', [
					'small' => 'COM_EASYBLOG_SOCIAL_BUTTONS_SIZE_SMALL',
					'large' => 'COM_EASYBLOG_SOCIAL_BUTTONS_SIZE_LARGE'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_like', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_twitter_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_linkedin_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_LINKEDIN_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_pinit_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_PINIT_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_pocket_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_POCKET_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_reddit_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_REDDIT_BUTTON'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_vk', 'COM_EASYBLOG_SETTINGS_VK_ENABLE'); ?>
				<?php echo $this->fd->html('settings.text', 'main_vk_api', 'COM_EASYBLOG_SETTINGS_VK_API_ID'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_xing_button', 'COM_EASYBLOG_SETTINGS_XING_ENABLE'); ?>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_button_type') == 'sharethis' ? '' : 't-hidden';?>" data-social-group="sharethis">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_TITLE'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PROPERTY_ID', 'social_sharethis_property'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'social_sharethis_property', $this->config->get('social_sharethis_property', ''), 'social_sharethis_property'); ?>

						<div class="notice full-width"><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PUBLISHERS_INSTRUCTIONS', 'https://stackideas.com/docs/easyblog/administrators/integrations/how-to-enable-sharethis');?></div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_button_type') == 'addthis' ? '' : 't-hidden';?>" data-social-group="addthis">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_CODE', 'social_addthis_customcode'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'social_addthis_customcode', $this->config->get('social_addthis_customcode', ''), 'social_addthis_customcode'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
