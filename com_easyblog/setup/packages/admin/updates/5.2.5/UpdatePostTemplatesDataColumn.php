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

class EasyBlogMaintenanceScriptUpdatePostTemplatesDataColumn extends EasyBlogMaintenanceScript
{
	public static $title = "Update Post templates table data column type";
	public static $description = "Update the data column type to longtext in order to store more data.";

	public function main()
	{
		$db = EB::db();

		$query = 'ALTER TABLE `#__easyblog_post_templates` MODIFY `data` LONGTEXT';

		$db->setQuery($query);
		$db->query();

		return true;
	}
}
