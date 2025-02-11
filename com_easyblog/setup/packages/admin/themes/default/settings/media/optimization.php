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
			<?php echo $this->fd->html('panel.heading', 'COM_EB_IMAGE_OPTIMIZER', '', '/administrators/configuration/setting-up-image-optimization'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_EB_IMAGE_OPTIMIZER_INFO'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_media_compression', 'COM_EB_ENABLE_IMAGE_OPTIMIZER'); ?>
				<?php echo $this->fd->html('settings.text', 'main_media_compression_key', 'COM_EB_ENABLE_IMAGE_OPTIMIZER_SERVICE_KEY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_media_compression_cron', 'COM_EB_OPTIMIZE_IMAGES_CRON'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
	</div>
</div>
