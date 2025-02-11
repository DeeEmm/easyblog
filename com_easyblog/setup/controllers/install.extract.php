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

class EasyBlogControllerInstallExtract extends EasyBlogSetupController
{
	/**
	 * For users who utilise the full installer, we need to extract it first
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function execute()
	{
		// Get the package
		$package = SI_PACKAGE;

		// Construct storage path
		$storage = SI_PACKAGES . '/' . $package;

		$exists = JFile::exists($storage);

		// Test if package really exists
		if (!$exists) {
			$this->setInfo('COM_EASYBLOG_INSTALLATION_ERROR_PACKAGE_DOESNT_EXIST', false);
			return $this->output();
		}

		// Check if the temporary folder exists
		if (!JFolder::exists(SI_TMP)) {
			JFolder::create(SI_TMP);
		}

		// Generate a temporary folder name
		$fileName = 'com_easyblog_package_' . uniqid();
		$tmp = SI_TMP . '/' . $fileName;

		// Delete any folders that already exists
		if (JFolder::exists($tmp)) {
			JFolder::delete($tmp);
		}

		// Try to extract the files
		$state = $this->ebExtract($storage, $tmp);

		// Regardless of the extraction state, delete the zip file.
		@JFile::delete($storage);

		if (!$state) {
			$this->setInfo('COM_EASYBLOG_INSTALLATION_ERROR_EXTRACT_ERRORS', false);
			return $this->output();
		}

		$this->setInfo('COM_EASYBLOG_INSTALLATION_EXTRACT_SUCCESS', true, array('path' => $tmp));
		return $this->output();
	}
}
