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
<form name="adminForm" action="index.php" method="post" class="adminForm" id="adminForm">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_TWITTER_APP_SETTINGS', 'COM_EASYBLOG_AUTOPOST_TWITTER_APP_SETTINGS_INFO', '/administrators/autoposting/twitter-autoposting'); ?>

				<div class="panel-body">
					<?php echo $this->fd->html('settings.toggle', 'integrations_twitter', 'COM_EASYBLOG_AUTOPOST_TWITTER_ENABLE'); ?>

					<div class="form-group" data-facebook-api>
						<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_SOCIALSHARE_TWITTER_OAUTH_CALLBACK_URL', 'integrations_twitter_oauth_callback_url'); ?>

						<div class="col-md-7">
							<p>Effective <b>June 12th</b>, Twitter has enforced <a href="https://twittercommunity.com/t/action-required-sign-in-with-twitter-users-must-whitelist-callback-urls/105342" target="_blank">Callback URLs to be whitelisted</a>. You will need to copy the links below and add it under the valid Callback URLs section of the Twitter app.</p>

							<?php foreach ($oauthURIs as $oauthURI) { ?>
							<div class="t-mb--sm">
								<?php echo $this->fd->html('form.textcopy', 'integrations_twitter_oauth_callback_url', $oauthURI); ?>
							</div>
							<?php } ?>
						</div>
					</div>	

					<div class="form-group" data-twitter-api>
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_API_KEY', 'integrations_twitter_api_key'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'integrations_twitter_api_key', $this->config->get('integrations_twitter_api_key', ''), 'integrations_twitter_api_key', [
								'help' => 'https://stackideas.com/docs/easyblog/administrators/autoposting/twitter-autoposting'
							]); ?>
						</div>
					</div>

					<div class="form-group" data-twitter-secret>
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_SECRET_KEY', 'integrations_twitter_secret_key'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'integrations_twitter_secret_key', $this->config->get('integrations_twitter_secret_key', ''), 'integrations_twitter_secret_key', [
								'help' => 'https://stackideas.com/docs/easyblog/administrators/autoposting/twitter-autoposting'
							]); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_AUTOPOSTING_TWITTER_ACCESS', 'twitter_access'); ?>

						<div class="col-md-7">
							<?php if ($associated) { ?>
								<?php echo $client->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=twitter', true);?>
							<?php } else { ?>

								<?php if ($this->config->get('integrations_twitter_secret_key') && $this->config->get('integrations_twitter_api_key')) { ?>
									<?php echo $client->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=twitter', true);?>
									<div class="mt-10">
										<?php echo JText::_('COM_EB_INTEGRATIONS_TWITTER_ACCESS_DESC');?>
									</div>
								<?php } else { ?>
									<?php echo JText::_('COM_EB_AUTOPOSTING_SAVE_SETTING_NOTICE');?>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER', 'COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_INFO'); ?>

				<div class="panel-body">
					<?php echo $this->fd->html('settings.toggle', 'integrations_twitter_shorten_url', 'COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_ENABLE'); ?>

					<div class="form-group" data-twitter-secret>
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_APIKEY', 'integrations_twitter_urlshortener_apikey'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'integrations_twitter_urlshortener_apikey', $this->config->get('integrations_twitter_urlshortener_apikey', ''), 'integrations_twitter_urlshortener_apikey', [
								'help' => 'https://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-twitter-autoposting'
							]); ?>
						</div>
					</div>
				</div>
			</div>

		</div>

		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_TWITTER', 'COM_EASYBLOG_AUTOPOST_TWITTER_INFO'); ?>
				
				<div class="panel-body">
					<?php echo $this->fd->html('settings.toggle', 'integrations_twitter_centralized', 'COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_twitter_centralized_auto_post', 'COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_twitter_centralized_send_updates', 'COM_EASYBLOG_AUTOPOST_ON_UPDATES'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_twitter_centralized_and_own', 'COM_EASYBLOG_TWITTER_ALLOW_AUTHOR_USE_OWN_ACCOUNT'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_twitter_upload_image', 'COM_EASYBLOG_TWITTER_AUTOPOST_UPLOAD_IMAGE'); ?>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE', 'main_twitter_message'); ?>

						<div class="col-md-7">
							<textarea name="main_twitter_message" id="main_twitter_message" class="form-control"><?php echo $this->config->get('main_twitter_message', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_STRING'));?></textarea>
							<br />
							<div><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_MESSAGE_DESC');?></div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
</form>
