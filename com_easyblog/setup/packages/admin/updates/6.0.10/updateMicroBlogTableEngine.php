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

class EasyBlogMaintenanceScriptUpdateMicroBlogTableEngine extends EasyBlogMaintenanceScript
{
	public static $title = 'Update microblog db table engine to innodb' ;
	public static $description = 'Update microblog table engine and to remove the fulltext index';

	public function main()
	{
		$db = EB::db();

		try {

			$query = 'ALTER TABLE `#__easyblog_twitter_microblog` DROP INDEX `id_str`';

			$db->setQuery($query);
			$state = $db->query();

			if ($state) {
				// now recreate index on id_str column
				$query = 'ALTER TABLE `#__easyblog_twitter_microblog` ADD INDEX `id_str` (id_str(190))';

				$db->setQuery($query);
				$state = $db->query();
			}

			// if all good, lets update the table engine to innodb
			if ($state) {
				$query = "ALTER TABLE `#__easyblog_twitter_microblog` engine=InnoDB";
				$db->setQuery($query);
				$db->query();
			}

		} catch (Exception $err) {
			// do nothing.
		}

		return true;
	}
}
