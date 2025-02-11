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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.database.driver');
jimport('joomla.installer.helper');

class EasyBlogSetupController
{
	private $result = array();

	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
	}

	/**
	 * Checks for development mode
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function checkDevelopmentMode()
	{
		if ($this->isDevelopment()) {
			return $this->outputDevelopment();
		}
	}

	protected function data($key, $value)
	{
		$obj = new stdClass();
		$obj->$key = $value;

		$this->result[] = $obj;
	}

	public function setInfo($message, $state = true, $args = array())
	{
		$result = new stdClass();
		$result->state = $state;
		$result->message = JText::_($message);

		if (!empty($args)) {
			foreach ($args as $key => $val) {
				$result->$key = $val;
			}
		}

		$this->result = $result;
	}

	public function output($data = array())
	{
		header('Content-type: text/x-json; UTF-8');

		if (empty($data)) {
			$data = $this->result;
		}

		echo json_encode($data);
		exit;
	}

	/**
	 * Outputs development notice
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function outputDevelopment()
	{
		return $this->output($this->getResultObj('Develpment mode, nothing happened.', true));
	}

	/**
	 * Allows caller to set the data
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getResultObj($message, $state, $stateMessage = '')
	{
		$obj = new stdClass();
		$obj->state = $state;
		$obj->stateMessage = $stateMessage;
		$obj->message = JText::_($message);

		return $obj;
	}

	/**
	 * Get's the version of this launcher so we know which to install
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getVersion()
	{
		static $version = null;

		if (is_null($version)) {

			// Get the version from the manifest file
			$contents = file_get_contents(JPATH_ROOT. '/administrator/components/com_easyblog/easyblog.xml');
			$parser = simplexml_load_string($contents);

			$version = $parser->xpath('version');
			$version = (string) $version[0];
		}

		return $version;
	}

	/**
	 * Retrieve the Joomla Version
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getJoomlaVersion()
	{
		$jVerArr = explode('.', JVERSION);
		$jVersion = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	/**
	 * Gets the info about the latest version
	 *
	 * @since	5.0.42
	 * @access	public
	 */
	public function getInfo($update = false)
	{
		$resource = curl_init();

		// If this is an update, we want to tell the server that this is being updated from which version
		$version = $this->getVersion();

		// We need to pass the api keys to the server
		curl_setopt($resource, CURLOPT_POST, true);
		curl_setopt($resource, CURLOPT_POSTFIELDS, 'apikey=' . SI_KEY . '&from=' . $version);
		curl_setopt($resource, CURLOPT_URL, SI_MANIFEST);
		curl_setopt($resource, CURLOPT_TIMEOUT, 120);
		curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($resource);
		curl_close($resource);

		if (!$result) {
			return false;
		}

		$obj = json_decode($result);

		return $obj;
	}

	/**
	 * Loads up the EasyBlog library if it exists
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function engine()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!JFile::exists($file)) {
			return false;
		}

		// Include foundry framework
		require_once($file);
	}

	/**
	 * Loads the previous version that was installed
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getInstalledVersion()
	{
		$this->engine();

		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/easyblog.xml';
		$contents = file_get_contents($path);

		$parser = simplexml_load_string($contents);

		$version = $parser->xpath('version');
		$version = (string) $version[0];

		return $version;
	}

	/**
	 * get a configuration item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getPreviousVersion($versionType)
	{
		// Render EasyBlog engine
		$this->engine();

		$table = EB::table('Configs');
		$exists = $table->load(array('name' => $versionType));

		if ($exists) {

			$currentVersion = $table->params;

			// Do the extra checking here to make sure that this current version is valid
			// e.g. one the case if the version is 3.x but the site is actually running 5.x or 6.x
			// use to prevent run any unnecessary maintenance script
			if ($currentVersion <= '4.0') {

				$isEasyblogVersion3 = $this->isEasyblogVersion3();

				// update the params version to 5.x if this is not Easyblog version 3.x
				// So do not need to run the 3.x maintenance script
				if (!$isEasyblogVersion3) {
					$table->params = '5.0.0';
				}
			}

			return $table->params;
		}

		// there is no value of the version type. return false.
		return false;
	}

	/**
	 * Determine if the previous version is valid or not
	 *
	 * @since	6.0.6
	 * @access	public
	 */
	public function isEasyblogVersion3()
	{
		static $_cache = null;

		if (is_null($_cache)) {

			$db = JFactory::getDBO();
			$query = 'SHOW COLUMNS FROM `#__easyblog_post` LIKE ' . $db->Quote('document');
			$db->setQuery($query);
			$columns = $db->loadColumn();

			// if the `document` column not exist mean this current version must be 3.x
			$_cache = count($columns) <= 0 ? true : false;

			return $_cache;
		}

		return $_cache;
	}

	/**
	 * Determines if we are in development mode
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function isDevelopment()
	{
		$session = JFactory::getSession();
		$developer = $session->get('easyblog.developer');

		return $developer;
	}


	/**
	 * Saves a configuration item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function updateConfig($key, $value)
	{
		$this->engine();

		$config = EB::config();
		$config->set($key, $value);

		$jsonString = $config->toString();

		$table = EB::table('Configs');
		$exists = $table->load(array('name' => 'config'));

		if (!$exists) {
			$table->type = 'config';
		}

		$table->params = $jsonString;
		$table->store();
	}

	/**
	 * Determine if database is set to mysql or not.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function isMySQL()
	{
		$jConfig = JFactory::getConfig();
		$dbType = $jConfig->get('dbtype');

		return $dbType == 'mysql' || $dbType == 'mysqli';
	}

	/**
	 * Determine if mysql can support utf8mb4 or not.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function hasUTF8mb4Support()
	{
		static $_cache = null;

		if (is_null($_cache)) {

			$db = JFactory::getDBO();

			if (method_exists($db, 'hasUTF8mb4Support')) {
				$_cache = $db->hasUTF8mb4Support();
				return $_cache;
			}

			// we check the server version 1st
			$server_version = $db->getVersion();
			if (version_compare($server_version, '5.5.3', '<')) {
				 $_cache = false;
				 return $_cache;
			}

			$client_version = '5.0.0';

			if (function_exists('mysqli_get_client_info')) {
				$client_version = mysqli_get_client_info();
			} else if (function_exists('mysql_get_client_info')) {
				$client_version = mysql_get_client_info();
			}

			if (strpos($client_version, 'mysqlnd') !== false) {
				$client_version = preg_replace('/^\D+([\d.]+).*/', '$1', $client_version);
				$_cache = version_compare($client_version, '5.0.9', '>=');
			} else {
				$_cache = version_compare($client_version, '5.5.3', '>=');
			}

		}

		return $_cache;
	}

	/**
	 * Convert utf8mb4 to utf8
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function convertUtf8mb4QueryToUtf8($query)
	{
		if ($this->hasUTF8mb4Support())
		{
			return $query;
		}

		// If it's not an ALTER TABLE or CREATE TABLE command there's nothing to convert
		$beginningOfQuery = substr($query, 0, 12);
		$beginningOfQuery = strtoupper($beginningOfQuery);

		if (!in_array($beginningOfQuery, array('ALTER TABLE ', 'CREATE TABLE')))
		{
			return $query;
		}

		// Replace utf8mb4 with utf8
		return str_replace('utf8mb4', 'utf8', $query);
	}

	/**
	 * method to extract zip file in installation part
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function ebExtract($destination, $extracted)
	{
		if (JVERSION < 4.0) {
			$state = JArchive::extract($destination, $extracted);

		} else {
			$archive = new Joomla\Archive\Archive();
			$state = $archive->extract($destination, $extracted);
		}

		return $state;
	}

	/**
	 * Splits a string of multiple queries into an array of individual queries.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function splitSql($contents)
	{
		if (JVERSION < 4.0) {
			$queries = JInstallerHelper::splitSql($contents);

		} else {
			$queries = JDatabaseDriver::splitSql($contents);
		}

		return $queries;
	}

	/**
	 * method to execute query
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function ebQuery($db)
	{
		if (JVERSION < 4.0) {
			return $db->query();
		} else {
			return $db->execute();
		}
	}
}

