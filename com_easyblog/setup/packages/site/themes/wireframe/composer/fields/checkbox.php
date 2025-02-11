<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if (!isset($classname)) {
	$classname = '';
} else {
	$classname = ' ' . $classname;
}

if (!isset($id)) {
	$id = uniqid("checkbox-");
}

$attrs = ' id="' . $id . '"';
if (isset($name)) $attrs .= ' name=' . $name . '"';
if (isset($checked) && $checked) $attrs .= ' checked="checked"';
if (isset($attributes)) $attrs .= ' ' . $attributes;

if (!isset($label)) {
	$label = '';
	$classname .= ' checkbox-only';
}
?>
<div class="eb-checkbox2 <?php echo $classname; ?>" data-type="checkbox">
	<input type="checkbox"<?php echo $attrs; ?> />
	<label for="<?php echo $id; ?>"><?php echo $label; ?></label>
</div>

