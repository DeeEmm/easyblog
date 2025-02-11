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

abstract class EasyBlogFieldsAbstract
{
	// The title of the field
	public $title  = '';

	// Form element used in the input
	public $formElement = '';
	public $app = null;
	public $config = null;
	public $input = null;

	public function __construct()
	{
		$this->config = EB::config();
		$this->app = JFactory::getApplication();
		$this->input = EB::request();
	}

	/**
	 * Allows caller to set the form element
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function setFormElement($element = 'field')
	{
		$this->formElement = $element;
	}

	/**
	 * Retrieve the value for this field
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getValue(EasyBlogTableField &$field, EasyBlogPost &$post)
	{
		$singleItemType = array('text', 'textarea', 'date', 'radio');
		$model = EB::model('Fields');

		$values = $model->getFieldValues($field->id, $post->id);

		if (!$values) {
			return false;
		}

		if (count($values) == 1 && in_array($field->type, $singleItemType)) {
			return $values[0];
		}

		return $values;
	}

	/**
	 * Displays the title of the custom field.
	 *
	 * @since   4.0
	 * @access  public
	 */
	final function getTitle()
	{
		return $this->title;
	}

	/**
	 * Retrieve the field's options
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getOptions(EasyBlogTableField &$field)
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

		return $options;
	}

	/**
	 * Displays the title of the custom field.
	 *
	 * @since   4.0
	 * @access  public
	 */
	final function getElement()
	{
		return $this->element;
	}

	/**
	 * Displays the form to the admin when they are customizing the field
	 *
	 * @since   4.0
	 * @access  public
	 */
	abstract public function admin(EasyBlogTableField &$field);

	/**
	 * Displays the form when user edits a blog post
	 *
	 * @since   4.0
	 * @access  public
	 */
	abstract public function form(EasyBlogPost $post, EasyBlogTableField &$field);

	/**
	 * Displays the custom field value when displayed in the blog post
	 *
	 * @since   4.0
	 * @access  public
	 */
	abstract public function display(EasyBlogTableField &$field, EasyBlogPost &$blog);

	/**
	 * return the field values in text
	 *
	 * @since   4.0
	 * @access  public
	 */
	abstract public function text(EasyBlogTableField &$field, EasyBlogPost &$blog);

	/**
	 * Generates the class input form
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function classForm(EasyBlogPost $post, EasyBlogTableField &$field)
	{
		// Retrieve the data for this post
		$items = $this->getValue($field, $post);

		$value = '';

		if (is_array($items)) {
			$value = $items[0]->class_name;
		}

		if (!is_array($items) && !empty($items->class_name)) {
			$value = $items->class_name;
		}

		$theme = EB::themes();
		$theme->set('value', $value);
		$theme->set('field', $field);

		$output = $theme->output('site/fields/forms/class');

		return $output;
	}
}
