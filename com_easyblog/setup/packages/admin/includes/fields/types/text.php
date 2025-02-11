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

class EasyBlogFieldsTypeText extends EasyBlogFieldsAbstract
{
	public $title = null;
	public $element = 'text';

	public function __construct()
	{
		// Set the title of this field
		$this->title = JText::_('COM_EASYBLOG_FIELDS_TYPE_TEXT');

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
		$theme->set('params', $params);
		$theme->set('element', $this->element);
		$theme->set('formElement', $this->formElement);
		$theme->set('options', $options);
		$theme->set('field', $field);

		$output = $theme->output('admin/fields/types/admin/text');

		return $output;
	}

	/**
	 * Renders the textbox form in the composer
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function form(EasyBlogPost $post, EasyBlogTableField &$field)
	{
		// Retrieve the data for this pot
		$value = $this->getValue($field, $post);

		if ($value) {
			$value = $value->value;
		}

		// Get the params
		$params = $field->getParams();

		$theme  = EB::themes();
		$theme->set('value', $value);
		$theme->set('formElement', $this->formElement);
		$theme->set('params', $params);
		$theme->set('element', $this->element);
		$theme->set('field', $field);

		$output = $theme->output('site/fields/forms/text');

		return $output;
	}

	/**
	 * Renders the output of the textbox field in the blog
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

		return strip_tags($data->value);
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
			$result[$idx] = strip_tags($data->value);
		}

		return $result[$idx];
	}
}
