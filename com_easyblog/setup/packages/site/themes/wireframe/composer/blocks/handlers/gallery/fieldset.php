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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion eb-gallery-size-fieldset is-open" data-eb-composer-block-section data-eb-gallery-size-fieldset data-name="gallery-size">
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GALLERY_SIZE'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group o-form-group--eb-style-bordered t-lg-p--md">
			<div class="eb-gallery-ratio-info-field" data-eb-gallery-ratio-info-field data-name="gallery-ratio-info">
				<div class="eb-composer-fieldgroup-content">
					<div class="o-row">
						<div class="o-col-sm"><?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_ASPECT_RATIO'); ?>
							<div class="eb-gallery-ratio-label" data-eb-gallery-ratio-label>16:9</div>
						</div>

						<div class="o-col-sm">
							<button type="button" class="btn btn--sm btn-eb-default-o pull-right" data-eb-gallery-ratio-button><?php echo JText::_('COM_EASYBLOG_CHANGE_BUTTON'); ?></button>
						</div>
					</div>
				</div>
			</div>

			<div class="eb-gallery-ratio-field">
				<div class="eb-composer-fieldgroup-content">
					<div class="">
						<div class="t-lg-mb--md">
							<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_SELECT_ASPECT_RATIO'); ?>
						</div>
						<div class="eb-swatch swatch-grid eb-gallery-ratio-swatch">
							<div class="row">
								<?php foreach ($ratioList as $ratio) { ?>
								<div class="col-sm-3">
									<div class="eb-swatch-item eb-gallery-ratio-selection <?php echo $ratio['classname']; ?>" data-eb-gallery-ratio-selection data-value="<?php echo $ratio['value']; ?>">
										<div class="eb-swatch-preview is-responsive">
											<div><div>
												<div class="eb-gallery-ratio-preview <?php echo $ratio['classname']; ?>" data-eb-gallery-ratio-preview>
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
						<div class="eb-gallery-ratio-actions">
							<button type="button" class="btn btn-sm btn-primary" data-eb-gallery-ratio-customize-button>
								<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOMIZE_BUTTON'); ?></span>
							</button>
							<button type="button" class="btn btn-sm btn-default" data-eb-gallery-ratio-cancel-button>
								<span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="eb-gallery-ratio-custom-field">
				<div class="eb-composer-fieldgroup-content">
					<div class="">
						<div class="t-lg-mb--md">
							<?php echo JText::_('COM_EASYBLOG_USE_CUSTOM_ASPECT_RATIO'); ?>
						</div>
						<input type="text" class="o-form-control eb-gallery-ratio-input" placeholder="16:9 or 1.77" data-eb-gallery-ratio-input/>
						<div class="eb-gallery-ratio-actions">
							<button type="button" class="btn btn-sm btn-primary" data-eb-gallery-ratio-use-custom-button>
								<span><?php echo JText::_('COM_EASYBLOG_USE_ASPECT_RATIO_BUTTON'); ?></span>
							</button>
							<button type="button" class="btn btn-sm btn-default" data-eb-gallery-ratio-cancel-custom-button>
								<span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>

		</div>

		<div class="o-form-group eb-gallery-strategy-field" data-eb-gallery-strategy-field>
			<div class="eb-tabs pill-style">
				<div class="eb-tabs-menu eb-pill">
					<div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
						 data-eb-gallery-strategy-menu-item
						 data-strategy="fill">
						<i class="eb-gallery-strategy-icon icon-fill"><b></b></i>
						<span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FILL'); ?></span>
					</div>
					<div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
						 data-eb-gallery-strategy-menu-item
						 data-strategy="fit">
						<i class="eb-gallery-strategy-icon icon-fit"><b></b></i>
						<span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FIT'); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open is-empty" data-eb-composer-block-section data-eb-gallery-items-fieldset data-name="gallery-items">
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GALLERY_ITEMS'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group o-form-group--eb-style-bordered eb-gallery-items-field">
			<div class="o-form-group eb-list eb-gallery-list" data-type="list" data-eb-gallery-list>
				<div class="eb-list-item-group eb-gallery-list-item-group" data-eb-gallery-list-item-group>
				</div>
				<div class="eb-gallery-hints">
					<div class="eb-hint hint-empty layout-overlay style-gray size-sm">
						<div>
							<span class="eb-hint-text">
								<?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_EMPTY'); ?>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="eb-gallery-list-actions">
				<div class="o-grid-sm">
					<div class="o-grid-sm__cell">
						<button type="button" class="btn btn--sm btn-eb-danger-o" data-eb-gallery-list-item-delete-button><?php echo JText::_('COM_EASYBLOG_DELETE_BUTTON'); ?></button>
					</div>
					<div class="o-grid-sm__cell">
						<button type="button" class="btn btn--sm btn-eb-primary-o pull-right" data-eb-gallery-list-item-primary-button><?php echo JText::_('COM_EASYBLOG_SET_AS_PRIMARY_BUTTON'); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
