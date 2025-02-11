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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_VIDEO_FIELDS_VIDEO_SIZE'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group eb-video-size-field" data-eb-video-size-field>
			<div class="eb-video-dimension-field">
				<div class="o-grid o-grid--gutters">
					<div class="o-grid__cell">
						<div class="eb-image-size-input">
							<div class="eb-image-size-input__field">
								<label for="" class="eb-image-size-input__label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_WIDTH');?></label>
								<div class="eb-image-size-input__input">
									<input name="video-width" class="o-form-control" type="text" data-video-width />
								</div>
							</div>
							<div class="eb-image-size-input__unit">%</div>
						</div>
					</div>
					<div class="o-grid__cell">
						<div class="eb-image-size-input">
							<div class="eb-image-size-input__field">
								<label for="" class="eb-image-size-input__label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_HEIGHT');?></label>
								<div class="eb-image-size-input__input">
									<input name="video-height" class="o-form-control" type="text" data-video-height />
								</div>
							</div>
							<div class="eb-image-size-input__unit">px</div>
						</div>
					</div>
					<div class="o-grid__cell o-grid__cell--auto-size">
						<button type="button" class="btn btn-eb-default-o eb-video-ratio-button" data-eb-video-ratio-button>
							<i class="fdi fa fa-lock"></i>
							<i class="fdi fa fa-unlock-alt"></i>
							<span class="eb-video-ratio-label" data-eb-video-ratio-label>16:9</span>
						</button>
					</div>
				</div>
			</div>

			<div class="eb-video-ratio-field">
				<div class="">
					<div class="o-form-group">
						<div class="t-lg-mb--md">
							<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_SELECT_ASPECT_RATIO'); ?>
						</div>
						<div class="eb-swatch swatch-grid eb-video-ratio-swatch">
							<div class="row">
								<?php foreach ($ratioList as $ratio) { ?>
								<div class="col-sm-3">
									<div class="eb-swatch-item eb-video-ratio-selection" data-eb-video-ratio-selection data-value="<?php echo $ratio['value']; ?>">
										<div class="eb-swatch-preview is-responsive">
											<div><div>
												<div class="eb-video-ratio-preview <?php echo $ratio['classname']; ?>">
													<div style="padding-top: <?php echo $ratio['padding']; ?>">
														<div><span><?php echo $ratio['caption']; ?></span></div>
													</div>
												</div>
											</div></div>
										</div>
										<div class="eb-swatch-label">
											<span><?php echo $ratio['name']; ?></span>
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
						<div class="eb-video-ratio-actions">
							<button type="button" class="btn btn-sm btn-primary" data-eb-video-ratio-customize-button>
								<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOMIZE_BUTTON'); ?></span>
							</button>
							<button type="button" class="btn btn-sm btn-default" data-eb-video-ratio-cancel-button>
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_CANCEL_BUTTON'); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="eb-video-ratio-custom-field">
				<div class="eb-composer-fieldgroup-content">
					<div class="o-form-group">
						<div class="eb-composer-fieldrow-label">
							<?php echo JText::_('COM_EASYBLOG_USE_CUSTOM_ASPECT_RATIO'); ?>
						</div>
						<input type="text" class="o-form-control eb-video-ratio-input" placeholder="16:9 or 1.77" data-eb-video-ratio-input/>
						<div class="eb-video-ratio-actions">
							<button type="button" class="btn btn-sm btn-primary" data-eb-video-ratio-use-custom-button>
								<span><?php echo JText::_('COM_EASYBLOG_USE_ASPECT_RATIO_BUTTON'); ?></span>
							</button>
							<button type="button" class="btn btn-sm btn-default" data-eb-video-ratio-cancel-custom-button>
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_RATIO_CUSTOM_CANCEL_BUTTON'); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_ALIGNMENT'); ?>

	<div class="eb-composer-fieldset-content">

		<div class="row-table eb-composer-fieldrow">
			<div class="col-cell eb-composer-fieldrow-content">
				<?php echo $this->fd->html('form.dropdown', 'video-alignment', 'center', [
					'left' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_LEFT',
					'center' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_CENTER',
					'right' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_RIGHT'
				], ['attr' => 'data-eb-video-alignment-selection']); ?>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-video-controls-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_CONTROLS'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'autoplay', 'COM_EASYBLOG_BLOCKS_VIDEO_AUTOPLAY', false, 'data-video-fieldset-autoplay'); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'loop', 'COM_EASYBLOG_BLOCKS_VIDEO_LOOP', false, 'data-video-fieldset-loop'); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'muted', 'COM_EASYBLOG_BLOCKS_VIDEO_MUTED', false, 'data-video-fieldset-muted'); ?>
	</div>
</div>
