<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_LIST_STYLE'); ?></strong>
	</div>

	<div class="eb-composer-fieldset-content">

		<div class="eb-font">
			<div data-eb-tabs-mode="toggle" class="eb-tabs pill-style">
				<div class="eb-tabs-menu eb-pill">
					<div data-eb-font-family-menu="" data-id="list-style" class="eb-tabs-menu-item eb-pill-item eb-font-family-menu">
						<div class="row-table layout-fixed">
							<div class="col-cell">
								<b><?php echo JText::_('COM_EASYBLOG_BLOCKS_LIST_STYLE');?></b>
							</div>
							<div class="col-cell eb-tabs-menu-item-value">
								<span data-eb-list-style-caption><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LIST_UNORDERED_LIST');?></span>
								<i class="fdi fa fa-caret-down"></i>
							</div>
						</div>
					</div>

					<div class="eb-pill-item" data-eb-list-format-option="" data-format="indent">
						<i class="fdi fa fa-indent"></i>
					</div>

					<div class="eb-pill-item" data-eb-list-format-option="" data-format="outdent">
						<i class="fdi fa fa-outdent"></i>
					</div>
				</div>

				<div class="eb-tabs-content">
					<div class="eb-tabs-content-item eb-font-family-content" data-id="list-style" data-eb-list-style-content>
						<div data-type="list" class="eb-list">
							<div class="eb-list-item-group">
								<div class="eb-list-item active" data-eb-list-style-option data-value="ul">
									<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LIST_UNORDERED_LIST');?>
								</div>
								
								<div class="eb-list-item" data-eb-list-style-option data-value="ol">
									<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LIST_ORDERED_LIST'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>