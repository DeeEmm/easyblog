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

$coverSizes = [
	'thumbnail' => 'COM_EASYBLOG_SETTINGS_POST_COVER_THUMBNAIL',
	'large' => 'COM_EASYBLOG_SETTINGS_POST_COVER_LARGE',
	'original' => 'COM_EASYBLOG_SETTINGS_POST_COVER_ORIGINAL'
];

$alignments = [
	'left' => 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_LEFT',
	'right' => 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_RIGHT',
	'center' => 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_CENTER',
	'none' => 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_NONE'
];

$coverAspectRatio = [
	'16/9' => '16:9',
	'3/2' => '3:2'
];
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_LISTING_TITLE', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_LISTING_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'cover_size', 'COM_EASYBLOG_SETTINGS_POST_COVER_SIZE', $coverSizes); ?>

				<?php echo $this->fd->html('settings.dropdown', 'cover_size_mobile', 'COM_EB_SETTINGS_POST_COVER_SIZE_MOBILE', $coverSizes); ?>

				<?php echo $this->fd->html('settings.dropdown', 'cover_aspect_ratio', 'COM_EB_SETTINGS_POST_COVER_ASPECT_RATIO', $coverAspectRatio); ?>

				<?php echo $this->fd->html('settings.toggle', 'cover_firstimage', 'COM_EASYBLOG_SETTINGS_POST_COVER_USE_FIRST_IMAGE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'cover_crop', 'COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER', '', 'data-cover-crop'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH', 'cover-width-full'); ?>

					<div class="col-md-7">
						<div class="checkbox" style="margin-top: 0;" data-cover-full-width-wrapper>
							<input type="checkbox" id="cover-width-full" value="1" name="cover_width_full" <?php echo $this->config->get('cover_width_full') ? ' checked="checked"' : '';?> data-cover-full-width />
							<label for="cover-width-full">
								<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_USE_FULL_WIDTH');?>
							</label>
						</div>

						<div class="row mt-lg <?php echo $this->config->get('cover_width_full') ? 'hide' : '';?>" data-cover-width-input>
							<div class="col-sm-5">
								<div class="input-group">
									<?php echo $this->fd->html('form.text', 'cover_width', $this->config->get('cover_width'), 'cover_width', [
										'attributes' => 'data-cover-width',
										'class' => 'form-control text-center'
									]); ?>

									<span class="input-group-addon">
										<?php echo JText::_('COM_EASYBLOG_ELEMENTS_PX'); ?>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.text', 'cover_height', 'COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT', '', [
					'postfix' => 'COM_EASYBLOG_ELEMENTS_PX',
					'size' => 5,
					'wrapperAttributes' => 'data-cover-height',
					'visible' => $this->config->get('cover_crop') ? true : false
				]); ?>

				<div class="form-group<?php echo $this->config->get('cover_width_full') ? ' hide' : '' ?>" data-cover-alignment>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT', 'cover_alignment'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'cover_alignment', $this->config->get('cover_alignment'), $alignments);?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_ENTRY_TITLE', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_ENTRY_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'cover_size_entry', 'COM_EASYBLOG_SETTINGS_POST_COVER_SIZE', $coverSizes); ?>

				<?php echo $this->fd->html('settings.dropdown', 'cover_size_entry_mobile', 'COM_EB_SETTINGS_POST_COVER_SIZE_MOBILE', $coverSizes); ?>

				<?php echo $this->fd->html('settings.toggle', 'cover_crop_entry', 'COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER', '', 'data-cover-crop-entry'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH', 'cover-width-full-entry'); ?>

					<div class="col-md-7">
						<div class="checkbox" style="margin-top: 0;" data-cover-full-width-wrapper>
							<input type="checkbox" id="cover-width-full-entry" value="1" name="cover_width_entry_full" <?php echo $this->config->get('cover_width_entry_full') ? ' checked="checked"' : '';?> data-cover-full-width-entry />
							<label for="cover-width-full-entry">
								<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_USE_FULL_WIDTH');?>
							</label>
						</div>

						<div class="row mt-lg <?php echo $this->config->get('cover_width_entry_full') ? 'hide' : '';?>" data-cover-width-input>
							<div class="col-sm-5">
								<div class="input-group">
									<?php echo $this->fd->html('form.text', 'cover_width_entry', $this->config->get('cover_width_entry', 260), 'cover_width_entry', [
										'attributes' => 'data-cover-width-entry',
										'class' => 'form-control text-center'
									]); ?>

									<span class="input-group-addon">
										<?php echo JText::_('COM_EASYBLOG_ELEMENTS_PX'); ?>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.text', 'cover_height_entry', 'COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT', '', [
					'postfix' => 'COM_EASYBLOG_ELEMENTS_PX',
					'size' => 5,
					'wrapperAttributes' => 'data-cover-height-entry',
					'visible' => $this->config->get('cover_crop_entry') ? true : false
				]); ?>

				<div class="form-group <?php echo $this->config->get('cover_width_entry_full') ? 'hide' : '';?>" data-cover-alignment-entry>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT', 'cover_alignment_entry'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'cover_alignment_entry', $this->config->get('cover_alignment_entry'), $alignments);?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_FEATURED_TITLE', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_FEATURED_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'cover_featured_size', 'COM_EASYBLOG_SETTINGS_POST_COVER_SIZE', $coverSizes); ?>

				<?php echo $this->fd->html('settings.dropdown', 'cover_featured_size_mobile', 'COM_EB_SETTINGS_POST_COVER_SIZE_MOBILE', $coverSizes); ?>

				<?php echo $this->fd->html('settings.toggle', 'cover_featured_crop', 'COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER', '', 'data-cover-featured-crop'); ?>

				<?php echo $this->fd->html('settings.text', 'cover_featured_width', 'COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH', '', [
					'postfix' => 'COM_EASYBLOG_ELEMENTS_PX',
					'size' => 5,
					'attributes' => 'data-cover-featured-width'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'cover_featured_height', 'COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT', '', [
					'postfix' => 'COM_EASYBLOG_ELEMENTS_PX',
					'size' => 5,
					'wrapperAttributes' => 'data-cover-featured-height',
					'visible' => $this->config->get('cover_featured_crop') ? true : false
				]); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT', 'cover_featured_alignment'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'cover_featured_alignment', $this->config->get('cover_featured_alignment'), [
							'left' => 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_LEFT',
							'right' => 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_RIGHT'
						]);?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
