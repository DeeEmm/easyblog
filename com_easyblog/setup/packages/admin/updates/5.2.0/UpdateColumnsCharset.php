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

class EasyBlogMaintenanceScriptUpdateColumnsCharset extends EasyBlogMaintenanceScript
{
	public static $title = "Update database columns charset";
	public static $description = "Update database columns charset to support utf8mb4 if database server supported this charset.";

	public function main()
	{
		$jConfig = JFactory::getConfig();
		$dbType = $jConfig->get('dbtype');

		$db = EB::db();

		if (($dbType == 'mysql' || $dbType == 'mysqli') && $db->hasUTF8mb4Support()) {

			$queries = array();

			// posts
			$query = "ALTER TABLE `#__easyblog_post`";
			$query .= " MODIFY `title` text CHARACTER SET utf8mb4 NOT NULL,";
			$query .= " MODIFY `permalink` text CHARACTER SET utf8mb4 NOT NULL,";
			$query .= " MODIFY `content` longtext CHARACTER SET utf8mb4 NOT NULL,";
			$query .= " MODIFY `intro` longtext CHARACTER SET utf8mb4 NOT NULL;";
			$queries[] = $query;

			// revision
			$query = "ALTER TABLE `#__easyblog_revisions`";
			$query .= " MODIFY `content` longtext CHARACTER SET utf8mb4 NOT NULL;";
			$queries[] = $query;

			// categories
			// first we need to drop the index
			$query = "ALTER TABLE `#__easyblog_category` drop index `idx_category_alias_id`;";
			$queries[] = $query;

			$query = "ALTER TABLE `#__easyblog_category`";
			$query .= " MODIFY `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL,";
			$query .= " MODIFY `alias` varchar(255) CHARACTER SET utf8mb4 NOT NULL,";
			$query .= " MODIFY `description` text CHARACTER SET utf8mb4 NOT NULL;";
			$queries[] = $query;

			// reinsert index
			$query = "ALTER TABLE `#__easyblog_category` add index `idx_category_alias_id` (`alias` (200), `id`);";
			$queries[] = $query;

			// tags
			// first we need to drop the index
			$query = "ALTER TABLE `#__easyblog_tag` drop index `easyblog_tag_query1`;";
			$queries[] = $query;

			$query = "ALTER TABLE `#__easyblog_tag`";
			$query .= " MODIFY `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL,";
			$query .= " MODIFY `alias` varchar(255) CHARACTER SET utf8mb4 NULL;";
			$queries[] = $query;

			// first we need to drop the index
			$query = "ALTER TABLE `#__easyblog_tag` add index `easyblog_tag_query1` (`published`, `id`, `title` (200));";
			$queries[] = $query;

			// teamblog
			$query = "ALTER TABLE `#__easyblog_team`";
			$query .= " MODIFY `title` text CHARACTER SET utf8mb4 NOT NULL,";
			$query .= " MODIFY `alias` varchar(255) CHARACTER SET utf8mb4 NULL,";
			$query .= " MODIFY `description` text CHARACTER SET utf8mb4 NOT NULL;";
			$queries[] = $query;

			// users
			$query = "ALTER TABLE `#__easyblog_users`";
			$query .= " MODIFY `permalink` varchar(255) CHARACTER SET utf8mb4 NULL;";
			$queries[] = $query;

			// mailq.body
			$query = "ALTER TABLE `#__easyblog_mailq`";
			$query .= " MODIFY `body` text CHARACTER SET utf8mb4 NOT NULL;";
			$queries[] = $query;

			foreach ($queries as $query) {
				$db->setQuery($query);
				$db->query();
			}

		}

		return true;
	}

}
