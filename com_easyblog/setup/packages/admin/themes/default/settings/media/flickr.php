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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_INTEGRATIONS_FLICKR', '', '/administrators/integrations/integrating-with-flickr'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_EASYBLOG_INTEGRATIONS_FLICKR_INFO'); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_media_flickr', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_FLICKR'); ?>

				<?php echo $this->fd->html('settings.text', 'integrations_flickr_api_key', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_API_KEY'); ?>

				<?php echo $this->fd->html('settings.text', 'integrations_flickr_secret_key', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_SECRET_KEY'); ?>
			</div>
		</div>
	</div>
</div>
