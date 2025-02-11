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
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_SETTINGS', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_INFO', '/administrators/autoposting/facebook-autoposting'); ?>

				<div class="panel-body">
					<?php echo $this->fd->html('settings.toggle', 'integrations_facebook', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_ENABLE'); ?>

					<div class="form-group" data-facebook-api>
						<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_SOCIALSHARE_FACEBOOK_OAUTH_REDIRECT_URI', 'integrations_facebook_oauth_redirect_uri'); ?>

						<div class="col-md-7">
							<p class="t-mb--sm"><?php echo JText::_('COM_EB_SETTINGS_SOCIALSHARE_FACEBOOK_OAUTH_REDIRECT_URI_INFO'); ?></p>
							<?php foreach ($oauthURIs as $oauthURI) { ?>
								<div class="t-mb--sm">
									<?php echo $this->fd->html('form.textcopy', 'integrations_facebook_oauth_redirect_uri', $oauthURI); ?>
								</div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group" data-facebook-api>
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID', 'integrations_facebook_api_key'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'integrations_facebook_api_key', $this->config->get('integrations_facebook_api_key', ''), 'integrations_facebook_api_key', [
								'help' => 'https://stackideas.com/docs/easyblog/administrators/autoposting/facebook-autoposting'
							]); ?>
						</div>
					</div>

					<div class="form-group" data-facebook-secret>
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_SECRET_KEY', 'integrations_facebook_secret_key'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'integrations_facebook_secret_key', $this->config->get('integrations_facebook_secret_key', ''), 'integrations_facebook_secret_key', [
								'help' => 'https://stackideas.com/docs/easyblog/administrators/autoposting/facebook-autoposting'
							]); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_SOCIALSHARE_FACEBOOK_SCOPE_PERMISSIONS', 'integrations_facebook_scope_permissions'); ?>

						<div class="col-md-7">
							<?php echo JText::_('COM_EB_SETTINGS_SOCIALSHARE_FACEBOOK_SCOPE_PERMISSIONS_INFO'); ?>

							<?php echo $this->html('form.scopes', 'integrations_facebook_scope_permissions[]', 'integrations_facebook_scope_permissions', $selectedScopePermissions); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_AUTOPOSTING_FACEBOOK_ACCESS', 'facebook_access'); ?>

						<div class="col-md-7">
							<?php if ($associated) { ?>
								<div>
									<div style="margin-top:5px;">
										<?php echo $client->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=facebook', true);?>
									</div>

									<div style="margin:15px 0 8px 0;border: 1px dashed #d7d7d7;padding: 20px;">
										<p>
											<?php echo JText::_('COM_EASYBLOG_FACEBOOK_EXPIRE_TOKEN');?> <b><?php echo $expire; ?></b>.
										</p>
									</div>
								</div>
							<?php } else { ?>

								<?php if ($this->config->get('integrations_facebook_secret_key') && $this->config->get('integrations_facebook_api_key')) { ?>
									<?php echo $client->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=facebook', true);?>

									<div class="mt-10">
										<?php echo JText::_('COM_EB_INTEGRATIONS_FACEBOOK_ACCESS_DESC');?>
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
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_PAGES', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_PAGES_INFO'); ?>

				<div class="panel-body">
					<?php if ($associated) { ?>
						<?php echo $this->fd->html('settings.toggle', 'integrations_facebook_impersonate_page', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_PAGE_IMPERSONATION'); ?>

						<div class="form-group">
							<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_PAGE', 'integrations_facebook_page_id'); ?>

							<div class="col-md-7">
								<?php if ($pages) { ?>
								<select name="integrations_facebook_page_id[]" id="integrations_facebook_page_id" class="form-control" multiple="multiple">
									<?php foreach ($pages as $page) { ?>
									<option value="<?php echo $page->id;?>" <?php echo ($storedPages && in_array($page->id, $storedPages)) ? ' selected="selected"' : '';?>>
										<?php echo $page->name;?>
									</option>
									<?php } ?>
								</select>
								<?php } ?>
							</div>
						</div>
					<?php } else { ?>
					<div class="form-group">
						<div>
							<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_PAGES_UNAVAILABLE');?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_GROUPS', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_GROUPS_INFO'); ?>

				<div class="panel-body">
					<?php if ($associated) { ?>
						<?php echo $this->fd->html('settings.toggle', 'integrations_facebook_impersonate_group', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_GROUP'); ?>

						<div class="form-group">
							<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_GROUPS', 'integrations_facebook_group_id'); ?>

							<div class="col-md-7">
								<?php if ($groups) { ?>
								<select name="integrations_facebook_group_id[]" id="integrations_facebook_group_id" class="form-control" multiple="multiple" size="10">
									<?php foreach ($groups as $group) { ?>
									<option value="<?php echo $group->id;?>" <?php echo in_array($group->id, $storedGroups) ? ' selected="selected"' : '';?>>
										<?php echo $group->name;?>
									</option>
									<?php } ?>
								</select>
								<?php } ?>
							</div>
						</div>
					<?php } else { ?>
					<div class="form-group">
						<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_GROUPS_UNAVAILABLE');?>
					</div>
					<?php } ?>
				</div>
			</div>

		</div>

		<div class="col-lg-6">
			<div class="panel">
				<div class="panel-head">
					<b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_GENERAL'); ?></b>
					<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_GENERAL_INFO'); ?></div>
				</div>

				<div class="panel-body">
					<?php echo $this->fd->html('settings.toggle', 'integrations_facebook_centralized_auto_post', 'COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_facebook_centralized_send_updates', 'COM_EASYBLOG_AUTOPOST_ON_UPDATES'); ?>

					<?php echo $this->fd->html('settings.toggle', 'integrations_facebook_introtext_message', 'COM_EB_AUTOPOST_FACEBOOK_CONTENT_AS_MESSAGE'); ?>

					<?php $hiddenClass = $this->config->get('integrations_facebook_introtext_message') ? '' : 'hidden'; ?>
					<div class="form-group <?php echo $hiddenClass; ?>" data-oauth-contentSource>
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_INTEGRATIONS_FACEBOOK_CONTENT_FROM', 'integrations_facebook_source'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.dropdown', 'integrations_facebook_source', $this->config->get('integrations_facebook_source'), [
								'intro' => 'COM_EASYBLOG_INTROTEXT',
								'content' => 'COM_EASYBLOG_CONTENT'
							], ['id' => 'integrations_facebook_source']); ?>
						</div>
					</div>

					<div class="form-group <?php echo $hiddenClass; ?>" data-oauth-contentLength>
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_INTEGRATIONS_FACEBOOK_CONTENT_LENGTH', 'integrations_facebook_blogs_length'); ?>

						<div class="col-md-7">
							<div class="form-inline">
								<div class="form-group">
									<div class="input-group">
										<input type="text" name="integrations_facebook_blogs_length" id="integrations_facebook_blogs_length" class="form-control text-center" value="<?php echo $this->config->get('integrations_facebook_blogs_length');?>" size="5" />
										<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_CHARACTERS');?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
</form>
