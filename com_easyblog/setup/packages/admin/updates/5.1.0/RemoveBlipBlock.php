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

class EasyBlogMaintenanceScriptRemoveBlipBlock extends EasyBlogMaintenanceScript
{
	public static $title = "Remove blip block";
	public static $description = "Clean up and remove unused blip block.";

	public function main()
	{
		$db = EB::db();

		$query = "DELETE FROM `#__easyblog_composer_blocks` WHERE `element` = 'blip'";
		$db->setQuery($query);
		$db->query();

		return true;
	}

}
