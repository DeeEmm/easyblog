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

class EasyBlogFieldsTypeCheckbox extends EasyBlogFieldsAbstract
{
	public $title = null;
	public $element = 'checkbox';

	public function __construct()
	{
		// Set the title of this field
		$this->title = JText::_('COM_EASYBLOG_FIELDS_TYPE_CHECKBOX');

		parent::__construct();
	}

	public function admin(EasyBlogTableField &$field)
	{
		$theme = EB::themes();

		// Get options
		$options = json_decode($field->options);

		// If there's no value, define a standard value
		if (empty($options)) {
			$option = new stdClass();
			$option->title = '';
			$option->value = '';

			$options = array($option);
		}

		$theme->set('formElement', $this->formElement);
		$theme->set('element', $this->element);
		$theme->set('options', $options);
		$theme->set('field', $field);

		$output = $theme->output('admin/fields/types/admin/options');

		return $output;
	}

	/**
	 * Renders the checkbox form
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function form(EasyBlogPost $post, EasyBlogTableField &$field)
	{
		// Retrieve the data for this pot
		$value = $this->getValue($field, $post);
		$checked = array();

		if ($value) {
			foreach ($value as $item) {
				$checked[] = $item->value;
			}
		}

		// Get multiple select options
		$theme = EB::themes();

		// Get the options
		$options = $this->getOptions($field);

		// Get the params
		$params = $field->getParams();

		$theme->set('checked', $checked);
		$theme->set('formElement', $this->formElement);
		$theme->set('params', $params);
		$theme->set('element', $this->element);
		$theme->set('options', $options);
		$theme->set('field', $field);

		$output = $theme->output('site/fields/forms/checkbox');

		return $output;
	}

	/**
	 * Renders the output of the checkbox values
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function display(EasyBlogTableField &$field, EasyBlogPost &$blog)
	{
		static $result = array();

		$idx = $field->id . $blog->id;

		if (! isset($result[$idx])) {
			$items = $this->getValue($field, $blog);

			if (!$items) {
				$result[$idx] = '';
				return;
			}

			// now we need to get the title for the selected value.
			$options = json_decode($field->options);

			if ($options) {

				for ($i = 0; $i < count($items); $i++) {
					$item =& $items[$i];

					foreach ($options as $option) {
						if ($option->value == $item->value) {
							$item->title = $option->title;
							break;
						}
					}
				}
			}

			$theme = EB::themes();
			$theme->set('items', $items);

			$result[$idx] = $theme->output('site/fields/checkbox');
		}

		return $result[$idx];
	}

	/**
	 * return checkbox values in plain text.
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function text(EasyBlogTableField &$field, EasyBlogPost &$blog)
	{
		static $result = array();

		$idx = $field->id . $blog->id;

		if (! isset($result[$idx])) {
			$items = $this->getValue($field, $blog);

			if (!$items) {
				$result[$idx] = '';
				return;
			}

			$tmp = array();

			// now we need to get the title for the selected value.
			$options = json_decode($field->options);

			if ($options) {
				foreach($items as $item) {
					foreach ($options as $option) {
						if ($option->value == $item->value) {
							$tmp[] = strip_tags($option->title);
							break;
						}
					}
				}
			} else {
				foreach($items as $item) {
					$tmp[] = strip_tags($item[$i]->value);
				}
			}

			$result[$idx] = implode(' ', $tmp);
		}

		return $result[$idx];
	}
}
