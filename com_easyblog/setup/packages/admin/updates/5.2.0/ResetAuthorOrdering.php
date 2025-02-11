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

class EasyBlogMaintenanceScriptResetAuthorOrdering extends EasyBlogMaintenanceScript
{
	public static $title = "Reassign author ordering";
	public static $description = "Reassign the existing author ordering and repopulate ordering accordingly.";

	public function main()
	{
		$db = EB::db();

		$query = 'UPDATE `#__easyblog_users` SET `ordering` = `id`';
		$db->setQuery($query);
		$db->query();

		return true;
	}	
}