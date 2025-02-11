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

class EasyBlogMaintenanceScriptUpdateDBTablesEngine extends EasyBlogMaintenanceScript
{
	public static $title = 'Update database tables engine type' ;
	public static $description = 'This script will attempt to update the existing EasyBlog table engine type to follow the default engine type used on the server.';

	public function main()
	{
		$db = EB::db();

		$defaultEngine = $this->getDefaultEngineType();
		$requireConvert = $this->isRequireConvertion();

		if ($defaultEngine != 'myisam' && $requireConvert) {
			$tables = $this->getEBTables();

			if ($tables) {
				try {
					foreach ($tables as $table) {
						$query = "alter table " . $db->nameQuote($table) . " engine=InnoDB";
						$db->setQuery($query);
						$db->query();
					}
				} catch (Exception $err) {
					// do nothing.
				}
			}
		}

		return true;
	}

	/**
	 * Get default database table engine from mysql server
	 *
	 * @since	5.4
	 * @access	public
	 */
	private function getDefaultEngineType()
	{
		$default = 'myisam';
		$db = EB::db();

		try {

			$query = "SHOW ENGINES";
			$db->setQuery($query);

			$results = $db->loadObjectList();

			if ($results) {
				foreach ($results as $item) {
					if ($item->Support == 'DEFAULT') {
						$default = strtolower($item->Engine);
						break;
					}
				}

				if ($default != 'myisam' && $default != 'innodb') {
					$default = 'myisam';
				}
			}

		} catch (Exception $err) {
			$default = 'myisam';
		}

		return $default;
	}

	/**
	 * Determine if we need to convert myisam engine to innodb
	 *
	 * @since	5.4
	 * @access	public
	 */
	private function isRequireConvertion()
	{
		$require = false;
		$db = EB::db();

		try {
			$query = "SHOW TABLE STATUS WHERE `name` LIKE " . $db->Quote('%_easyblog_configs');
			$db->setQuery($query);
			$result = $db->loadObject();

			if ($result) {
				$currentEngine = strtolower($result->Engine);
				if ($currentEngine == 'myisam') {
					$require = true; 
				}
			}

		} catch (Exception $err) {
			// do nothing.
			$require = false;
		}

		return $require;
	}

	/**
	 * Get EasyBlog tables names
	 *
	 * @since	5.4
	 * @access	public
	 */
	private function getEBTables()
	{
		$tables = array();

		try {

			// $jConfig = EB::jconfig();
			// $dbname = $jConfig->get('db');
			// $dbprefix = $jConfig->get('dbprefix');
			// $db = EB::db();

			// $query = "SELECT `table_name` FROM information_schema.tables";
			// $query .= " where `table_type` = " . $db->Quote('base table');
			// $query .= " and `table_schema` = " . $db->Quote($dbname);
			// $query .= " and `table_name` like " . $db->Quote($dbprefix . 'easyblog_%');
			// $query .= " and `table_name` NOT IN (" . $db->Quote($dbprefix . 'easyblog_twitter_microblog') . ',' . $db->Quote($dbprefix . 'easyblog_post') . ')';
			// $db->setQuery($query);
			// $tables = $db->loadColumn();

			// for now we do the manual work.
			$tables = array(
				'#__easyblog_acl',
				'#__easyblog_acl_filters',
				'#__easyblog_acl_group',
				'#__easyblog_adsense',
				'#__easyblog_associations',
				'#__easyblog_autoarticle_map',
				'#__easyblog_captcha',
				'#__easyblog_category',
				'#__easyblog_category_acl',
				'#__easyblog_category_fields_groups',
				'#__easyblog_comment',
				'#__easyblog_composer_blocks',
				'#__easyblog_configs',
				'#__easyblog_download',
				'#__easyblog_external',
				'#__easyblog_external_groups',
				'#__easyblog_favourites',
				'#__easyblog_featured',
				'#__easyblog_feedburner',
				'#__easyblog_feeds',
				'#__easyblog_feeds_history',
				'#__easyblog_fields',
				'#__easyblog_fields_filter',
				'#__easyblog_fields_groups',
				'#__easyblog_fields_groups_acl',
				'#__easyblog_fields_values',
				'#__easyblog_hashkeys',
				'#__easyblog_languages',
				'#__easyblog_likes',
				'#__easyblog_mailq',
				'#__easyblog_media',
				'#__easyblog_meta',
				'#__easyblog_migrate_content',
				'#__easyblog_oauth',
				'#__easyblog_oauth_logs',
				'#__easyblog_oauth_posts',
				'#__easyblog_post_assets',
				'#__easyblog_post_category',
				'#__easyblog_post_rejected',
				'#__easyblog_post_tag',
				'#__easyblog_post_templates',
				'#__easyblog_ratings',
				'#__easyblog_reactions',
				'#__easyblog_reactions_history',
				'#__easyblog_reports',
				'#__easyblog_revisions',
				'#__easyblog_subscriptions',
				'#__easyblog_tag',
				'#__easyblog_team',
				'#__easyblog_team_groups',
				'#__easyblog_team_post',
				'#__easyblog_team_request',
				'#__easyblog_team_users',
				'#__easyblog_themes_overrides',
				'#__easyblog_uploader_tmp',
				'#__easyblog_users',
				'#__easyblog_xml_wpdata'
			);

		} catch (Exception $err) {
			// do nothing.
		}

		return $tables;
	}
}
