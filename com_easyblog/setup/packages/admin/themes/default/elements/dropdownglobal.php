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
<select id="<?php echo $id;?>" name="<?php echo $name;?>" class="form-select">
	<option value="-1" <?php echo $value == -1 ? 'selected="selected"' : '';?>>Use Global (<?php echo JText::_($globalValue);?>)</option>

	<?php foreach ($options as $option) { ?>
		<option value="<?php echo $option->value;?>" <?php echo $value == $option->value ? 'selected="selected"' : '';?>><?php echo JText::_($option->title);?></option>
	<?php } ?>
</select>
