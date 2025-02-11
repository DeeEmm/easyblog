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
<div data-eb-input-value-<?php echo $idx; ?>>
	<select data-eb-input-select>
		<option <?php echo $value == '-1' ? 'selected="selected"' : ''; ?> value="-1"><?php echo JText::_('COM_EASYBLOG_USE_DEFAULT_OPTIONS'); ?></option>
		<option <?php echo $value != '-1' ? 'selected="selected"' : ''; ?> value="custom"><?php echo JText::_('Specify the value'); ?></option>
	</select>

	<input data-eb-input-value type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>" class="form-control input-mini text-center<?php echo $value == '-1' ? ' hide' : ''; ?>">
</div>