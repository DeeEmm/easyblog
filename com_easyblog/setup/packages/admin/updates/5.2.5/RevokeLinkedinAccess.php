<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptRevokeLinkedinAccess extends EasyBlogMaintenanceScript
{
	public static $title = "Revoke Linkedin Oauth 1 Access";
	public static $description = "Revoke all Linkedin oauth 1 access so that oauth 2 can be use.";

	public function main()
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_oauth`';
		$query .= ' WHERE `type` = ' . $db->Quote('linkedin');

		// In previous oauth 1, we do not store the expiry date.
		$query .= ' AND `expires` IS NULL';
		$query .= ' OR `expires` = ' . $db->Quote('0000-00-00 00:00:00');

		$db->setQuery($query);
		$db->query();

		return true;
	}
}
