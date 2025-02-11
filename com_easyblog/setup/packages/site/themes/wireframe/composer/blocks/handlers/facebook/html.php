<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-fb-form>
	<?php echo $this->html('composer.block.placeholder', 'fdi fab fa-facebook', 'COM_EASYBLOG_COMPOSER_BLOCKS_FACEBOOK_EMBED_POST', 'COM_EASYBLOG_COMPOSER_BLOCKS_FACEBOOK_EMBED_POST_NOTE'); ?>

	<p class="eb-composer-placeholder-error t-text--danger t-hidden" data-fb-error><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_FACEBOOK_ERROR'); ?></p>

	<div class="o-input-group o-input-group--sm" style="width: 70%; margin: 0 auto;">
		<input type="text" class="o-form-control" type="text" value="" data-fb-source placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_FACEBOOK_PLACEHOLDER', true);?>" />
		<span class="o-input-group__btn">
			<a href="javascript:void(0);" class="btn btn-eb-primary btn--sm" data-fb-embed><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_FACEBOOK_EMBED');?></a>
		</span>
	</div>
</div>
