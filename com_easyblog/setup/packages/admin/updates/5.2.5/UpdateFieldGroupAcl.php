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

class EasyBlogMaintenanceScriptUpdateFieldGroupAcl extends EasyBlogMaintenanceScript
{
	public static $title = "Update Field Group ACL To Use New Structure";
	public static $description = "Update Field Group ACL To Use New Database Structure";

	public function main()
	{
		$db = EB::db();

		// Check whether data already exists
		$query = 'SELECT count(*) FROM `#__easyblog_fields_groups_acl`';
		$db->setQuery($query);

		$result = $db->loadResult();

		if ($result > 0) {
			return true;
		}

		$query = 'SELECT * from `#__easyblog_fields_groups`';
		$db->setQuery($query);

		$results = $db->loadObjectList();

		if ($results) {
			foreach ($results as $result) {
				if (!$result->write && !$result->read) {
					continue;
				}

				// process write
				$write = json_decode($result->write);

				if ($write) {
					foreach ($write as $aclId) {
						$table = EB::table('fieldGroupAcl');
						$table->group_id = $result->id;
						$table->acl_id = $aclId;
						$table->acl_type = 'write';

						$table->store();
					}
				}

				$read = json_decode($result->read);

				if ($read) {
					foreach ($read as $aclId) {
						$table = EB::table('fieldGroupAcl');
						$table->group_id = $result->id;
						$table->acl_id = $aclId;
						$table->acl_type = 'read';

						$table->store();
					}
				}
			}
		}

		return true;
	}
}
