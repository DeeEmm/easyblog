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
<input type="password"
	name="<?php echo $name;?>"
	<?php echo $id ? 'id="' . $id . '"' : '';?>
	class="form-control <?php echo str_ireplace(['o-form-control', 'form-control'], '', $class);?>"
	value="<?php echo $value;?>"
	<?php if (!$autocomplete) { ?>
	autocomplete="new-password"
	<?php } ?>
	<?php echo $placeholder ? 'placeholder="' . JText::_($placeholder) . '"' : '';?>
	<?php echo $attributes;?>
/>
