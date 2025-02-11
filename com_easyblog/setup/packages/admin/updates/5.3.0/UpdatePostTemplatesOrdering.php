<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptUpdatePostTemplatesOrdering extends EasyBlogMaintenanceScript
{
	public static $title = "Update ordering for post templates";
	public static $description = "Update ordering for post templates";

	public function main()
	{
		$db = EB::db();
		$query = 'select `id` from `#__easyblog_post_templates` where ordering = 0';

		$db->setQuery($query);
		$exists = $db->loadObject();

		if (!$exists) {
			return true;
		}
		
		$query = 'select `id` from `#__easyblog_post_templates` order by ordering';

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (count($rows) > 0) {
			$orderNum = '1';

			foreach ($rows as $row) {
				$query = 'update `#__easyblog_post_templates` set';
				$query .= ' `ordering` = ' . $db->Quote($orderNum);
				$query .= ' where `id` = ' . $db->Quote($row->id);

				$db->setQuery($query);
				$db->query();

				$orderNum++;
			}
		}

		return true;
	}
}
