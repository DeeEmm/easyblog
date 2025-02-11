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
<div class="o-control-input">
	<input class="o-form-control"
		type="text"
		id="<?php echo $id;?>"
		name="<?php echo $name;?>"
		value="<?php echo $this->fd->html('str.escape', $value);?>"
		<?php if ($placeholder) { ?>
		placeholder="<?php echo JText::_($placeholder, true);?>"
		<?php } ?>
		<?php echo $attributes; ?>
	/>
</div>
