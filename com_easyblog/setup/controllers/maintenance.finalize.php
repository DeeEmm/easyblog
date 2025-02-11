<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include parent library
require_once( dirname( __FILE__ ) . '/controller.php' );

class EasyBlogControllerMaintenanceFinalize extends EasyBlogSetupController
{
	public function execute()
	{
		$this->engine();

		$version = $this->getInstalledVersion();

		// Update the version in the database to the latest now
		$config = EB::table('Configs');
		$config->load(array('name' => 'scriptversion'));

		$config->name = 'scriptversion';
		$config->params = $version;

		// Save the new config
		$config->store($config->name);

		// Remove any folders in the temporary folder.
		$this->cleanup(SI_TMP);

		// Remove installation temporary file
		JFile::delete(JPATH_ROOT . '/tmp/easyblog.installation');

		// Update installation package to 'launcher'
		$this->updatePackage();

		$result = $this->getResultObj(JText::sprintf('COM_EASYBLOG_INSTALLATION_MAINTENANCE_UPDATED_MAINTENANCE_VERSION', $version), 1, 'success');

		return $this->output($result);
	}

	/**
	 * Perform system wide cleanups after the installation is completed.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function cleanup($path)
	{
		$folders = JFolder::folders($path, '.', false, true);
		$files = JFolder::files($path, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}
		}

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}
	}

	/**
	 * Update installation package to launcher package to update issue via update button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function updatePackage()
	{
		// For beta, we need to update the setup script
		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/setup/bootstrap.php';

		// Read the contents
		$contents = file_get_contents($path);

		$contents = str_ireplace("define('EB_INSTALLER', 'full');", "define('EB_INSTALLER', 'launcher');", $contents);
		$contents = preg_replace('/define\(\'EB_PACKAGE\', \'.*\'\);/i', "define('EB_PACKAGE', '');", $contents);

		JFile::write($path, $contents);
	}
}
