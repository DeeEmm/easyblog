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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_USERS', 'COM_EASYBLOG_SETTINGS_WORKFLOW_USERS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_joomlauserparams', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_JOOMLA_USER_PARAMETERS'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_login_provider', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOGIN_PROVIDER', [
					'easyblog' => 'COM_EASYBLOG',
					'easysocial' => 'EasySocial',
					'joomla' => 'Joomla',
					'cb' => 'Community Builder',
					'jomsocial' => 'JomSocial'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_autofeatured', 'COM_EASYBLOG_SETTINGS_WORKFLOW_AUTOMATIC_FEATURE_BLOG_POST'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_bloggerlistingoption', 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_LISTINGS_OPTION'); ?>

				<?php echo $this->fd->html('settings.text', 'layout_exclude_bloggers', 'COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_USERS_FROM_BLOGGER_LISTINGS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_show_blockeduserposts', 'COM_EB_SETTINGS_USERS_SHOW_BLOCKED_USERS_POSTS'); ?>
			</div>
		</div>
	</div>

	<div class="space-y-md">
	</div>
</div>
