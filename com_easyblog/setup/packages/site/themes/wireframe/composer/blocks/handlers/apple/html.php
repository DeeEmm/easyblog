<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-apple-wrapper>
	<div data-apple-form>
		<i class="eb-composer-placeholder-icon fdi fab fa-apple"></i>
		<b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_APPLE_TITLE');?></b>
		<p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_APPLE_DESC');?></p>
		<p class="eb-composer-placeholder-error t-text--danger t-hidden" data-apple-error><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_APPLE_ERROR'); ?></p>

		<div class="o-input-group o-input-group--sm" style="width: 70%; margin: 0 auto;">
			<input type="text" class="o-form-control" type="text" value="" data-apple-input placeholder="<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_APPLE_PLACEHOLDER', true);?>" />
			<span class="o-input-group__btn">
				<a href="javascript:void(0);" class="btn btn-eb-primary btn--sm" data-apple-embed><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_APPLE_EMBED');?></a>
			</span>
		</div>
	</div>

	<div class="o-loader-wrapper">
		<div class="o-loader o-loader--inline"></div>
	</div>
</div>