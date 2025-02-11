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

class EasyBlogMaintenanceScriptCleanupCategoryAcl extends EasyBlogMaintenanceScript
{
	public static $title = "Cleanup Category Acls";
	public static $description = "Remove category acls for those deleted categories.";

	public function main()
	{

		$state = true;
		$db = EB::db();

		$query = "DELETE a FROM `#__easyblog_category_acl` as a";
		$query .= " LEFT JOIN `#__easyblog_category` as b on a.`category_id` = b.`id`";
		$query .= " WHERE b.`id` IS NULL";

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}

}
