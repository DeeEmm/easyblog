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
<div class="eb-accordion" data-eb-accordion>
	<div class="block ac section-content is-active" data-tabs-content>
		<h2 class="ac-q" tabindex="0" contenteditable="true" data-section-title><?php echo JText::_('COM_EB_BLOCK_ACCORDION_DEFAULT_TITLE'); ?></h2>

		<div class="ac-a section-ans" contenteditable="false">
			<div class="ebd-nest" data-type="block" data-accordion-wrapper="">
				<?php echo EB::blocks()->renderEditableBlock(EB::blocks()->createBlock('text', [], array('nested' => true)));?>
			</div>
		</div>
	</div>
</div>