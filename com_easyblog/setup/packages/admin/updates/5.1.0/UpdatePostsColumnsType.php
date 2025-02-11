<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

Class EasyBlogMaintenanceScriptUpdatePostsColumnsType extends EasyBlogMaintenanceScript
{
	public static $title = 'Update column character set to utf8mb4';
	public static $description = 'Update column character set to utf8mb4';

	public function main()
	{
		$jConfig = JFactory::getConfig();
		$dbType = $jConfig->get('dbtype');

		if ($dbType == 'mysql' || $dbType == 'mysqli') {
			$db = EB::db();

			$dbversion = $db->getVersion();
			$dbversion = (float) $dbversion;

			// Only mysql version 5.5 and above is supported
			if ($dbversion >= '5.5') {
				$query = "ALTER TABLE `#__easyblog_post`";
				$query .= " MODIFY `content` longtext CHARACTER SET utf8mb4 NOT NULL,";
				$query .= " MODIFY `intro` longtext CHARACTER SET utf8mb4 NOT NULL;";

				$db->setQuery($query);
				$db->query();
			}
		}

		return true;
	}
}