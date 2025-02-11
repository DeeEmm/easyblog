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

class EasyBlogMaintenanceScriptRelocateBadges extends EasyBlogMaintenanceScript
{
	public static $title = 'Relocate Badges To Media Folder' ;
	public static $description = 'Relocating badges from component folder to media folder.';

	public function main()
	{
		// Make sure that ES is existed on the site.
		if (!EB::easysocial()->exists()) {
			return true;
		}

		$db = JFactory::getDBO();

		$query = 'UPDATE `#__social_badges` set `avatar` = CONCAT("media/com_easyblog/images/badges/", `alias`, ".png") WHERE `extension` = ' . $db->quote('com_easyblog');

		$db->setQuery($query);
		$db->execute();

		return true;
	}
}
