<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-image-size-advanced-field" data-eb-image-size-advanced-field>
	<div class="eb-tabs pill-style">
		<div class="eb-tabs-menu eb-pill">
			<div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
				 data-eb-image-strategy-menu-item
				 data-strategy="fill">
				<i class="eb-image-strategy-icon icon-fill"><b></b></i>
				<span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FILL'); ?></span>
			</div>
			<div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
				 data-eb-image-strategy-menu-item
				 data-strategy="fit">
				<i class="eb-image-strategy-icon icon-fit"><b></b></i>
				<span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FIT'); ?></span>
			</div>
			<div class="eb-tabs-menu-item eb-pill-item cell-ellipse" data-id="image-strategy-custom"
				 data-eb-image-strategy-menu-item
				 data-strategy="custom">
				<i class="eb-image-strategy-icon icon-custom"><b></b></i>
				<span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_CUSTOM'); ?></span>
			</div>
		</div>
		<div class="eb-tabs-content">
			<div class="eb-tabs-content-item" data-id="image-strategy-custom"
				 data-eb-image-strategy-menu-content
				 data-strategy="custom">
				<div class="eb-image-map-container" data-eb-image-map-container>
					<div class="eb-image-map-figure" data-eb-image-map-figure>
						<div class="eb-image-map-viewport"  data-eb-image-map-viewport>
							<b></b><q></q><s></s><i></i>
							<div class="eb-image-map-preview" data-eb-image-map-preview></div>
						</div>
					</div>
				</div>
				<div class="eb-image-resize-field">
					<div>
						<div class="row-table">
							<div class="col-cell">
								<div class="eb-composer-fieldrow-label"><?php echo JText::_('COM_EASYBLOG_IMAGE_WIDTH'); ?></div>
								<input type="text" class="o-form-control" data-eb-image-resize-input-field data-prop="width" />
							</div>
							<div class="col-cell cell-tight">
								<div class="eb-composer-field-row-label">&nbsp;</div>
								<?php echo $this->output('site/composer/fields/checkbox', array(
										'classname' => 'eb-image-resize-ratio-lock',
										'attributes' => 'data-eb-image-resize-ratio-lock',
										'checked' => true
									)); ?>
							</div>
							<div class="col-cell">
								<div class="eb-composer-fieldrow-label"><?php echo JText::_('COM_EASYBLOG_IMAGE_HEIGHT'); ?></div>
								<input type="text" class="o-form-control" data-eb-image-resize-input-field data-prop="height"/>
							</div>
						</div>
					</div>
					<div>
						<div class="row-table">
							<div class="col-cell cell-pad">
								<div class="eb-composer-fieldrow-label"><?php echo JText::_('COM_EASYBLOG_IMAGE_TOP'); ?></div>
								<input type="text" class="o-form-control" data-eb-image-resize-input-field data-prop="top"/>
							</div>
							<div class="col-cell cell-pad">
								<div class="eb-composer-fieldrow-label"><?php echo JText::_('COM_EASYBLOG_IMAGE_LEFT'); ?></div>
								<input type="text" class="o-form-control" data-eb-image-resize-input-field data-prop="left" />
							</div>
							<div class="col-cell cell-tight">
								<div class="eb-composer-field-row-label">&nbsp;</div>
								<button class="btn btn-eb-default-o btn-sm" data-eb-image-resize-reset-button><i class="fdi fa fa-sync"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
