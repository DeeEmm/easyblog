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
<div class="eb-pills t-w--100" <?php echo $wrapperAttribute; ?>>
	<div class="eb-pill t-w--100">
		<?php foreach($types as $index => $value) { ?>
		<div class="eb-pill-item <?php echo $selected == $value ? 'active' : ''; ?>" data-alignment-item data-type="<?php echo $value; ?>"
			data-eb-provide="tooltip"
			data-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT_' . strtoupper($value));?>"
		>
			<i class="fdi fa fa-align-<?php echo $value; ?>"></i><span></span>
		</div>
		<?php } ?>
	</div>
</div>
