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

class EasyBlogMaintenanceScriptRemoveNoteBlock extends EasyBlogMaintenanceScript
{
	public static $title = 'Remove the old note block';
	public static $description = 'Removing the old \'Note Block\' as it will be rebranded as \'Alerts Block\'';

	public function main()
	{
		$db = EB::db();

		$query = "delete from `#__easyblog_composer_blocks` where `element` = " . $db->Quote('note');
		$db->setQuery($query);
		$db->query();

		return true;
	}
}