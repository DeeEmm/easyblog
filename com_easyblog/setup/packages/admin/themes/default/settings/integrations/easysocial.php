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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_GENERAL', '', '/administrators/integrations/integrating-with-easySocial'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_EASYBLOG_EASYSOCIAL_INFO', 'https://stackideas.com/easysocial?from=easyblog', 'COM_EASYBLOG_SIGNUP_WITH_EASYSOCIAL', 'btn-sm', JURI::base() . 'components/com_easyblog/themes/default/images/vendors/easysocial.png'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_es_privacy', 'COM_EB_SETTINGS_INTEGRATIONS_EASYSOCIAL_PRIVACY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_es_eb_toolbar', 'COM_EB_SETTINGS_INTEGRATIONS_EASYSOCIAL_TOOLBAR'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_miniheader', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_MINI_HEADER'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_badges', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_ACHIEVEMENTS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_conversations', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_CONVERSATIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_points', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_POINTS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_editprofile', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PROFILE_MODIFY_EDIT_PROFILE_LINK'); ?>
			</div>
		</div>
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_MEDIA_TITLE', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_MEDIA_INFO', '/administrators/integrations/integrating-with-easySocial'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_album', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_ENABLE_MEDIA'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_TITLE', 'COM_EASYBLOG_EASYSOCIAL_NOTIFICATIONS_INFO', '/administrators/integrations/integrating-with-easySocial'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_notifications_newpost', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_POST'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_notifications_newcomment', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_COMMENT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_notifications_commentreply', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_COMMENT_REPLY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_notifications_ratings', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_RATING'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_notifications_reaction', 'COM_EB_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_REACTION'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_SEARCH'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrations_easysocial_indexer_newpost', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST'); ?>

				<?php echo $this->fd->html('settings.text', 'integrations_easysocial_indexer_newpost_length', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST_LENGTH', '', [
					'postfix' => 'COM_EASYBLOG_CHARACTERS',
					'size' => 5,
					'class' => 'text-center'
				]); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_SETTINGS_INTEGRATION_EASYSOCIAL_STREAM', '', '/administrators/integrations/integrating-with-easySocial'); ?>


			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'integrations_easysocial_stream_date_source', 'COM_EASYBLOG_POST_DATE_SOURCE', [
					'created' => 'COM_EASYBLOG_POST_DATE_SOURCE_CREATION',
					'modified' => 'COM_EASYBLOG_POST_DATE_SOURCE_MODIFIED',
					'publish_up' => 'COM_EASYBLOG_POST_DATE_SOURCE_PUBLISHING'
				]); ?>
			</div>
		</div>
	</div>
</div>
