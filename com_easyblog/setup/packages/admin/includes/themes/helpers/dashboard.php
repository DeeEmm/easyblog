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

class EasyBlogThemesHelperDashboard
{
	/**
	 * Renders the headers for the dashboard that includes the snackbars
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function headers($heading, $actions = null, $search = null, $filters = [])
	{
		$theme = EB::themes();
		$theme->set('heading', $heading);
		$theme->set('actions', $actions);
		$theme->set('search', $search);
		$theme->set('filters', $filters);

		$output = $theme->output('site/helpers/dashboard/headers');

		return $output;
	}

	/**
	 * Deprecated. Use dashboard.headers instead
	 *
	 * @deprecated	6.0.0
	 */
	public function heading($title, $icon, $action = false)
	{
		$theme = EB::themes();

		return $theme->html('dashboard.headers', $theme->html('snackbar.heading', $title));
	}

	/**
	 * Renders the empty list for the dashboard
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function emptyList($title, $description = '', $options = [])
	{
		$icon = EB::normalize($options, 'icon', null);
		$button = EB::normalize($options, 'button', null);

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('description', $description);
		$theme->set('icon', $icon);
		$theme->set('button', $button);

		$output = $theme->output('site/helpers/dashboard/empty');

		return $output;
	}

	/**
	 * Render mini heading on the dashboard
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function miniHeading($title, $desc = null)
	{
		$title = EBString::strtoupper($title);

		if (!$desc) {
			$desc = $title . '_DESC';
		}

		$title = JText::_($title);
		$desc = JText::_($desc);

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('description', $desc);

		$output = $theme->output('site/helpers/dashboard/miniheading');

		return $output;
	}

	/**
	 * Generates a checkbox in a table
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function checkbox($element, $value, $options = [])
	{
		$theme = EB::themes();

		$disabled = false;

		if (isset($options['disabled']) && $options['disabled']) {
			$disabled = true;
		}

		$theme->set('element', $element);
		$theme->set('value', $value);
		$theme->set('disabled', $disabled);


		$output = $theme->output('site/helpers/dashboard/checkbox');

		return $output;
	}

	public static function action($title, $action, $type = 'dialog')
	{
		$title  = JText::_($title);
		$theme  = EB::themes();

		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('action', $action);

		$output = $theme->output('site/dashboard/html/item.action');

		return $output;
	}

	/**
	 * Renders a filter dropdown
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function filters($state, $filters)
	{
		$theme = EB::themes();
		$theme->set('state', $state);
		$theme->set('filters', $filters);
		$output = $theme->output('site/helpers/dashboard/filters');

		return $output;
	}

	/**
	 * Renders a filter dropdown
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function sort($label, $column, $ordering, $currentSort = '', $default = 'desc')
	{
		$label = JText::_($label);
		$default = ($default) ? $default : 'desc';

		$icon = $ordering == 'desc' ? 'fa-sort-down' : 'fa-sort-up';

		$theme = EB::themes();
		$theme->set('icon', $icon);
		$theme->set('label', $label);
		$theme->set('column', $column);
		$theme->set('ordering', $ordering);
		$theme->set('default', $default);
		$theme->set('currentSort', $currentSort);

		$output = $theme->output('site/helpers/dashboard/sort');

		return $output;
	}


	/**
	 * Renders a check all button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function checkall($disabled = false)
	{
		$theme = EB::themes();
		$theme->set('disabled', $disabled);
		$output = $theme->output('site/helpers/dashboard/checkall');

		return $output;
	}

	/**
	 * Renders label form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function label($text, $id = null)
	{
		$key = EBString::strtoupper($text);
		$text = JText::_($key);

		$theme = EB::themes();
		$theme->set('id', $id);
		$theme->set('text', $text);

		$output = $theme->output('site/helpers/dashboard/label');

		return $output;
	}

	/**
	 * Render statistics card on the dashboard overview page.
	 *
	 * Due to the front end differences in EasyBlog, which does not have the #fd wrapper,
	 * we cannot rely on stats.card from foundry
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function stats($label, $count, $icon, $url = null)
	{
		$theme = EB::themes();
		$theme->set('label', $label);
		$theme->set('count', $count);
		$theme->set('icon', $icon);
		$theme->set('url', $url);

		return $theme->output('site/helpers/dashboard/stats');
	}

	/**
	 * Render text form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function text($name, $value = '', $id = null, $options = [])
	{
		$class = 'form-control';
		$placeholder = '';
		$attributes = '';

		if (isset($options['attr']) && $options['attr']) {
			$attributes = $options['attr'];
		}

		if (isset($options['class']) && $options['class']) {
			$class = $options['class'];
		}

		if (isset($options['placeholder']) && $options['placeholder']) {
			$placeholder = JText::_($options['placeholder']);
		}

		$theme = EB::themes();
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		return $theme->output('site/helpers/dashboard/text');
	}
}
