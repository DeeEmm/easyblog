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
<div class="o-form-group is-radio">
	<div class="radio">
		<label>
			<input type="radio" id="<?php echo $id;?>" name="<?php echo $name;?>" <?php echo $checked ? 'checked': ''; ?> <?php echo $disabled ? 'disabled': ''; ?> <?php echo $attributes;?>>
			<span class="<?php echo $textClass; ?>">
				<?php echo $title;?>
			</span>
		</label>
	</div>
</div>