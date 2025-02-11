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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_HEADING_LEVEL'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="eb-list" data-type="list" data-eb-composer-block-heading-level>
			<div class="eb-list-item-group">
				<div class="eb-list-item" data-level="H1"><h1><?php echo JText::_('COM_EASYBLOG_BLOCKS_HEADING_LEVEL_1'); ?><small>&lt;h1&gt;</small></h1></div>
				<div class="eb-list-item" data-level="H2"><h2><?php echo JText::_('COM_EASYBLOG_BLOCKS_HEADING_LEVEL_2'); ?><small>&lt;h2&gt;</small></h2></div>
				<div class="eb-list-item" data-level="H3"><h3><?php echo JText::_('COM_EASYBLOG_BLOCKS_HEADING_LEVEL_3'); ?><small>&lt;h3&gt;</small></h3></div>
				<div class="eb-list-item" data-level="H4"><h4><?php echo JText::_('COM_EASYBLOG_BLOCKS_HEADING_LEVEL_4'); ?><small>&lt;h4&gt;</small></h4></div>
				<div class="eb-list-item" data-level="H5"><h5><?php echo JText::_('COM_EASYBLOG_BLOCKS_HEADING_LEVEL_5'); ?><small>&lt;h5&gt;</small></h5></div>
				<div class="eb-list-item" data-level="H6"><h6><?php echo JText::_('COM_EASYBLOG_BLOCKS_HEADING_LEVEL_6'); ?><small>&lt;h6&gt;</small></h6></div>
			</div>
		</div>
	</div>
</div>
