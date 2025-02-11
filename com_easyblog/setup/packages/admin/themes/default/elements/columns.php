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
<select id="<?php echo $id;?>" name="<?php echo $name;?>" class="<?php echo $isJoomla4 ? 'form-select' : ''; ?>">
	<?php if ($useGlobal) { ?>
	<option value="-1" <?php echo $value == -1 ? 'selected="selected"' : '';?>>Use Global (<?php echo JText::_($globalValue);?>)</option>
	<?php } ?>

	<?php for ($i = $min; $i <= $max; $i++) { ?>
	<option value="<?php echo $i;?>" <?php echo $value == $i ? 'selected="selected"' : '';?>><?php echo $i;?> Columns</option>
	<?php } ?>
</select>
