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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_GENERAL_TITLE', '', '/administrators/integrations/Integrating-with-JomSocial'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_jomsocial_privacy', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_PRIVACY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_toolbar', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_TOOLBAR'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_jomsocial_messaging', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MESSAGING'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_jomsocial_userpoint', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_USERPOINT'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MEDIA_TITLE', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_MEDIA_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_album', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ENABLE_MEDIA'); ?>
			</div>
		</div>
		
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_STREAM_TITLE', '', '/administrators/integrations/Integrating-with-JomSocial'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_blog_new_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_POST_ACTIVITY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_rss_import_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_RSS_IMPORT_NEW_POST_ACTIVITY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_blog_update_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UPDATE_POST_ACTIVITY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_unpublish_remove_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_UNPUBLISH_POST_REMOVE_ACTIVITY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_comment_new_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_NEW_COMMENT_ACTIVITY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_feature_blog_activity', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_FEATURED_BLOG_ACTIVITY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_submit_content', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_SUBMIT_CONTENT'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_show_category', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_DISPLAY_CATEGORY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_activity_likes', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_LIKES'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_jomsocial_activity_comments', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT'); ?>

				<?php echo $this->fd->html('settings.text', 'integrations_jomsocial_blogs_length', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_LENGTH', '', [
					'postfix' => 'COM_EASYBLOG_CHARACTERS',
					'size' => 5
				]); ?>

				<?php echo $this->fd->html('settings.text', 'integrations_jomsocial_comments_length', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_COMMENT_LENGTH', '', [
					'postfix' => 'COM_EASYBLOG_CHARACTERS',
					'size' => 5
				]); ?>

				<?php echo $this->fd->html('settings.text', 'jomsocial_blog_title_length', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JOMSOCIAL_ACTIVITY_BLOG_TITLE_LENGTH', '', [
					'postfix' => 'COM_EASYBLOG_CHARACTERS',
					'size' => 5
				]); ?>

			</div>
		</div>
	</div>
</div>
