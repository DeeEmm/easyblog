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
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-block-poll-form>
	<?php echo $this->html('composer.block.placeholder', 'fdi fas fa-poll-h', 'COM_EB_COMPOSER_BLOCK_POLL'); ?>

	<span class="o-input-group__btn">
		<button type="button" class="btn btn-eb-primary btn--sm" data-poll-compose-button>
			<?php echo JText::_('COM_EB_BLOCKS_POLL_INSERT_BUTTON');?>
		</button>
	</span>
</div>
