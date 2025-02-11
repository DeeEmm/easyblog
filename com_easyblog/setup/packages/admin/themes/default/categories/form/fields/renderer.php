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
<?php foreach ($fields as $field) { ?>
<div class="form-group">
	<?php echo $this->fd->html('form.label', $field->attributes->label, 'params[' . $field->attributes->name . ']', '', JText::_($field->attributes->description)); ?>

	<div class="col-md-7">
		<?php
		// Use text instead of textext
		if ($field->attributes->type == 'textext') {
			$field->attributes->type = 'text';
		}

		// For dropdown global, we need to use boolean
		if ($field->attributes->type == 'dropdownglobal') {
			$field->attributes->type = 'boolean';

			// On some field we want to explicitly display a specific type
			if (isset($field->attributes->configtype)) {
				$field->attributes->type = $field->attributes->configtype;
			}
		}
		?>
		<?php echo $this->output('admin/categories/form/fields/' . $field->attributes->type, [
			'field' => $field,
			'params' => $params
		]); ?>
	</div>
</div>
<?php } ?>

