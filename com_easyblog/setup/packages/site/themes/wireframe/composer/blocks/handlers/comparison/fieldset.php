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
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GENERAL_ATTRIBUTES'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'left', 'Display Left Label', true, 'data-field-left-label'); ?>
		<?php echo $this->html('composer.field', 'composer.field.text', 'left_text', 'Left Label', JText::_('Left')); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'right', 'Display Right Label', true, 'data-field-right-label'); ?>
		<?php echo $this->html('composer.field', 'composer.field.text', 'right_text', 'Right Label', JText::_('Right'), 'data-field-right-text'); ?>
	</div>
</div>
