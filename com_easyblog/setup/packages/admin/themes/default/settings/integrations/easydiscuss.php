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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_GENERAL_TITLE', '', '/administrators/integrations/integrating-with-easydiscuss'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_DESC', 'https://stackideas.com/easydiscuss?from=easyblog', 'COM_EASYDISCUSS_TRY_BUTTON', 'btn-sm'
										, JURI::base() . 'components/com_easyblog/themes/default/images/vendors/easydiscuss.png'); ?>

				<?php echo $this->fd->html('settings.toggle', 'integrations_ed_eb_toolbar', 'COM_EB_SETTINGS_INTEGRATIONS_EASYDISCUSS_TOOLBAR'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_easydiscuss_points', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_POINTS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_easydiscuss_badges', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_BADGES'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_TITLE', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrations_easydiscuss_notification_blog', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_NEW_BLOG'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_easydiscuss_notification_comment', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_easydiscuss_notification_rating', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYDISCUSS_NOTIFICATIONS_RATING'); ?>
			</div>
		</div>
	</div>
</div>
