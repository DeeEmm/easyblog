<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptUpdatePostsColumns extends EasyBlogMaintenanceScript
{
	public static $title = "Update posts table columns to 5.1";
	public static $description = "Update posts table columns to 5.1";

	public function main()
	{
		$config = EB::config();
		$db = EB::db();

		$query = "select count(1) from `#__easyblog_post` where `version` = ''";
		$db->setQuery($query);

		$result = $db->loadResult();

		if ($result) {
			$query = "update `#__easyblog_post` set `version` = '5.0.44'";
			$query .= " where `version` = ''";
			$db->setQuery($query);
			$db->query();
		}


		// $columns = $db->getTableColumns('#__easyblog_post');

		// if (! in_array('version', $columns)) {

		// 	// lets create db columns here.
		// 	$update = "ALTER TABLE `#__easyblog_post` ADD COLUMN `version` varchar(10) default ''";
		// 	$db->setQuery($update);
		// 	$state = $db->query();

		// 	if ($state) {
		// 		// now lets update the value of this new column on existing blog posts.
		// 		$query = "update `#__easyblog_post` set `version` = '5.0.44'";
		// 		$db->setQuery($query);
		// 		$db->query();
		// 	}
		// }

		return true;
	}

}
