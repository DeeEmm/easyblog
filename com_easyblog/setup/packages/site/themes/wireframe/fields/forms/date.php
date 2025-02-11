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
<div class="o-input-group date" data-date-picker>
	<input type="text"
		class="o-form-control"
		name="<?php echo $formElement;?>[<?php echo $field->id;?>]"
		value="<?php echo $value;?>"
		data-field-class-input-date
	/>

	<span class="o-input-group__addon" data-field-class-input-date-button>
		<i class="fdi far fa-calendar-alt"></i>
	</span>
</div>
