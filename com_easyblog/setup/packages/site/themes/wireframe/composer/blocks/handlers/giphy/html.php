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
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-giphy-form>
	<?php echo $this->html('composer.block.placeholder', 'fdi fab fa-giphy', 'COM_EB_COMPOSER_BLOCKS_GIPHY_SHARE'); ?>

	<p class="eb-composer-placeholder-error t-text--danger t-hidden" data-giphy-error><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_ERROR'); ?></p>

	<span class="o-input-group__btn">
		<a href="javascript:void(0);" class="btn btn--sm btn-eb-default-o" data-giphy-browse><?php echo JText::_('COM_EB_COMPOSER_BLOCKS_GIPHY_BROWSE_BUTTON');?></a>
	</span>
</div>
