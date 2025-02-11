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
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_LINKEDIN_APP_SETTINGS', 'COM_EASYBLOG_AUTOPOST_LINKEDIN_APP_SETTINGS_INFO', '/administrators/autoposting/linkedin-autoposting'); ?>

				<div class="panel-body">
					<?php echo $this->fd->html('settings.toggle', 'integrations_linkedin', 'COM_EASYBLOG_AUTOPOST_LINKEDIN_ENABLE'); ?>

					<div class="form-group" data-facebook-api>
						<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_SOCIALSHARE_LINKEDIN_OAUTH_REDIRECT_URI', 'integrations_linkedin_oauth_redirect_uri'); ?>

						<div class="col-md-7">
							<p class="t-mb--sm">You will need to copy the links below and add it under the Authorized Redirect URLs section of the LinkedIn app.</p>

							<?php if (EBR::isSefEnabled()) { ?>
								<?php foreach ($oauthURIs as $oauthURI) { ?>
								<div class="t-mb--sm">
									<?php echo $this->fd->html('form.textcopy', 'integrations_linkedin_oauth_redirect_uri', $oauthURI); ?>
								</div>
								<?php } ?>
							<?php } else { ?>
							<div style="margin:15px 0 8px 0;border: 1px dashed #FC595B;padding: 20px;color: #FC595B;">
								It seems like your site Search Engine Friendly (SEF) is disabled. In order to use LinkedIn as autoposting medium, you must first enable the SEF of your site.
							</div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_API_KEY', 'integrations_linkedin_api_key'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'integrations_linkedin_api_key', $this->config->get('integrations_linkedin_api_key', ''), 'integrations_linkedin_api_key', [
								'help' => 'https://stackideas.com/docs/easyblog/administrators/autoposting/linkedin-autoposting'
							]); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_SECRET_KEY', 'integrations_linkedin_secret_key'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'integrations_linkedin_secret_key', $this->config->get('integrations_linkedin_secret_key', ''), 'integrations_linkedin_secret_key', [
								'help' => 'https://stackideas.com/docs/easyblog/administrators/autoposting/linkedin-autoposting'
							]); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_SOCIALSHARE_LINKEDIN_SCOPE_PERMISSIONS', 'integrations_linkedin_scope_permissions'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.scopes', 'integrations_linkedin_scope_permissions[]', 'integrations_linkedin_scope_permissions', $selectedScopePermissions, 'linkedin'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_ACCESS', 'linkedin_access'); ?>

						<div class="col-md-7">
							<?php if ($associated) { ?>
								<div>
									<div style="margin-top:5px;">
										<?php echo $client->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=linkedin', true);?>
									</div>
								</div>
							<?php } else { ?>
								<?php if ($this->config->get('integrations_linkedin_api_key') && $this->config->get('integrations_linkedin_secret_key')) { ?>
									<?php echo $client->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=linkedin', true);?>

									<div class="mt-10">
										<?php echo JText::_('COM_EB_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?>
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
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES', 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_INFO'); ?>

				<div class="panel-body">
					<?php if ($companies && !empty($companies)){ ?>
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_SELECT_COMPANY', 'integrations_linkedin_company'); ?>

						<div class="col-md-7">
							<select name="integrations_linkedin_company[]" id="integrations_linkedin_company" multiple style="height: 150px;">
								<?php foreach ($companies as $company) { ?>
								<option value="<?php echo $company->id;?>"<?php echo in_array($company->id , $storedCompanies ) ? ' selected="selected"' : '';?>><?php echo $company->name;?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<?php } else { ?>
						<div>
							<?php echo JText::sprintf('COM_EB_AUTOPOSTING_LINKEDIN_COMPANIES_UNAVAILABLE_REVIEW_REQUIRED', '<a href="https://stackideas.com/docs/easyblog/administrators/autoposting/linkedin-autoposting" target="_blank">', '</a>');?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOSTING_GENERAL_TITLE', 'COM_EASYBLOG_AUTOPOSTING_GENERAL_DESC'); ?>

				<div class="panel-body">
					<?php echo $this->fd->html('settings.toggle', 'integrations_linkedin_centralized', 'COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_linkedin_centralized_auto_post', 'COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_linkedin_centralized_send_updates', 'COM_EASYBLOG_AUTOPOST_ON_UPDATES'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_linkedin_centralized_and_own', 'COM_EASYBLOG_LINKEDIN_ALLOW_AUTHOR_USE_OWN_ACCOUNT'); ?>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_DEFAULT_MESSAGE', 'main_linkedin_message'); ?>

						<div class="col-md-7">
							<textarea name="main_linkedin_message" id="main_linkedin_message" class="form-control" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('main_linkedin_message', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_STRING'));?></textarea>
							<div class="mt-10"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_MESSAGE_DESC');?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
</form>
