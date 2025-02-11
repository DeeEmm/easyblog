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
	<input type="<?php echo $mask ? 'password' : 'text';?>"
		class="o-form-control"
		name="<?php echo $name;?>"
		id="<?php echo $id;?>"
		value="<?php echo $value;?>"
		<?php echo $attributes ? $attributes : '';?>
		autocomplete="off"
	/>
</div>
