<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-select-group">
	<select <?php echo $id ? 'id="' . $id . '"' : '';?> name="<?php echo $name;?>" class="<?php echo $baseClass ? $baseClass : 'form-control';?> <?php echo $class;?>" <?php echo $attributes;?>>
		<?php foreach ($values as $key => $value) { ?>

			<?php if (is_array($value)) { ?>
			<optgroup label="<?php echo $key;?>">
				<?php foreach ($value as $subKey => $subValue) { ?>
				<option value="<?php echo $useValue ? $subValue : $subKey;?>" <?php echo (is_array($selected) ? in_array($subKey, $selected) : $selected === $subKey) ? 'selected="selected"' : '';?>>
					<?php echo JText::_($subValue);?>
				</option>
				<?php } ?>
			</optgroup>
			<?php } ?>

			<?php // simple array values ?>
			<?php if (!is_array($value) && !is_object($value)) { ?>
			<option value="<?php echo $useValue ? $value : $key;?>" <?php echo (is_array($selected) ? in_array($key, $selected) : $selected === $key || (is_int($key) && $selected == $key)) ? 'selected="selected"' : '';?>>
				<?php echo JText::_($value);?>
			</option>
			<?php } ?>

			<?php // object values ?>
			<?php if (!is_array($value) && is_object($value)) { ?>
			<option value="<?php echo $key;?>" <?php echo (is_array($selected) ? in_array($key, $selected) : $selected === $key) ? 'selected="selected"' : '';?> <?php echo isset($value->attr) ? $value->attr : '';?>>
				<?php echo JText::_($value->title);?>
			</option>
			<?php } ?>

		<?php } ?>
	</select>
	<label for="<?php echo $name;?>" class="o-select-group__drop"></label>
</div>
