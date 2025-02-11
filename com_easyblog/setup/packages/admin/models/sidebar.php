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

use Foundry\Models\Sidebar;

class EasyBlogModelSidebar extends Sidebar
{
	/**
	 * Populate menus for the sidebar
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getMenus()
	{
		$file = JPATH_COMPONENT . '/defaults/menus.json';
		$contents = file_get_contents($file);
		$menus = json_decode($contents);

		return $menus;
	}

	/**
	 * Retrieves a specific count item based on the namespace
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getCount($namespace)
	{
		static $counters = [];

		list($model, $method) = explode('/', $namespace);

		if (!isset($counters[$namespace])) {
			$model = EB::model($model);

			$counters[$namespace] = $model->$method();
		}

		return $counters[$namespace];
	}
}
