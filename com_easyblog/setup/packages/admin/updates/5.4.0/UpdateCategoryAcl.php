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

class EasyBlogMaintenanceScriptUpdateCategoryAcl extends EasyBlogMaintenanceScript
{
	public static $title = 'Removing category permissions definition table' ;
	public static $description = 'Removing category permission definition table and updating permissions to allow specific users to publish blog into specific category';

	public function main()
	{
		$db = EB::db();

		// @task: Update #__easyblog_category_acl table.
		$query = array();
		$query[] = 'UPDATE `#__easyblog_category_acl` SET `acl_type` =';
		$query[] = 'CASE';
		$query[] = 'WHEN `acl_id` = ' . $db->Quote('1');
		$query[] = 'THEN ' . $db->Quote('view');
		$query[] = 'WHEN `acl_id` = ' . $db->Quote('2');
		$query[] = 'THEN ' . $db->Quote('create');
		$query[] = 'ELSE ' . $db->Quote('create');
		$query[] = 'END';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$db->query();

		// @task: Drop #__easyblog_category_acl_item table.
		$query = "DROP TABLE IF EXISTS `#__easyblog_category_acl_item`";

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}
}
