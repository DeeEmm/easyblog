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
<button type="button" class="btn eb-comp-toolbar__nav-btn dropdown-toggle_" data-bp-toggle="dropdown" data-composer-toolbar-cover
	data-title="<?php echo JText::_('COM_EASYBLOG_INSERT_BLOCK');?><br />[ shift ] + [ i ]"
	data-placement="bottom"
	data-eb-provide="tooltip"
	data-html="1"
>
	<i class="fdi fa fa-layer-group fa-fw"></i>
</button>

<div class="dropdown-menu eb-comp-toolbar-dropdown-menu eb-comp-toolbar-dropdown-menu--blocks">
	<div class="eb-comp-toolbar-dropdown-menu__hd">
		<div class="eb-comp-toolbar-dropdown-menu__icon-container t-lg-mr--md">
			<i class="fdi fa fa-layer-group fa-fw"></i>
		</div>
		 <?php echo JText::_('COM_EASYBLOG_INSERT_BLOCK');?>
		<div class="eb-comp-toolbar-dropdown-menu__hd-action">
			<a href="javascript:void(0);" class="eb-comp-toolbar-dropdown-menu__close" data-toolbar-dropdown-close>
				<i class="fdi fa fa-times-circle"></i>
			</a>
		</div>
	</div>
	<div class="eb-comp-toolbar-dropdown-menu__bd">
		<div class="">
			<div class="t-px--lg t-py--md t-border-bottom--1">
				<input class="o-form-control" type="text" placeholder="Search Blocks" data-eb-blocks-search="">
			</div>

			<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section="" data-id="elements">

				<!-- TODO: Remove dummy codes -->
				<div class="eb-composer-blocks-group-contaixner">
					<?php $i = 0; ?>
					<?php foreach ($blocks as $category => $blockItems) { ?>
						<?php if ((count($blockItems) == 1 && $blockItems[0]->visible == true) || count($blockItems) > 1) { ?>
						<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$composerPreferences || $composerPreferences->get(strtolower($category), 'open') == 'open' ? 'is-open' : '';?>" data-eb-composer-block-section data-id="<?php echo strtolower($category);?>">
							<div class="eb-composer-fieldset-header" data-eb-composer-block-section-header>
								<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_CATEGORY_' . strtoupper($category)); ?></strong>
								<i class="eb-composer-fieldset-header__icon t-lg-pull-right" data-panel-icon></i>
							</div>

							<div class="eb-composer-fieldset-content" data-eb-composer-block-section-content>
								<div class="eb-composer-block-menu-group" data-eb-composer-block-menu-group>
									<?php foreach ($blockItems as $block) { ?>
									<div class="eb-composer-block-menu ebd-block<?php echo !$block->visible ? ' is-hidden' : '';?>" data-eb-composer-block-menu data-type="<?php echo $block->type; ?>" data-keywords="<?php echo $block->visible ? $block->keywords : ''; ?>">
										<div>
											<i class="<?php echo $block->icon; ?>"></i>
											<span><?php echo $block->title; ?></span>
										</div>
										<textarea data-eb-composer-block-meta data-type="<?php echo $block->type; ?>"><?php echo json_encode($block->meta(), JSON_HEX_QUOT | JSON_HEX_TAG); ?></textarea>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } else if (count($blockItems) == 1 && $blockItems[0]->visible != true) { ?>
						<div class="eb-composer-block-menu-group" data-eb-composer-block-menu-group>
							<?php foreach ($blockItems as $block) { ?>
							<div class="eb-composer-block-menu ebd-block<?php echo !$block->visible ? ' is-hidden' : '';?>" data-eb-composer-block-menu data-type="<?php echo $block->type; ?>" data-keywords="<?php echo $block->visible ? $block->keywords : ''; ?>">
								<div>
									<i class="<?php echo $block->icon; ?>"></i>
									<span><?php echo $block->title; ?></span>
								</div>
								<textarea data-eb-composer-block-meta data-type="<?php echo $block->type; ?>"><?php echo json_encode($block->meta(), JSON_HEX_QUOT | JSON_HEX_TAG); ?></textarea>
							</div>
							<?php } ?>
						</div>
						<?php } ?>

						<?php $i++; ?>
					<?php } ?>

					<div class="o-empty">
						<div class="o-empty__content">
							<i class="o-empty__icon fa fa-cube"></i>
							<div class="o-empty__text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_NOT_FOUND'); ?></div>
						</div>
					</div>

					<div class="o-loader o-loader--top"></div>
				</div>





			</div>
		</div>
	</div>
</div>
