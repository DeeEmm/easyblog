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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_ogauthor', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_USE_AUTHOR_URL'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_ogpage', 'COM_EB_SETTINGS_SOCIALSHARE_FACEBOOK_USE_AUTHOR_URL'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID', 'main_facebook_like_admin'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="main_facebook_like_admin" id="main_facebook_like_admin" class="form-control" value="<?php echo $this->config->get('main_facebook_like_admin');?>" />
							<span class="input-group-btn">
								<a href="https://stackideas.com/docs/easyblog/administrators/autoposting/obtaining-facebook-profile-id" target="_blank" class="btn btn-default">
									<i class="fdi fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.text', 'main_facebook_like_appid', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_scripts', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_opengraph', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APPEND_OPENGRAPH_HEADERS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_opengraph_imageavatar', 'COM_EB_SETTINGS_SOCIALSHARE_FACEBOOK_OPENGRAPH_IMAGEAVATAR'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_analytics', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_ENABLE'); ?>

				<div class="o-alert o-alert--warning mt-20">
					<b><?php echo JText::_('COM_EASYBLOG_NOTE');?></b><br />

					<?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_NOTE');?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_BUTTON_STYLING'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_like_faces', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_facebook_like_send', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_facebook_like_verb', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB', [
					'like' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES',
					'recommend' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_facebook_like_theme', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES', [
					'light' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT',
					'dark' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK'
				]); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_FACEBOOK_INSTANT_ARTICLE'); ?>

			<div class="panel-body">
				<p><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_FACEBOOK_INSTANT_ARTICLE_INSTRUCTIONS', 'https://stackideas.com/docs/easyblog/administrators/configuration/instant-article-configuration');?></p>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_INSTANT_ARTICLE_ID', 'facebook_instant_article_id'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="facebook_instant_article_id" id="facebook_instant_article_id" class="form-control" value="<?php echo $this->config->get('facebook_instant_article_id');?>" />
							<span class="input-group-btn">
								<a href="https://stackideas.com/docs/easyblog/administrators/configuration/instant-article-configuration" target="_blank" class="btn btn-default">
									<i class="fdi fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_INSTANT_ARTICLE_URL', 'facebook_instant_article_url'); ?>

					<div class="col-md-7">
						<div class="form-control-static"><?php echo JURI::root();?>index.php?option=com_easyblog&view=latest&format=instant</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_ADS_PLACEMENT_ID', 'facebook_ads_placement_id'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="facebook_ads_placement_id" id="facebook_ads_placement_id" class="form-control" value="<?php echo $this->config->get('facebook_ads_placement_id');?>" />
							<span class="input-group-btn">
								<a href="https://developers.facebook.com/docs/instant-articles/monetization/audience-network" target="_blank" class="btn btn-default">
									<i class="fdi fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.dropdown', 'fb_ads_density', 'COM_EASYBLOG_SETTINGS_FB_ADS_DENSITY', [
					'default' => 'COM_EASYBLOG_SETTINGS_FB_ADS_DENSITY_DEFAULT',
					'medium' => 'COM_EASYBLOG_SETTINGS_FB_ADS_DENSITY_MEDIUM',
					'low' => 'COM_EASYBLOG_SETTINGS_FB_ADS_DENSITY_LOW'
				]); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_ADS_WIDTH', 'facebook_ads_width'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="facebook_ads_width" id="facebook_ads_width" class="form-control" value="<?php echo $this->config->get('facebook_ads_width', 300);?>" />
									<span class="input-group-addon">px</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_ADS_HEIGHT', 'facebook_ads_height'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="facebook_ads_height" id="facebook_ads_height" class="form-control" value="<?php echo $this->config->get('facebook_ads_height', 250);?>" />
									<span class="input-group-addon">px</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
