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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('category', true) ? 'is-open' : ''; ?>" data-name="category" data-eb-composer-block-section>
	<div class="eb-composer-fieldset-header" data-eb-composer-block-section-header>
		<strong><?php echo JText::_('COM_EASYBLOG_CATEGORY'); ?></strong>
		<small><span data-eb-composer-category-count>0</span> <?php echo JText::_('COM_EASYBLOG_COMPOSER_CATEGORY_SELECTED');?></small>
		<i class="eb-composer-fieldset-header__icon" data-panel-icon></i>
	</div>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<div class="eb-composer-category">
			<div class="eb-composer-category-list">
				<div class="eb-composer-category-viewport" data-eb-composer-category-viewport data-multiple-categories="<?php echo $this->config->get('layout_composer_multiple_categories'); ?>">
					<div class="eb-hint hint-loading layout-overlay style-gray size-sm hide" data-eb-composer-category-loader>
						<div>
							<i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
						</div>
					</div>
					<div class="eb-composer-category-tree" data-eb-composer-category-tree></div>
				</div>
			</div>
			<div class="eb-composer-category-search">
				<i class="fdi fa fa-search"></i>
				<input type="text" class="eb-composer-category-search-textfield" data-eb-composer-category-search-textfield placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_SEARCH_CATEGORY', true);?>"/>
			</div>
		</div>

		<textarea style="display:none;" data-eb-composer-category-jsondata><?php echo json_encode($categories); ?></textarea>
	</div>

	<div class="hide" data-category-item-group-template>
		<div class="eb-composer-category-item-group" data-eb-composer-category-item-group="$" data-id="">
			<div class="eb-composer-category-item-group-header" data-eb-composer-category-item-group-header>
				<i class="fdi fa fa-angle-left"></i> <span data-title></span>
			</div>
			<div class="eb-composer-category-item-group-body">
				<div class="eb-composer-category-item-group-viewport" data-eb-composer-category-item-group-viewport></div>
			</div>
		</div>
	</div>

	<div class="hide" data-category-item-template>
		<div class="eb-composer-category-item" data-eb-composer-category-item data-id="">
			<b data-eb-composer-category-item-checkbox>
				<b>
					<i class="fdi fa fa-check"></i><em class="fdi fa fa-square"></em>
				</b>
			</b>

			<a href="javascript:void(0);" class="eb-composer-category-item__toggle-primary t-mr--sm" data-category-primary-item data-id="" data-title="" data-original-title="<?php echo JText::_('Set this as the primary category');?>" data-eb-provide="tooltip">
				<i class="fdi fa fa-star eb-composer-category-item__indicator"></i>
			</a>

			<span data-item-title data-title></span> <small><?php echo JText::_('COM_EASYBLOG_COMPOSER_CATEGORY_IS_PRMARY'); ?></small>
			<div class="eb-composer-category-item-count" data-eb-composer-category-item-count><span></span><i class="fdi fa fa-angle-right"></i></div>
		</div>
	</div>
</div>