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
<?php foreach ($options as $option) { ?>
	<?php if ($option->title && $option->value) { ?>
		<div class="o-radio">
			<input type="radio"
				id="<?php echo $formElement;?>[<?php echo $field->id;?>]<?php echo $option->value;?>"
				name="<?php echo $formElement;?>[<?php echo $field->id;?>]"
				value="<?php echo $option->value;?>" <?php echo $value == $option->value ? ' checked="checked"' : '';?>
				data-field-class-input-radio
				class="t-mt--no t-mr--xs"
			/>
			<label for="<?php echo $formElement;?>[<?php echo $field->id;?>]<?php echo $option->value;?>">
				<?php echo JText::_($option->title);?>
			</label>
		</div>
	<?php } ?>
<?php } ?>
