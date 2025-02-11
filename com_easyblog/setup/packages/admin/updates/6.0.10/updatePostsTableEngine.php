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

class EasyBlogMaintenanceScriptUpdatePostsTableEngine extends EasyBlogMaintenanceScript
{
	public static $title = 'Update posts db table engine to innodb' ;
	public static $description = 'Update posts table engine and to remove the fulltext index';

	public function main()
	{
		$db = EB::db();

		$defaultEngine = $this->getDefaultEngineType();
		$requireConvert = $this->isRequireConvertion();

		if ($defaultEngine != 'myisam' && $requireConvert) {
			try {
				
				$query = "ALTER TABLE `#__easyblog_post` engine=InnoDB";
				$db->setQuery($query);
				$db->query();
				
			} catch (Exception $err) {
				// do nothing.
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
			$query = "SHOW TABLE STATUS WHERE `name` LIKE " . $db->Quote('%_easyblog_post');
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
}
