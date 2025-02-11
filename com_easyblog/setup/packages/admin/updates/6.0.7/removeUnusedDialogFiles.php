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

class EasyBlogMaintenanceScriptRemoveUnusedDialogFiles extends EasyBlogMaintenanceScript
{
	public static $title = 'Remove Unused Files' ;
	public static $description = 'Remove unused files for the meta restore.';

	public function main()
	{
		$files = [
			JPATH_ADMINISTRATOR . '/components/com_easyblog/themes/default/blogs/dialogs/update.blogger.meta.php'
		];

		foreach ($files as $file) {
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}

		return true;
	}
}
