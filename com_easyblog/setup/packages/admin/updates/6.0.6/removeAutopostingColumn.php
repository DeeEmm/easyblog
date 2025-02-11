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

class EasyBlogMaintenanceScriptRemoveAutopostingColumn extends EasyBlogMaintenanceScript
{
	public static $title = 'Remove Unused Autoposting Column' ;
	public static $description = 'Remove unused column from the post table since this autoposting column has been removed since 3.x version.';

	public function main()
	{
		$db = EB::db();

		$query = 'SHOW COLUMNS FROM `#__easyblog_post` LIKE ' . $db->Quote('autoposting');
		$db->setQuery($query);
		$exists = $db->loadColumn();

		if ($exists) {
			$query = 'ALTER TABLE `#__easyblog_post` DROP COLUMN ' . $db->nameQuote('autoposting');
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}