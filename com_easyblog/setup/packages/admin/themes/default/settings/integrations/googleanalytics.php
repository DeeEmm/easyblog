<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->fd->html('panel.heading', 'COM_EB_GOOGLE_ANALYTICS_TITLE'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'main_google_analytics_id', 'COM_EB_GOOGLE_ANALYTICS_TRACKING_ID'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_google_analytics', 'COM_EB_GOOGLE_ANALYTICS_ENABLE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_google_analytics_script', 'COM_EB_SETTINGS_SEO_ENABLE_AMP_ANALYTICS_LOAD_SCRIPT'); ?>

				<?php echo $this->fd->html('settings.toggle', 'amp_analytics', 'COM_EB_SETTINGS_SEO_ENABLE_AMP_ANALYTICS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'facebook_google_analytics', 'COM_EB_GOOGLE_ANALYTICS_FACEBOOK_ENABLE'); ?>
			</div>
		</div>
	</div>
</div>
