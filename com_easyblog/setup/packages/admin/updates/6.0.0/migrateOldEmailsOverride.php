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

class EasyBlogMaintenanceScriptMigrateOldEmailsOverride extends EasyBlogMaintenanceScript
{
	public static $title = 'Migrate old emails\' override';
	public static $description = 'Migrating the old email\'s template override files to the new email\'s template override';

	public function main()
	{
		$currentTemplate = EB::getCurrentTemplate();
		$basePath = JPATH_ROOT . '/templates/' . $currentTemplate . '/html/com_easyblog';
		$oldPath = $basePath . '/emails/html';
		$parentFolder = dirname($oldPath);
		$oldFiles = JFolder::files($oldPath);

		if (empty($oldFiles)) {
			return true;
		}

		foreach ($oldFiles as $file) {
			$oldFilePath = $oldPath . '/' . $file;
			$newFilePath = $parentFolder . '/' . $file;

			JFile::copy($oldFilePath, $newFilePath);
		}

		JFolder::move($oldPath, $parentFolder . '/emails_backup');

		return true;
	}
}