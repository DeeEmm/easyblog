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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_TITLE', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_twitter_cards', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_CARDS_ENABLE'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_twitter_cards_type', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_IMAGE_CARD_TYPE', [
					'summary_large_image' => 'COM_EASYBLOG_INTEGRATIONS_TWITTER_IMAGE_CARD_SUMMARY_LARGE',
					'summary' => 'COM_EASYBLOG_INTEGRATIONS_TWITTER_IMAGE_CARD_SUMMARY'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'main_twitter_button_via_screen_name', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_VIA_SCREEN_NAME', '', '', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_VIA_SCREEN_NAME_EXAMPLE')); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_twitter_opengraph_imageavatar', 'COM_EB_SETTINGS_SOCIALSHARE_TWITTER_OPENGRAPH_IMAGEAVATAR'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS'); ?>

			<div class="panel-body">
				<p><?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_NOTE');?></p>

				<?php echo $this->fd->html('settings.toggle', 'main_twitter_analytics', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_ENABLE'); ?>
			</div>
		</div>

	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_TITLE', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrations_twitter_microblog', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_ENABLE'); ?>

				<?php echo $this->fd->html('settings.textarea', 'integrations_twitter_microblog_hashes', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_SEARCH_HASHTAGS', '', '', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_SEARCH_HASHTAGS_INSTRUCTIONS'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_CATEGORY', 'integrations_twitter_microblog_category'); ?>

					<div class="col-md-7">
						<?php echo EB::populateCategories('', '', 'select', 'integrations_twitter_microblog_category', $this->config->get('integrations_twitter_microblog_category'), true); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.dropdown', 'integrations_twitter_microblog_publish', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_PUBLISH_STATE', [
					'0' => 'COM_EASYBLOG_UNPUBLISHED_OPTION',
					'1' => 'COM_EASYBLOG_PUBLISHED_OPTION',
					'2' => 'COM_EASYBLOG_SCHEDULED_OPTION',
					'3' => 'COM_EASYBLOG_DRAFT_OPTION'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_twitter_microblog_frontpage', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_FRONTPAGE'); ?>
			</div>
		</div>
	</div>
</div>
