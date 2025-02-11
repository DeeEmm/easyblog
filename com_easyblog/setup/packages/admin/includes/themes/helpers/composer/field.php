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

class EasyBlogThemesHelperComposerField
{
	/**
	 * Renders an alignment option on the composer panel
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function alignment($selected = null, $attributes = [])
	{
		if (!$selected) {
			$selected = 'center';
		}

		$wrapperAttribute = EB::normalize($attributes, 'wrapperAttribute', '');
		$types = ['left', 'center', 'right'];

		$theme = EB::themes();
		$theme->set('types', $types);
		$theme->set('selected', $selected);
		$theme->set('wrapperAttribute', $wrapperAttribute);

		$output = $theme->output('site/helpers/composer/field/alignment');

		return $output;
	}

	/**
	 * Renders a calendar field in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function calendar($name, $value, $displayValue = '', $options = [])
	{
		$id = EB::normalize($options, 'id', $name);
		$placeholder = EB::normalize($options, 'placeholder', null);
		$attributes = EB::normalize($options, 'attributes', '');
		$calendarFormat = EB::normalize($options, 'format', 'COM_EASYBLOG_MOMENTJS_DATE_DMY24H');
		$calendarEmptyText = EB::normalize($options, 'emptyText', 'COM_EASYBLOG_COMPOSER_NOW');
		$calendarLocale = EB::getMomentLanguage();

		$hash = md5($name);

		$theme = EB::themes();
		$theme->set('hash', $hash);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('displayValue', $displayValue);
		$theme->set('id', $id);
		$theme->set('placeholder', $placeholder);
		$theme->set('attributes', $attributes);
		$theme->set('calendarLocale', $calendarLocale);
		$theme->set('calendarFormat', $calendarFormat);
		$theme->set('calendarEmptyText', $calendarEmptyText);

		$output = $theme->output('site/helpers/composer/field/calendar');

		return $output;
	}

	/**
	 * Renders a checkbox field in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function checkbox($name, $title, $value = null, $attributes = array())
	{
		$title = JText::_($title);

		if (is_array($attributes) && !empty($attributes)) {
			$attributes = implode(' ', $attributes);
		} else {
			$attributes = '';
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/composer/field/checkbox');

		return $output;
	}

	/**
	 * Renders a label for a field in composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function label($title, $target = null, $showInfo = false)
	{
		$info = false;

		if ($showInfo) {
			$info = Jtext::_($title . '_DESC');
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('target', $target);
		$theme->set('info', $info);

		$output = $theme->output('site/helpers/composer/field/label');

		return $output;
	}

	/**
	 * Renders a language field in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function language($name, $value, $options = [])
	{
		$id = EB::normalize($options, 'id', $name);

		$theme = EB::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);

		$output = $theme->output('site/helpers/composer/field/language');

		return $output;
	}

	/**
	 * Renders a password field in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function password($name, $value, $options = [])
	{
		$id = EB::normalize($options, 'id', $name);
		$placeholder = EB::normalize($options, 'placeholder', null);
		$attributes = EB::normalize($options, 'attributes', '');
		$mask = EB::normalize($options, 'mask', true);

		$theme = EB::themes();
		$theme->set('mask', $mask);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('id', $id);
		$theme->set('placeholder', $placeholder);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/composer/field/password');

		return $output;
	}

	/**
	 * Renders a privacy field in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function privacy($name, $value, $options = [])
	{
		$id = EB::normalize($options, 'id', $name);
		$author = EB::normalize($options, 'author', JFactory::getUser()->id);
		$privacyOptions = EB::privacy()->getOptions('', $author);

		$theme = EB::themes();
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('id', $id);
		$theme->set('privacyOptions', $privacyOptions);

		$output = $theme->output('site/helpers/composer/field/privacy');

		return $output;
	}

	/**
	 * Renders a radio in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function radio($name, $title, $id = null, $options = [])
	{
		$disabled = EB::normalize($options, 'disabled', false);
		$checked = EB::normalize($options, 'checked', false);
		$attributes = EB::normalize($options, 'attributes', '');
		$textClass = EB::normalize($options, 'textClass', '');

		if (!$id) {
			$id = $name;
		}

		$theme = EB::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('title', JText::_($title));
		$theme->set('disabled', $disabled);
		$theme->set('checked', $checked);
		$theme->set('attributes', $attributes);
		$theme->set('textClass', $textClass);

		$output = $theme->output('site/helpers/composer/field/radio');

		return $output;
	}

	/**
	 * Renders a text field in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function text($name, $value, $options = [])
	{
		$id = EB::normalize($options, 'id', $name);
		$placeholder = EB::normalize($options, 'placeholder', null);
		$attributes = EB::normalize($options, 'attributes', '');

		$theme = EB::themes();
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('id', $id);
		$theme->set('placeholder', $placeholder);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/composer/field/text');

		return $output;
	}

	/**
	 * Renders a textarea field in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function textarea($name, $value, $options = [])
	{
		$id = EB::normalize($options, 'id', $name);
		$placeholder = EB::normalize($options, 'placeholder', null);
		$attributes = EB::normalize($options, 'attributes', '');
		$rows = EB::normalize($options, 'rows', 5);

		$theme = EB::themes();
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('id', $id);
		$theme->set('placeholder', $placeholder);
		$theme->set('attributes', $attributes);
		$theme->set('rows', $rows);

		$output = $theme->output('site/helpers/composer/field/textarea');

		return $output;
	}

	/**
	 * Renders a toggle field in the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function toggler($name, $value, $options = [])
	{
		// $id = EB::normalize($options, 'id', $name);
		// $attributes = EB::normalize($options, 'attributes', '');

		// $theme = EB::themes();
		// $theme->set('name', $name);
		// $theme->set('value', $value);
		// $theme->set('id', $id);
		// $theme->set('attributes', $attributes);
		// $theme->set('options', $options);
		// $theme->set('disabled', $disabled);
		// $theme->set('disabledTitle', $disabledTitle);
		// $theme->set('disabledDesc', $disabledDesc);
		// $theme->set('attributes', $attributes);
		// $theme->set('dependency', $dependency);
		// $theme->set('dependencyValue', $dependencyValue);

		// $output = $theme->output('site/helpers/composer/field/toggler');

		// return $output;

		$id = EB::normalize($options, 'id', $name);
		$attributes = EB::normalize($options, 'attributes', '');

		$disabled = false;

		if (is_array($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		// Target dependencies option allows this option to display / hide dependency items
		// based on the current toggle value
		$dependency = EB::normalize($options, 'dependency', '');
		$dependencyValue = EB::normalize($options, 'dependencyValue', 1);

		// Ensure it does not have any double quotes
		if ($dependency) {
			$dependency = str_ireplace('"', '\'', $dependency);
		}

		// Determines if the input has been disabled
		$disabled = EB::normalize($options, 'disabled', false);
		$disabledDesc = EB::normalize($options, 'disabledDesc', '');
		$disabledTitle = EB::normalize($options, 'disabledTitle', '');

		if (!$id) {
			$id = $name;
		}

		$theme = EB::themes();
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('id', $id);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);
		$theme->set('disabled', $disabled);
		$theme->set('disabledTitle', $disabledTitle);
		$theme->set('disabledDesc', $disabledDesc);
		$theme->set('attributes', $attributes);
		$theme->set('dependency', $dependency);
		$theme->set('dependencyValue', $dependencyValue);

		$output = $theme->output('site/helpers/composer/field/toggler');

		return $output;
	}

	/**
	 * Renders a dropdown for the composer panel
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function dropdown($name, $selected = '', $values = [], $options = [], $useValue = false)
	{
		$class = EB::normalize($options, 'class', '');
		$attributes = EB::normalize($options, 'attributes', '');

		$themes = EB::themes();
		$themes->set('values', $values);
		$themes->set('attributes', $attributes);
		$themes->set('name', $name);
		$themes->set('class', $class);
		$themes->set('selected', $selected);
		$themes->set('useValue', $useValue);

		return $themes->output('site/helpers/composer/field/dropdown');
	}
}
