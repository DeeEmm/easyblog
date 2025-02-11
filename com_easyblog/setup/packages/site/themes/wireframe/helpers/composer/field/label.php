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
<label class="o-control-label eb-composer-field-label"
	<?php echo $target ? 'for="' . $target . '"' : '';?>
>
	<?php echo JText::_($title); ?>

	<?php if ($info) { ?>
		<i data-html="true" data-placement="top" data-title="<?php echo JText::_($title); ?>" data-content="<?php echo $info; ?>" data-eb-provide="popover" class="fdi fa fa-question-circle"></i>
	<?php } ?>
</label>
