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

class EasyBlogThemesHelperComposer extends EasyBlogThemesHelperAbstract
{
	/**
	 * Deprecated. Temporarily being used to serve existing fields. This needs to be refactored in the future
	 *
	 * @deprecated	6.0.0
	 */
	public function field($type, $name, $title, $value = null, $attributes = [], $arrayAttributes = [])
	{
		$title = JText::_($title);

		// We may allow user to provide us with a string as attribute
		if (!is_array($attributes)) {
			$attributes = [$attributes];
		}

		if (!$attributes && $arrayAttributes) {
			$attributes = $arrayAttributes;
		}

		// Ensure that attributes are always strings
		if (is_array($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		$theme = EB::themes();
		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/composer/field');

		return $output;
	}

	/**
	 * Renders a fieldset in the composer panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function fieldset($type, $name, $title, $value = null, $attributes = array(), $help = '', $wrapperAttributes = array())
	{
		$title = JText::_($title);

		if ($help) {
			$help = JText::_($help);
		}

		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}

		if (is_array($attributes) && !empty($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		if (!is_array($wrapperAttributes)) {
			$wrapperAttributes = array($wrapperAttributes);
		}

		if (is_array($wrapperAttributes) && !empty($wrapperAttributes)) {
			$wrapperAttributes = implode(' ', $wrapperAttributes);
		}

		if (!$wrapperAttributes) {
			$wrapperAttributes = '';
		}

		$theme = EB::themes();
		$theme->set('wrapperAttributes', $wrapperAttributes);
		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('help', $help);

		$output = $theme->output('site/helpers/composer/fieldset');

		return $output;
	}

	/**
	 * Renders the composer label
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function label($title, $for = '', $options = [])
	{
		$title = JText::_($title);
		$class = EB::normalize($options, 'wrapperClass', '');

		$theme = EB::themes();
		$theme->set('class', $class);
		$theme->set('title', $title);
		$theme->set('for', $for);

		$output = $theme->output('site/helpers/composer/label');

		return $output;
	}

	/**
	 * Renders the composer shortcut
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function shortcut($keys)
	{
		if (!$this->config->get('layout_composer_shortcuts')) {
			return;
		}

		$themes = EB::themes();
		$themes->set('keys', $keys);

		$output = $themes->output('site/composer/shortcuts/tooltip');

		return $output;
	}
}
