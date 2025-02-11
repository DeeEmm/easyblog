<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptRemoveSourceColumn extends EasyBlogMaintenanceScript
{
	public static $title = 'Remove Unused Source Column' ;
	public static $description = 'Remove unused column from the post table since this source column has been removed since 3.x version.';

	public function main()
	{
		$db = EB::db();
		$columns = $db->getTableColumns('#__easyblog_post');

		if (in_array('source', $columns)) {
			$query = 'ALTER TABLE `#__easyblog_post` DROP COLUMN ' . $db->nameQuote('source');

			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}