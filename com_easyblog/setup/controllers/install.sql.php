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

class EasyBlogControllerInstallSql extends EasyBlogSetupController
{
	/**
	 * Perform installation of SQL queries
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function execute()
	{
		// Check for development mode
		$this->checkDevelopmentMode();

		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// There should be a queries.zip archive in the archive.
		$tmpQueriesPath = $tmpPath . '/queries.zip';

		// Extract the queries
		$path = $tmpPath . '/queries';

		// Check if this folder exists.
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}

		// Extract the archive now
		$state = $this->ebExtract($tmpQueriesPath, $path);

		if (!$state) {
			$this->setInfo('COM_EASYBLOG_INSTALLATION_ERROR_UNABLE_EXTRACT_QUERIES', false);
			return $this->output();
		}

		// Get the list of files in the folder.
		$queryFiles = JFolder::files($path, '.', true, true);

		// When there are no queries file, we should just display a proper warning instead of exit
		if (!$queryFiles) {
			$this->setInfo('COM_EASYBLOG_INSTALLATION_ERROR_EMPTY_QUERIES_FOLDER', false);
			return $this->output();
		}

		$db = JFactory::getDBO();
		$isMySQL = $this->isMySQL();
		$total = 0;

		foreach ($queryFiles as $file) {
			// Get the contents of the file
			$contents = file_get_contents($file);
			$queries = $this->splitSql($contents);

			foreach ($queries as $query) {
				$query = trim($query);

				if ($isMySQL && !$this->hasUTF8mb4Support()) {
					$query = $this->convertUtf8mb4QueryToUtf8($query);
				}

				if (!empty($query)) {
					$db->setQuery($query);
					$state = $db->execute();
				}
			}
			$total += 1;
		}

		// lets fix the created_by id
		$this->fixBlogAuthorId();

		$this->setInfo(JText::sprintf('COM_EASYBLOG_INSTALLATION_SQL_EXECUTED_SUCCESS', $total), true);
		return $this->output();
	}


	public function fixBlogAuthorId()
	{
		// assuming the user that logged into backed installer will be a superadmin as well.
		$my = JFactory::getUser();

		$db = JFactory::getDBO();

		$query = "update `#__easyblog_post` set `created_by` = " . $db->Quote($my->id);
		$query .= " where `created_by` = 0";

		$db->setQuery($query);
		$this->ebQuery($db);

		return true;
	}

}
