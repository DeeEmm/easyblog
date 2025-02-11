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

class EasyBlogThemesHelperFilter
{
	/**
	 * Renders the user's group tree
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function published($name = 'state', $selected = 'all' )
	{
		$theme = EB::themes();

		$theme->set('name', $name);
		$theme->set('selected', $selected);

		$contents = $theme->output('admin/html/filters/published');

		return $contents;
	}

	/**
	 * Displays the dropdown to filter number of items per page
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function limit($selected = 5, $name = 'limit', $step = 5, $min = 5, $max = 100, $showAll = true)
	{
		$theme = EB::themes();

		$theme->set('selected', $selected);
		$theme->set('name', $name);
		$theme->set('step', $step);
		$theme->set('min', $min);
		$theme->set('max', $max);
		$theme->set('showAll', $showAll);

		$contents = $theme->output('admin/html/filters/limit');

		return $contents;
	}

	/**
	 * Displays a search box in the filter
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function search($value = '', $name = 'search', $tooltip = '')
	{
		if ($tooltip) {
			$tooltip = JText::_($tooltip);
		}

		$theme = EB::themes();

		$theme->set('tooltip', $tooltip);
		$theme->set('value', $value);
		$theme->set('name', $name);

		$contents = $theme->output('admin/html/filters/search');

		return $contents;
	}

	public static function lists($items = [], $name = 'listitem', $selected = 'all', $initial = '', $initialValue = 'all')
	{
		$theme = EB::themes();

		$theme->set('initialValue', $initialValue);
		$theme->set('initial', $initial);
		$theme->set('name', $name);
		$theme->set('items', $items);
		$theme->set('selected', $selected);

		$contents = $theme->output('admin/html/filters/lists');

		return $contents;
	}

	public static function custom($html)
	{
		$theme = EB::themes();

		$theme->set('html', $html);
		$contents = $theme->output('admin/html/filters/custom');

		return $contents;
	}
}
