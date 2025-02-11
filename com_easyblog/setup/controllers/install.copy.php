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

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerInstallCopy extends EasyBlogSetupController
{
	/**
	 * Responsible to copy the necessary files over.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function execute()
	{
		// Check for development mode
		$this->checkDevelopmentMode();

		// Get which type of data we should be copying
		$type = $this->input->get('type', '');

		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// Get the path to the zip file
		$archivePath = $tmpPath . '/' . $type . '.zip';

		// Where the extracted items should reside
		$path = $tmpPath . '/' . $type;

		// Extract the admin folder
		$state = $this->ebExtract($archivePath, $path);

		if (!$state) {
			$this->setInfo(JText::sprintf('COM_EASYBLOG_INSTALLATION_COPY_ERROR_UNABLE_EXTRACT', $type), false);
			return $this->output();
		}

		// Look for files in this path
		$files = JFolder::files( $path , '.' , false , true );

		// Look for folders in this path
		$folders = JFolder::folders( $path , '.' , false , true );

		// Construct the target path first.
		if ($type == 'admin') {
			// Cleanup admin folder
			$this->cleanupAdmin();

			$target = JPATH_ADMINISTRATOR . '/components/com_easyblog';
		}

		if ($type == 'site') {
			// Cleanup site folder
			$this->cleanupSite();

			$target = JPATH_ROOT . '/components/com_easyblog';
		}

		// There could be instances where the user did not upload the launcher and just used the update feature.
		if ($type == 'languages') {

			// Copy the admin language file
			$adminFile = $path . '/admin/en-GB.com_easyblog.ini';
			JFile::copy($adminFile, JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_easyblog.ini');

			// Copy the admin system language file
			$adminFileSys = $path . '/admin/en-GB.com_easyblog.sys.ini';
			JFile::copy($adminFileSys, JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_easyblog.sys.ini');

			// Copy the site language file
			$siteFile = $path . '/site/en-GB.com_easyblog.ini';
			JFile::copy($siteFile, JPATH_ROOT . '/language/en-GB/en-GB.com_easyblog.ini');


			$this->setInfo('COM_EASYBLOG_INSTALLATION_LANGUAGES_UPDATED', true);
			return $this->output();
		}

		if ($type == 'media') {
			// Cleanup media folder
			$this->cleanupMedia();

			$target = JPATH_ROOT . '/media/com_easyblog';
		}

		// Ensure that the target folder exists
		if (!JFolder::exists($target)) {
			JFolder::create($target);
		}

		// Scan for files in the folder
		$totalFiles = 0;
		$totalFolders = 0;

		foreach ($files as $file) {
			$name = basename($file);

			$targetFile = $target . '/' . $name;

			// Copy the file
			JFile::copy($file, $targetFile);

			$totalFiles++;
		}


		// Scan for folders in this folder
		foreach ($folders as $folder) {
			$name = basename($folder);
			$targetFolder = $target . '/' . $name;

			// Copy the folder across
			JFolder::copy($folder, $targetFolder, '', true);

			$totalFolders++;
		}

		$result = $this->getResultObj(JText::sprintf('COM_EASYBLOG_INSTALLATION_COPY_FILES_SUCCESS', $totalFiles, $totalFolders), true);

		return $this->output($result);
	}

	/**
	 * Cleanup media folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function cleanupMedia()
	{
		$path = JPATH_ROOT . '/media/com_easyblog';
		$exists = JFolder::exists($path);

		if ($exists) {
			JFolder::delete($path);
		}

		return;
	}

	/**
	 * Cleanup admin folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function cleanupSite()
	{
		$path = JPATH_ROOT . '/components/com_easyblog';
		$exists = JFolder::exists($path);

		if ($exists) {

			// Look for files in this path
			$files = JFolder::files($path, '.', false, true);

			// Look for folders in this path
			$folders = JFolder::folders($path, '.', false, true);

			// before we delete everything, we must make a backup for the theme files
			$this->backupThemeFiles();

			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}

			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		return;
	}

	/**
	 * Backup Theme folders
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function backupThemeFiles()
	{
		// Doesn't matter if this is an upgrade from which version, we still need to backup
		$themePath = JPATH_ROOT . '/components/com_easyblog/themes';
		$exists = JFolder::exists($themePath);

		// if not exists, means user is doing fresh installation, no backup needed.
		if (!$exists) {
			return;
		}

		$previousVersion = $this->getPreviousVersion('scriptversion');

		// Only backup if user upgrade from 5.x
		if (version_compare($previousVersion, '6.0', '>=')) {
			return;
		}

		// Look for files in this path
		$files = JFolder::files($themePath, '.', false, true);

		// Look for folders in this path
		$folders = JFolder::folders($themePath, '.', false, true);

		$backupTarget = JPATH_ROOT . '/components/com_easyblog/backups/easyblog_v' . $previousVersion . '/themes';

		// Ensure that the target folder exists
		if (!JFolder::exists($backupTarget)) {
			JFolder::create($backupTarget);
		}

		foreach ($files as $file) {
			$name = basename($file);
			$targetFile = $backupTarget . '/' . $name;

			// Copy the file
			JFile::copy($file, $targetFile);
		}

		foreach ($folders as $folder) {
			$name = basename($folder);
			$targetFolder = $backupTarget . '/' . $name;

			// Copy the folder across
			JFolder::copy($folder, $targetFolder, '', true);
		}

		return;
	}

	/**
	 * Cleanup admin folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function cleanupAdmin()
	{
		$folders = array(
						'/components/com_easyblog/controllers',
						'/components/com_easyblog/elements',
						'/components/com_easyblog/includes',
						'/components/com_easyblog/models',
						'/components/com_easyblog/tables',
						'/components/com_easyblog/themes',
						'/components/com_easyblog/views',
						'/components/com_easyblog/defaults/blocks'
					);

		foreach ($folders as $folder) {
			$path = JPATH_ADMINISTRATOR . $folder;
			$exists = JFolder::exists($path);

			if ($exists) {
				JFolder::delete($path);
			}
		}

		return;
	}
}
