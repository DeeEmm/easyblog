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

$mediaQualityOptions = [];

for ($i = 0; $i <= 100; $i += 10) {
	$message = $i;
	$message = $i == 0 ? JText::sprintf('COM_EASYBLOG_LOWEST_QUALITY_OPTION', $i) : $message;
	$message = $i == 50 ? JText::sprintf('COM_EASYBLOG_MEDIUM_QUALITY_OPTION', $i) : $message;
	$message = $i == 100 ? JText::sprintf('COM_EASYBLOG_HIGHEST_QUALITY_OPTION', $i) : $message;

	$mediaQualityOptions[$i] = $message;
}

$postfix = [
	'postfix' => 'COM_EASYBLOG_PIXELS',
	'size' => 5
];
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_SETTINGS_MEDIA_IMAGES_WATERMARK'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'image_watermark', 'COM_EB_SETTINGS_MEDIA_IMAGES_ENABLE_WATERMARK'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_MEDIA_IMAGES_WATERMARK', 'watermark'); ?>

					<div class="col-md-7">
						<?php if (EB::hasOverrideLogo('watermark')) { ?>
						<div class="mb-20" data-watermark-placeholder>
							<div class="eb-img-holder">
								<div class="eb-img-holder__remove">
									<a href="javascript:void(0);" data-remove-button>
										<i class="fdi fa fa-times"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_REMOVE'); ?>
									</a>
								</div>


								<img src="<?php echo EB::getLogo('watermark'); ?>" width="120" data-watermark-image />
							</div>
						</div>
						<?php } ?>

						<div>
							<input type="file" name="watermark_logo" />
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.dropdown', 'image_watermark_position', 'COM_EB_SETTINGS_MEDIA_IMAGES_WATERMARK_POSITION', [
					'top-left' => 'Top Left',
					'top' => 'Top',
					'top-right' => 'Top Right',
					'left' => 'Left',
					'center' => 'Center',
					'right' => 'Right',
					'bottom-left' => 'Bottom Left',
					'bottom' => 'Bottom',
					'bottom-right' => 'Bottom Right'
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_ORIGINAL_IMAGE_TITLE', 'COM_EASYBLOG_SETTINGS_MEDIA_ORIGINAL_IMAGE_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_resize_original_image', 'COM_EASYBLOG_SETTINGS_MEDIA_RESIZE_ORIGINAL_IMAGE'); ?>
				<?php echo $this->fd->html('settings.text', 'main_original_image_width', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH', '', $postfix); ?>
				<?php echo $this->fd->html('settings.text', 'main_original_image_height', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT', '', $postfix); ?>
				<?php echo $this->fd->html('settings.dropdown', 'main_original_image_quality', 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY', $mediaQualityOptions); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_SETTINGS_MEDIA_LARGE_IMAGE_TITLE', 'COM_EB_SETTINGS_MEDIA_LARGE_IMAGE_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'main_image_large_width', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH', '', $postfix); ?>
				<?php echo $this->fd->html('settings.text', 'main_image_large_height', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT', '', $postfix); ?>
				<?php echo $this->fd->html('settings.dropdown', 'main_image_large_quality', 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY', $mediaQualityOptions); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_THUMBNAILS_TITLE', 'COM_EASYBLOG_SETTINGS_MEDIA_THUMBNAILS_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'main_image_thumbnail_width', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH', '', $postfix); ?>
				<?php echo $this->fd->html('settings.text', 'main_image_thumbnail_height', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT', '', $postfix); ?>
				<?php echo $this->fd->html('settings.dropdown', 'main_image_thumbnail_quality', 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY', $mediaQualityOptions); ?>
			</div>
		</div>
	</div>
</div>
