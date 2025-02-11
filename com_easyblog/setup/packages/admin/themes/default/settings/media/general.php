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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_MANAGER', '', 'administrators/configuration/how-to-configure-medias'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_media_manager_place_shared_media', 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_SHARED_MEDIA'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_media_relative_path', 'COM_EASYBLOG_SETTINGS_MEDIA_USE_RELATIVE_PATH'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_media_editing', 'COM_EB_ENABLE_SIMPLE_IMAGE_EDITOR'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_MEDIA_ALLOWED_EXTENSIONS', 'main_media_extensions'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" class="form-control" value="<?php echo $this->config->get('main_media_extensions');?>" id="media_extensions" name="main_media_extensions" data-media-extensions />
							<span class="input-group-btn">
								<button type="button" class="btn btn-default" data-reset-extensions><?php echo JText::_('COM_EASYBLOG_RESET_DEFAULT');?></button>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_MAX_FILESIZE', 'main_upload_image_size'); ?>

					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-6">
								<div class="input-group">
									<input type="text" name="main_upload_image_size" class="form-control text-center" value="<?php echo $this->config->get('main_upload_image_size', '0' );?>" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_MEGABYTES');?></span>
								</div>
							</div>
						</div>


						<div><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_UPLOAD_PHP_MAXSIZE' , ini_get( 'upload_max_filesize') ); ?></div>
						<div><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_UPLOAD_PHP_POSTMAXSIZE' , ini_get( 'post_max_size') ); ?></div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.dropdown', 'main_image_processor', 'COM_EB_SETTINGS_MEDIA_IMAGE_PROCESSOR', [
									'gd' => 'GD Library (Default)',
									'imagick' => 'ImageMagick'
								]); ?>

				<?php
				$mediaQualityOptions = [];

				for ($i = 0; $i <= 100; $i += 10) {
					$message = $i;

					if ($i == 0) {
						$message = JText::sprintf('COM_EASYBLOG_LOWEST_QUALITY_OPTION', $i);
					}

					if ($i == 50) {
						$message = JText::sprintf('COM_EASYBLOG_MEDIUM_QUALITY_OPTION', $i);
					}

					if ($i == 100) {
						$message = JText::sprintf('COM_EASYBLOG_HIGHEST_QUALITY_OPTION', $i);
					}

					$mediaQualityOptions[$i] = $message;
				}
				?>
				<?php echo $this->fd->html('settings.dropdown', 'main_image_quality', 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY', $mediaQualityOptions); ?>
				<?php echo $this->fd->html('settings.dropdown', 'main_default_variation', 'COM_EB_SETTINGS_MEDIA_DEFAULT_VARIATION', [
					'system/large' => 'Large',
					'system/original' => 'Original'
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_STORAGE_TITLE', 'COM_EASYBLOG_SETTINGS_MEDIA_STORAGE_INFO', 'administrators/configuration/how-to-configure-medias'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'main_articles_path', 'COM_EASYBLOG_SETTINGS_MEDIA_ARTICLE_PATH'); ?>

				<?php echo $this->fd->html('settings.text', 'main_image_path', 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_PATH'); ?>

				<?php echo $this->fd->html('settings.text', 'main_shared_path', 'COM_EASYBLOG_SETTINGS_MEDIA_SHARED_PATH'); ?>

				<?php echo $this->fd->html('settings.text', 'main_avatarpath', 'COM_EASYBLOG_SETTINGS_MEDIA_AVATAR_PATH'); ?>

				<?php echo $this->fd->html('settings.text', 'main_categoryavatarpath', 'COM_EASYBLOG_SETTINGS_MEDIA_CATEGORY_PATH'); ?>

				<?php echo $this->fd->html('settings.text', 'main_teamavatarpath', 'COM_EASYBLOG_SETTINGS_MEDIA_TEAMBLOG_PATH'); ?>

			</div>
		</div>
	</div>
</div>
