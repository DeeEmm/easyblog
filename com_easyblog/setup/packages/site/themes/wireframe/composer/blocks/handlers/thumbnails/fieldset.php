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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open eb-thumbnails-layout-fieldset" data-eb-composer-block-section data-eb-thumbnails-layout-fieldset data-name="thumbnails-layout">
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_THUMBNAIL_LAYOUT'); ?>

	<div class="eb-composer-fieldset-content o-form-group t-lg-p--lg">
		<div class="o-form-group">
			<div class="eb-swatch swatch-grid">
				<div class="row">
					<div class="col-sm-6">
						<div class="eb-swatch-item-thumbnail active" data-eb-thumbnails-layout-selection data-value="stack">
							<div class="eb-swatch-preview is-responsive layout-stack">
								<div>
									<b class="col-1">
										<s style="height: 30%"></s>
										<s style="height: 50%"></s>
										<s style="height: 20%"></s>
									</b>
									<b class="col-2">
										<s style="height: 50%"></s>
										<s style="height: 30%"></s>
										<s style="height: 20%"></s>
									</b>
									<b class="col-3">
										<s style="height: 20%"></s>
										<s style="height: 30%"></s>
										<s style="height: 50%"></s>
									</b>
									<b class="col-4">
										<s style="height: 30%"></s>
										<s style="height: 50%"></s>
										<s style="height: 20%"></s>
									</b>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_STACK'); ?></span>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="eb-swatch-item-thumbnail" data-eb-thumbnails-layout-selection data-value="grid">
							<div class="eb-swatch-preview is-responsive layout-grid">
								<div>
									<b class="col-1"><s></s><s></s><s></s></b>
									<b class="col-2"><s></s><s></s><s></s></b>
									<b class="col-3"><s></s><s></s><s></s></b>
									<b class="col-4"><s></s><s></s><s></s></b>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_GRID'); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open eb-thumbnails-size-fieldset preset-stack" data-eb-composer-block-section data-eb-thumbnails-size-fieldset data-name="thumbnails-size">
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_THUMBNAIL_SIZE'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group o-form-group--eb-styled-bordered">

			<div class="xeb-composer-fieldgroup eb-thumbnails-columns-field" data-eb-thumbnails-columns-field data-name="thumbnails-columns">
				<div class="xeb-composer-fieldgroup-content o-grid t-pt--lg t-pb--lg">
					<div class="o-grid__cell">
					<?php echo $this->output('site/composer/fields/numslider', array(
							'name'   => 'thumbnails-columns',
							'type'   => 'thumbnails-columns',
							'label'  => JText::_('COM_EASYBLOG_COMPOSER_FIELDS_COLUMNS'),
							'toggle' => false,
							'units'  => array(),
							'input'  => false
						)); ?>
					</div>
					<div class="o-grid__cell o-grid__cell--auto-size t-hidden">
						<div class="eb-thumbnails-ratio-toggle">
							<div>
								<button type="button" class="btn btn-eb-default-o eb-thumbnails-ratio-button" data-eb-thumbnails-ratio-button>
									<i class="fdi fa fa-lock"></i>
									<i class="fdi fa fa-unlock-alt"></i>
									<span class="eb-thumbnails-ratio-label" data-eb-thumbnails-ratio-label>4:3</span>
								</button>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="xeb-composer-fieldgroup eb-thumbnails-ratio-field">
				<div class="xeb-composer-fieldgroup-content">
					<div class="o-form-group">
						<div class="eb-composer-fieldrow-label">
							<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_SELECT_ASPECT_RATIO'); ?>
						</div>
						<div class="eb-swatch swatch-grid eb-thumbnails-ratio-swatch">
							<div class="t-d--flex t-justify-content--sb">
								<?php foreach ($ratioList as $ratio) { ?>
								<div class="t-w--100">
									<div class="eb-swatch-item eb-thumbnails-ratio-selection <?php echo $ratio['classname']; ?>" data-eb-thumbnails-ratio-selection data-value="<?php echo $ratio['value']; ?>">
										<div class="eb-swatch-preview is-responsive">
											<div><div>
												<div class="eb-thumbnails-ratio-preview <?php echo $ratio['classname']; ?>" data-eb-thumbnails-ratio-preview>
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
						<div class="eb-thumbnails-ratio-actions">
							<button type="button" class="btn btn-sm btn-primary" data-eb-thumbnails-ratio-customize-button>
								<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOMIZE_BUTTON'); ?></span>
							</button>
							<button type="button" class="btn btn-sm btn-default" data-eb-thumbnails-ratio-cancel-button>
								<span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="xeb-composer-fieldgroup eb-thumbnails-ratio-custom-field">
				<div class="xeb-composer-fieldgroup-content">
					<div class="o-form-group">
						<div class="eb-composer-fieldrow-label">
							<?php echo JText::_('Enter custom aspect ratio'); ?>
						</div>
						<input type="text" class="o-form-control eb-thumbnails-ratio-input" placeholder="16:9 or 1.77" data-eb-thumbnails-ratio-input/>
						<div class="eb-thumbnails-ratio-actions">
							<button type="button" class="btn btn-sm btn-primary" data-eb-thumbnails-ratio-use-custom-button>
								<span><?php echo JText::_('COM_EASYBLOG_USE_ASPECT_RATIO_BUTTON'); ?></span>
							</button>
							<button type="button" class="btn btn-sm btn-default" data-eb-thumbnails-ratio-cancel-custom-button>
								<span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>

		</div>

		<div class="o-form-group eb-thumbnails-strategy-field" data-eb-thumbnails-strategy-field>
			<div class="eb-tabs pill-style">
				<div class="eb-tabs-menu eb-pill">
					<div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
						 data-eb-thumbnails-strategy-menu-item
						 data-strategy="fill">
						<i class="eb-thumbnails-strategy-icon icon-fill"><b></b></i>
						<span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FILL'); ?></span>
					</div>
					<div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
						 data-eb-thumbnails-strategy-menu-item
						 data-strategy="fit">
						<i class="eb-thumbnails-strategy-icon icon-fit"><b></b></i>
						<span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FIT'); ?></span>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

