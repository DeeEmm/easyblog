<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptFixComposerMenuItem extends EasyBlogMaintenanceScript
{
	public static $title = "Fix issue where composer menu item pointing to wrong location.";
	public static $description = "Fix issue where composer menu item pointing to wrong location.";

	public function main()
	{
		$config = EB::config();
		$db = EB::db();

		// update wrong link in #__menu table.
		$query = "select * FROM `#__menu` WHERE `link` LIKE '%index.php?option=com_easyblog&view=dashboard&layout=write%'";
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if ($results) {
			foreach ($results as $menu) {
				$link = $menu->link;
				$newlink = str_replace('&view=dashboard&layout=write', '&view=composer', $link);
				$id = $menu->id;

				$update = "update `#__menu` SET `link` = " . $db->Quote($newlink);
				$update .= " WHERE `id` = " . $db->Quote($id);

				$db->setQuery($update);
				$db->query();
			}
		}

		// remove old layout xml file.
		$file = JPATH_ROOT . '/components/com_easyblog/views/dashboard/tmpl/pending.xml';
		if (JFile::exists($file)) {
			JFile::delete($file);
		}

		$file = JPATH_ROOT . '/components/com_easyblog/views/dashboard/tmpl/write.xml';
		if (JFile::exists($file)) {
			JFile::delete($file);
		}

		return true;
	}

}
