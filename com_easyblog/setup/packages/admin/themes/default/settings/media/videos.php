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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_VIDEOS_TITLE'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'max_video_width', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH', '', [
					'postfix' => 'COM_EASYBLOG_PIXELS',
					'size' => 5
				]); ?>

				<?php echo $this->fd->html('settings.text', 'max_video_height', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT', '', [
					'postfix' => 'COM_EASYBLOG_PIXELS',
					'size' => 5
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_MEDIA_YOUTUBE_VIDEOS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_youtube_nocookie', 'COM_EB_VIDEOS_YOUTUBE_ALWAYS_NOCOOKIE'); ?>
			</div>
		</div>
	</div>
</div>
