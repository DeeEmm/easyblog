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

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptUpdateDocumentColumnSize extends EasyBlogMaintenanceScript
{
	public static $title = 'Update document column size in Post table.' ;
	public static $description = 'Update element from blog.user.null to blog.user.create.';

	public function main()
	{
		$db = EB::db();

		$columns = $db->getTableColumns('#__easyblog_post');

		// run the column update only when column exists
		if (in_array('document', $columns)) {

			$query = "ALTER TABLE `#__easyblog_post` MODIFY `document` LONGTEXT NOT NULL";
			$db->setQuery($query);
			$db->query();
		}

		return true;
	}
}
