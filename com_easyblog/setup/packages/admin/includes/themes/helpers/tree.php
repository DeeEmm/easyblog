<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogThemesHelperTree
{
	/**
	 * Renders the user group tree listings
	 *
	 * @since   5.2.6
	 * @access  public
	 */
	public static function groups($name = 'gid', $selected = '', $exclude = array(), $checkSuperAdmin = false)
	{
		static $count;

		$count++;

		// If selected value is a string, we assume that it's a json object.
		if (is_string($selected)) {
			$selected = json_decode($selected);
		}

		$groups = EB::getUsergroupsIds();

		if (!is_array($selected)) {
			$selected = array($selected);
		}

		$isSuperAdmin = JFactory::getUser()->authorise('core.admin');

		$theme 	= EB::themes();
		$theme->set('name', $name);
		$theme->set('checkSuperAdmin', $checkSuperAdmin);
		$theme->set('isSuperAdmin', $isSuperAdmin);
		$theme->set('selected', $selected);
		$theme->set('count', $count);
		$theme->set('groups', $groups);

		return $theme->output('admin/html/tree.groups');
	}
}
