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

if (!isset($container)) $container = '';
if (!isset($label)) $label = '';
if (!isset($value)) $value = '100';
if (!isset($toggle)) $toggle = true;
if (!isset($selected)) $selected = false;
if (!isset($input)) $input = true;
if (!isset($resetToggle)) $resetToggle = false;

$unitPresets = array(
	'pixel' => array(
		'title' => 'COM_EASYBLOG_PIXEL',
		'type'  => 'pixel',
		'unit'  => 'px'
	),
	'percent' => array(
		'title' => 'COM_EASYBLOG_PERCENT',
		'type'  => 'percent',
		'unit'  => '%'
	)
);

if (!isset($units)) {
	$units = array('pixel', 'percent');
}

$hasUnits = is_array($units) && count($units) > 0;

if ($hasUnits) {

	$normalizedUnits = array();
	foreach ($units as $unit) {
		$normalizedUnits[] = is_string($unit) ? $unitPresets[$unit] : $unit;
	}
	$units = $normalizedUnits;

	// Use the first unit as default unit
	if (!isset($defaultUnit)) $defaultUnit = $units[0]['type'];
}

$attrs = '';
if (isset($name)) $attrs .= ' data-name="' . $name . '"';
if (isset($attributes)) $attrs .= ' ' . $attributes;
?>
<div class="o-form-group eb-numslider<?php echo $classname; ?> t-mb--no" data-type="numslider" data-eb-numslider<?php echo $attrs; ?>>

	<div class="row-table eb-composer-fieldrow" <?php echo $container; ?>>


		<div class="eb-numslider-slider">
			<div class="eb-numslider-widget" data-eb-numslider-widget></div>
		</div>

		<!-- <?php if ($input) { ?>
		<div class="eb-numslider-value col-cell" data-eb-numslider-value>
			<div class="input-group">
				<input type="text" class="eb-numslider-input form-control" value="<?php echo $value; ?>" data-eb-numslider-input />
			</div>
		</div>
		<?php } ?> -->

	</div>
</div>
