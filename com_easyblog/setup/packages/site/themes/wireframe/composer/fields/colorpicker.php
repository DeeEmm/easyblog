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

if (!isset($classname)) {
	$classname = '';
} else {
	$classname = ' ' . $classname;
}

$attrs = '';
if (isset($attributes)) $attrs .= ' ' . $attributes;
?>
<div class=" eb-colorpicker colorpicker<?php echo $classname; ?>" data-type="colorpicker" data-eb-colorpicker<?php echo $attrs; ?>>
	<div class="colorpicker-hsb-panel">
		<div class="colorpicker-sb-panel">
			<div class="colorpicker-b-overlay"></div>
			<div class="colorpicker-s-overlay"></div>
			<div class="colorpicker-sb-handle"></div>
		</div>
		<div class="colorpicker-h-panel">
			<div class="colorpicker-h-handle"></div>
		</div>
	</div>
</div>
