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

class EasyBlogForm extends EasyBlog
{
	public $form = null;

	public function getParams($name, $contents, $manifestFile, $xpath = false)
	{
		$this->form = new JForm($name);

		if ($xpath == 'params') {
			$xpath = 'config/fields';
		}

		$this->form->loadFile($manifestFile, true, $xpath);

		$config = EB::table('Configs');
		$config->load($name);

		$params = new JRegistry($config->params);
		$registry = EB::registry($contents);

		$this->form->bind($registry->toArray());
	}

	public function getFormValues()
	{
		$result = array();

		foreach ($this->form->getFieldsets() as $name => $fieldset) {

			foreach ($this->form->getFieldset($name) as $field) {
				$obj = new stdClass();
				$obj->type 	= $field->type;
				$obj->label	= $field->label;
				$obj->key 	= $field->fieldname;
				$obj->value = $field->value;
				$obj->desc 	= $field->description;
				$obj->input = $field->input;

				$result[]	= $obj;
			}

		}

		return $result;
	}

	/**
	 * Retrieves the settings from the entry view
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getManifest($file, $hasInherit = true)
	{
		$parser = EB::getXml($file);

		$fieldsets = [];

		foreach ($parser->fields as $field) {
			$i = 0;

			foreach ($field->fieldset as $row) {

				$fieldset = new stdClass();

				$attributes = $row->attributes();

				$fieldset->id = $i;
				$fieldset->name = (string) $attributes['name'];
				$fieldset->info = (string) $attributes['info'];
				$fieldset->label = (string) $attributes['label'];
				$fieldset->fields = array();

				if (isset($attributes['id'])) {
					$fieldset->id = (string) $attributes['id'];
				}

				$i++;

				// Skip anything without a label.
				if (!$fieldset->label) {
					continue;
				}

				// Go through each of the fields
				foreach ($row->field as $fieldItem) {

					$field = new stdClass();
					$field->attributes = new stdClass();
					$field->options = array();

					foreach ($fieldItem->attributes() as $key => $value) {
						$field->attributes->$key = (string) $value;
					}

					// If user wants to skip this option altogether.
					if (isset($field->attributes->globals) && !$field->attributes->globals) {
						continue;
					}


					foreach ($fieldItem->option as $optionItem) {

						$option = new stdClass();
						$option->label = (string) $optionItem;

						foreach ($optionItem->attributes() as $optionKey => $optionValue) {
							$option->$optionKey = (string) $optionValue;

							$field->options[] = $option;
						}
					}

					// If the xml file contains such attributes, we assume that the user wants to render a boolean field
					// This is only applicable if the field has maximum of 2 option
					if ($field->attributes->type == 'radio' && $field->attributes->class == 'btn-group') {
						$field->attributes->type = 'boolean';
					}

					$fieldset->fields[] = $field;
				}

				$fieldsets[$fieldset->id] = $fieldset;
			}
		}

		return $fieldsets;
	}

	/**
	 * Renders fieldset html codes
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function render($file, $params, $skipEmpty = false, $prefix = '', $hasInherit = true)
	{
		$fieldsets = $this->getManifest($file, $hasInherit);

		$theme = EB::themes();
		$theme->set('skipEmpty', $skipEmpty);
		$theme->set('fieldsets', $fieldsets);
		$theme->set('params', $params);
		$theme->set('prefix', $prefix);

		$output = $theme->output('admin/form/fieldset');

		return $output;
	}
}
