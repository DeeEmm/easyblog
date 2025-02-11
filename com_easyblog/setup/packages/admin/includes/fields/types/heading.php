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

class EasyBlogFieldsTypeHeading extends EasyBlogFieldsAbstract
{
	public $title = null;
	public $element = 'heading';

	public function __construct()
	{
		// Set the title of this field
		$this->title = JText::_('COM_EASYBLOG_FIELDS_TYPE_HEADING');

		parent::__construct();
	}

	public function admin(EasyBlogTableField &$field)
	{
		// Get options
		$options = json_decode($field->options);

		// If there's no value, define a standard value
		if (empty($options)) {
			$option = new stdClass();
			$option->title = '';
			$option->value = '';

			$options = array($option);
		}

		// Get the params for this field.
		$params = $field->getParams();

		$theme = EB::themes();
		$theme->set('formElement', $this->formElement);
		$theme->set('params', $params);
		$theme->set('element', $this->element);
		$theme->set('options', $options);
		$theme->set('field', $field);

		$output = $theme->output('admin/fields/types/admin/heading');

		return $output;
	}

	/**
	 * Retrieves the form portion of the custom fields
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function form(EasyBlogPost $post, EasyBlogTableField &$field)
	{
		// Get the params
		$params = $field->getParams();

		// The field title will be use as the value
		$value = $field->title;

		$theme = EB::themes();
		$theme->set('formElement', $this->formElement);
		$theme->set('params', $params);
		$theme->set('element', $this->element);
		$theme->set('field', $field);
		$theme->set('value', $value);

		$output = $theme->output('site/fields/forms/heading');

		return $output;
	}

	/**
	 * Displays the output of the custom field value
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function display(EasyBlogTableField &$field, EasyBlogPost &$blog)
	{
		$data = $this->getValue($field, $blog);

		if (!$data) {
			return;
		}

		$value = strip_tags($data[0]->value);

		if (!$value) {
			return;
		}

		$theme = EB::themes();
		$theme->set('value', nl2br($value));

		$output = $theme->output('site/fields/heading');

		return $output;
	}

	/**
	 * return text values in plain text.
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function text(EasyBlogTableField &$field, EasyBlogPost &$blog)
	{
		static $result = array();

		$idx = $field->id . $blog->id;

		if (!isset($result[$idx])) {
			$data = $this->getValue($field, $blog);
			$result[$idx] = strip_tags($data[0]->value);
		}

		return $result[$idx];
	}
}
