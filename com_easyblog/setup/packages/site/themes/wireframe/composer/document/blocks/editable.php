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
<div class="ebd-block <?php echo !empty($blockNest) ? $blockNest : ''; ?>" <?php echo isset($blockType) ? $blockType : ''; ?> <?php echo isset($blockStyle) ? $blockStyle : ''; ?> <?php echo isset($blockUid) ? $blockUid : ''; ?> <?php echo isset($blockTemplate) ? $blockTemplate : ''; ?> <?php echo isset($blockTitle) ? $blockTitle : ''; ?>>

	<?php if (!$postTemplateIsLocked) { ?>
	<div class="ebd-block-action" data-ebd-block-actions>
		<div class="ebd-block-action__inner">

			<?php if ($this->isMobile() || $this->isTablet()) { ?>
			<a href="javascript:void(0);" data-composer-mobile-info
				data-eb-provide="tooltip"
				data-html="1"
				title="<?php echo JText::_('COM_EB_SETTINGS');?>"
			>
				<i class="fdi fa fa-cog"></i>
			</a>
			<?php } ?>

			<a href="javascript:void(0);" data-blocks-insert
				data-eb-provide="tooltip"
				data-html="1"
				title="<?php echo JText::_('COM_EB_COMPOSER_INSERT_BLOCK');?><?php echo $this->html('composer.shortcut', [EB::getMetaKey(), 'shift', 'i']); ?>"
			>
				<i class="fdi far fa-plus-square"></i>
			</a>

			<?php if ($this->config->get('composer_block_templates') && (FH::isSiteAdmin() || $this->acl->get('create_block_templates'))) { ?>
			<a href="javascript:void(0);" data-blocks-save-template
				data-eb-provide="tooltip"
				data-html="1"
				title="<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_SAVE_TEMPLATE');?><?php echo $this->html('composer.shortcut', [EB::getMetaKey(), 'shift', 's']); ?>"
			>
				<i class="fdi far fa-save"></i>
			</a>
			<?php } ?>

			<a href="javascript:void(0);" data-blocks-duplicate
				data-eb-provide="tooltip"
				data-html="1"
				title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_DUPLICATE');?><?php echo $this->html('composer.shortcut', [EB::getMetaKey(), 'shift', 'd']); ?>"
			>
				<i class="fdi far fa-copy"></i>
			</a>

			<a href="javascript:void(0);" data-blocks-move
				data-eb-provide="tooltip"
				data-html="1"
				title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_MOVE');?><?php echo $this->html('composer.shortcut', [EB::getMetaKey(), 'shift', 'm']); ?>"
			>
				<i class="fdi fa fa-arrows-alt"></i>
			</a>
			<a href="javascript:void(0);" data-blocks-remove
				data-eb-provide="tooltip"
				data-html="1"
				title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_REMOVE');?><?php echo $this->html('composer.shortcut', [EB::getMetaKey(), 'shift', 'backspace']); ?>"
			>
				<i class="fdi far fa-trash-alt"></i>
			</a>
		</div>
	</div>
	<?php } ?>

	<div class="ebd-block-toolbar" data-ebd-block-toolbar>
		<div class="ebd-block-toolbar__label">
			<span data-ebd-block-label></span>
		</div>
		<div class="ebd-block-sort-handle" data-ebd-block-sort-handle></div>
	</div>

	<div class="ebd-block-viewport" data-ebd-block-viewport>
		<div class="ebd-block-content" data-ebd-block-content>
			<?php echo isset($blockHtml) ? $blockHtml : ''; ?>
		</div>
		<div class="eb-hint hint-loading style-gray size-sm">
			<div>
				<i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
			</div>
		</div>
	</div>
	<div class="ebd-block-hint" data-ebd-block-hint>
		<div class="eb-hint hint-move layout-overlay">
			<div>
				<span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_HINT_DRAG_TO_MOVE_BLOCK'); ?></span>
			</div>
		</div>
	</div>
</div>
