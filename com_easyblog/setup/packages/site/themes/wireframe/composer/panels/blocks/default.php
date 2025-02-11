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
<div class="eb-composer-panel is-multipanel eb-composer-panel-blocks is-empty <?php echo $templateEditor ? 'active' : ''; ?>" data-eb-composer-panel data-id="blocks">
	<div class="eb-composer-panel-content">
		<div class="eb-composer-subpanel eb-composer-blocks-block-subpanel" data-eb-composer-blocks-block-subpanel>
			<div class="eb-composer-subpanel-content">
				<div data-eb-composer-panel-content-viewport data-scrolly-viewport>
					<div class="eb-composer-fieldset eb-blocks-tree-field tree-minimal" data-eb-blocks-tree-field>
						<div class="eb-block o-grid-sm" data-eb-block>
							<div class="o-grid-sm__cell o-grid-sm__cell--auto-size eb-block-icon">


								<i class="fdi fa fa-suqare-o" data-eb-block-icon></i>
							</div>
							<div class="o-grid-sm__cell t-lg-pl--md">
								<div class="eb-block-title" data-eb-block-title><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS');?></div>
								<div class="eb-block-stat text-muted" data-eb-block-stat>
									<span class="eb-block-level"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_TREE_LEVEL'); ?> <span class="eb-block-level-count" data-eb-block-level-count><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LEVEL_COUNT_ONE');?></span></span>
									<span class="eb-block-child"> &middot; <span class="eb-block-child-count" data-eb-block-child-count><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LEVEL_COUNT_ZERO');?></span> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_TREE_CHILD_BLOCKS'); ?></span>
								</div>
							</div>
							<div class="o-grid-sm__cell o-grid-sm__cell--auto-size">
								<button type="button" class="btn btn-eb-default-o btn--sm eb-blocks-tree-toggle-button" data-eb-blocks-tree-toggle-button><span class="text-show-tree"><i class="fdi fa fa-plus-square"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SHOW_FULL_TREE'); ?></span><span class="text-hide-tree"><i class="fdi fa fa-minus-square"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_HIDE_FULL_TREE'); ?></span></button>
							</div>
						</div>
						<div class="eb-blocks-tree" data-eb-blocks-tree>
							<div class="eb-list">
								<div class="eb-list-item-group" data-eb-blocks-tree-item-group>
								</div>
							</div>
						</div>
						<div class="hide" data-eb-blocks-tree-item-template>
							<div class="eb-list-item eb-blocks-tree-item" data-eb-blocks-tree-item data-type="" data-uid="">
								<i class="" data-eb-blocks-tree-item-icon></i>
								<strong data-eb-blocks-tree-item-title></strong>
							</div>
						</div>
					</div>


					<div class="eb-composer-blocks-prop-group" data-eb-composer-blocks-prop-group data-type="specific"></div>

					<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-name="font" data-type="alignment" data-eb-composer-block-section style="display:none;">
						<?php echo $this->html('composer.panel.header', 'COM_EB_ALIGNMENT'); ?>

						<div class="eb-composer-fieldset-content o-form-horizontal">
							<?php
								$layout = [
									[
										'class' => 'group-alignment',
										'actions' => array('alignleft', 'aligncenter', 'alignright', 'justify')
									]
								];
								echo $this->output('site/composer/fields/font_formatting', ['classname' => 'section-text', 'layout' => $layout]);
							?>
						</div>
					</div>

					<!-- When the block is selected -->
					<?php if ($this->config->get('layout_composer_font')) { ?>
						<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-name="font" style="display: none;" data-eb-composer-block-section>
							<?php echo $this->html('composer.panel.header', 'COM_EB_COMPOSER_FONT_STYLE'); ?>

							<div class="eb-composer-fieldset-content o-form-horizontal">
								<?php echo $this->output('site/composer/fields/font'); ?>
							</div>
						</div>
					<?php } ?>

					<!-- When text is selected -->
					<?php if ($this->config->get('layout_composer_font')) { ?>
						<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-composer-selection data-type="text" data-name="text" style="display: none;" data-eb-composer-block-section>
							<?php echo $this->html('composer.panel.header', 'COM_EB_COMPOSER_FONT_STYLE'); ?>

							<div class="eb-composer-fieldset-content o-form-horizontal">
								<?php echo $this->output('site/composer/fields/font'); ?>
							</div>
						</div>
					<?php } ?>

					<?php if ($this->config->get('layout_composer_customid')) { ?>
					<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-name="custom-id" data-eb-composer-block-section style="display: none;">
						<?php echo $this->html('composer.panel.header', 'COM_EB_BLOCKS_CUSTOM_ID'); ?>

						<div class="eb-composer-fieldset-content">
							<div class="o-form-group">
								<?php echo $this->html('composer.field.text', 'heading_id', '', ['attributes' => 'data-eb-composer-blocks-id']); ?>
							</div>

							<?php echo $this->html('composer.panel.help', 'COM_EB_BLOCKS_CUSTOM_ID_HELP'); ?>
						</div>
					</div>
					<?php } ?>

					<?php if ($this->config->get('layout_composer_customcss')) { ?>
					<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-name="custom-css" data-eb-composer-block-section style="display: none;">
						<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_BLOCKS_CUSTOM_CSS'); ?>

						<div class="eb-composer-fieldset-content">
							<div class="o-form-group">
								<?php echo $this->html('composer.field.text', 'heading_id', '', ['attributes' => 'data-eb-composer-blocks-css']); ?>
							</div>

							<?php echo $this->html('composer.panel.help', 'COM_EASYBLOG_COMPOSER_BLOCKS_CUSTOM_CSS_HELP'); ?>
						</div>
					</div>
					<?php } ?>

					<div class="eb-hint style-gray layout-overlay hint-empty">
						<div>
							<i class="eb-hint-icon fdi fa fa-cube"></i>
							<span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SELECT'); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="eb-composer-subpanel eb-composer-blocks-removal-subpanel" data-eb-composer-blocks-removal-subpanel>
			<div>
				<div class="eb-hint layout-overlay style-gray">
					<div>
						<i class="eb-hint-icon far fa-trash-alt"></i>
						<span class="eb-hint-text t-lg-mt--lg"><?php echo JText::_('COM_EASYBLOG_COMPOSER_DROP_BLOCK_TO_REMOVE_BLOCK');?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>