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
<select name="<?php echo $name;?>" class="form-control">
	<option value="0" <?php echo $value == -1 ? 'selected="selected"' : '';?>>Use Default (<?php echo ucfirst($this->config->get('layout_theme'));?>)</option>

	<?php foreach ($themes as $theme) { ?>
		<option value="<?php echo $theme;?>" <?php echo $value == $theme ? 'selected="selected"' : '';?>><?php echo ucfirst($theme);?></option>
	<?php } ?>
</select>

