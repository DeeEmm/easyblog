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
<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_FIELDSET_BROWSE'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content">
		<div class="eb-giphy-browser is-open" data-giphy-browser>
			<div class="o-tabs o-tabs--eb t-justify-content--se">
				<div class="o-tabs__item t-flex-grow--1 active" data-giphy-gifs-tab>
					<a class="o-tabs__link" href="javascript:void(0);">
						<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_GIFS'); ?>
					</a>
				</div>
				<div class="o-tabs__item t-flex-grow--1" data-giphy-stickers-tab>
					<a class="o-tabs__link" href="javascript:void(0);">
						<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_STICKERS'); ?>
					</a>
				</div>
			</div>
			<div class="eb-giphy" data-giphy-container>
				<div class="tab-content">
					<div class="eb-giphy-browser__input-search t-my--md">
						<input
							data-giphy-search
							type="text"
							class="o-form-control"
							placeholder="<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_SEARCH_PLACEHOLDER'); ?>"
						/>
					</div>
					<div class="eb-giphy-browser__result-label t-mb--sm" data-giphy-trending>
						<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_TRENDING'); ?>
					</div>
					<div class="tab-pane active" data-gifs-panel>
						<div class="eb-giphy-list-container t-d--none" data-gifs-list>
						</div>
					</div>
					<div class="tab-pane" data-stickers-panel>
						<div class="eb-giphy-list-container t-d--none" data-stickers-list>
						</div>
					</div>
				</div>

				<div class="o-loader-wrapper">
					<div class="o-loader o-loader--inline"></div>
				</div>
				<div class="o-empty o-empty--height-no">
					<div class="o-card">
						<div class="o-card__body">
							<div class="">
								<div class="o-empty__text eb-giphy-browser__result-text">
									<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_NO_RESULT'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="eb-giphy-browser__result-footer">
				<div class="eb-powered-by-giphy"></div>
			</div>
		</div>
	</div>
</div>
<div class="eb-composer-fieldset t-d--none" data-giphy-alignment-fieldset>
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_FIELDSET_ALIGNMENT'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content">
		<?php echo $this->html('composer.field.alignment', null, ['wrapperAttribute' => 'data-giphy-alignment-selection']); ?>
	</div>
</div>