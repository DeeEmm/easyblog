<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.filesystem.file' );
jimport('joomla.filesystem.folder' );
jimport('joomla.html.parameter' );
jimport('joomla.application.component.model');
jimport('joomla.access.access');


if (!function_exists('dump')) {

	function dump()
	{
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			var_dump($arg);
		}

		echo '</pre>';
		exit;
	}
}

class EasyBlog
{
	public $config = null;
	public $doc = null;
	public $app = null;
	public $input = null;
	public $my = null;
	public $string = null;
	public $lang = null;
	public $db = null;
	public $jconfig = null;

	public function __construct()
	{
		// EasyBlog's configuration
		$this->config = EB::config();

		if (!defined('EASYBLOG_COMPONENT_CLI')) {
			$this->jconfig = EB::jconfig();
			$this->doc = JFactory::getDocument();
			$this->app = JFactory::getApplication();
			$this->input = EB::request();
			$this->my = JFactory::getUser();
			$this->string = EB::string();
			$this->lang = JFactory::getLanguage();
			$this->db = EB::db();
		}
	}

	/**
	 * Helper method to load language
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function loadLanguage($admin = false)
	{
		if ($admin) {
			return $this->lang->load('com_easyblog', JPATH_ADMINISTRATOR);
		}

		return $this->lang->load('com_easyblog', JPATH_ROOT);
	}

	public function hasError()
	{
		return !empty($this->errors);
	}

	public function hasErrors()
	{
		return !empty($this->errors);
	}

	public function setError($msg)
	{
		$this->errors[] = $msg;

		return $this;
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function isMobile()
	{
		$responsive = null;

		if (is_null($responsive)) {
			$responsive = EB::responsive()->isMobile();
		}

		return $responsive;
	}

	/**
	 * Determines if this is a tablet layout
	 *
	 * @since   5.3
	 * @access  public
	 */
	public function isTablet()
	{
		$responsive = null;

		if (is_null($responsive)) {
			$responsive = EB::responsive()->isTablet();
		}

		return $responsive;
	}

	public function getError()
	{
		if (!$this->hasErrors()) {
			return null;
		}

		// Return the last possible error
		return $this->errors[count($this->errors) - 1];
	}

	public function getErrors()
	{
		return $this->errors;
	}
}

class EasyBlogDbJoomla
{
	public $db = null;

	public function __construct()
	{
		$this->db = JFactory::getDBO();
	}

	/**
	 * Override parent's query method
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function query()
	{
		return $this->db->execute();
	}

	/**
	 * Override parent's setquery method
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function setQuery($query, $offset = 0, $limit = 0)
	{
		if (is_array($query)) {
			$query = implode(' ', $query);
		}

		return $this->db->setQuery($query, $offset, $limit);
	}

	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}

	public function getEscaped($str, $extra = false)
	{
		return $this->db->escape($str, $extra);
	}

	public function nameQuote($str)
	{
		return $this->db->quoteName($str);
	}

	/**
	 * Override the quote to check if array is passed in, then quote all the items accordingly.
	 * This is actually already supported from J3.3 but for older versions, we need this compatibility layer
	 */
	public function quote($item, $escape = true)
	{
		if (!is_array($item)) {
			return $this->db->quote($item, $escape);
		}

		$result = array();

		foreach ($item as $i) {
			$result[] = $this->db->quote($i, $escape);
		}

		return $result;
	}

	/**
	 * Override the quoteName to check if array is passed in, then quoteName all the items accordingly.
	 * This is actually already supported from J3.3 but for older versions, we need this compatibility layer
	 */
	public function quoteName($name, $as = null)
	{
		if (!is_array($name)) {
			return $this->db->quoteName($name, $as);
		}

		$result = array();

		foreach ($name as $i) {
			$result[] = $this->db->quoteName($i, $as);
		}

		return $result;
	}

	/**
	 * Retrieve table columns
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function getTableColumns($tableName)
	{
		$db = JFactory::getDBO();

		$query  = 'SHOW FIELDS FROM ' . $db->quoteName($tableName);

		$db->setQuery($query);

		$rows = $db->loadObjectList();
		$fields = array();

		foreach ($rows as $row) {
			$fields[] = $row->Field;
		}

		return $fields;
	}

	/**
	 * Retrieves table indexes from a specific table.
	 *
	 * @since   5.0
	 * @access  public
	 */
	public static function getTableIndexes($tableName)
	{
		$db = JFactory::getDBO();

		$query = 'SHOW INDEX FROM ' . $db->quoteName($tableName);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		$indexes = array();

		foreach ($result as $row) {
			$indexes[] = $row->Key_name;
		}

		return $indexes;
	}


	public function __call($method, $args)
	{
		$refArray = array();

		if ($args) {

			foreach($args as &$arg) {
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array(array($this->db, $method), $refArray);
	}

	/**
	 * Alias for quote.
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function q($item, $escape = true)
	{
		return $this->quote($item, $escape);
	}

	/**
	 * Alias for quotename.
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function qn($name, $as = null)
	{
		return $this->quoteName($name, $as);
	}

	/**
	 * Synchronizes database versions
	 *
	 * @since   5.0
	 * @access  public
	 */
	public static function sync($from = '')
	{
		$db = EB::db();

		// List down files within the updates folder
		$path = EBLOG_ADMIN_ROOT . '/updates';

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$scripts = [];

		if ($from) {
			$folders = JFolder::folders($path);

			if ($folders) {
				foreach ($folders as $folder) {

					// Because versions always increments, we don't need to worry about smaller than (<) versions.
					// As long as the folder is greater than the installed version, we run updates on the folder.
					// We cannot do $folder > $from because '1.2.8' > '1.2.15' is TRUE
					// We want > $from, NOT >= $from

					if (version_compare($folder, $from) === 1) {
						$fullPath = $path . '/' . $folder;

						// Get a list of sql files to execute
						$files = JFolder::files( $fullPath , '.json$' , false , true );

						foreach ($files as $file) {
							$data = json_decode(file_get_contents($file));
							$scripts = array_merge($scripts, (array) $data);
						}
					}
				}
			}
		} else {

			$files = JFolder::files($path, '.json$', true, true);

			// If there is nothing to process, skip this
			if (!$files) {
				return false;
			}

			foreach ($files as $file) {
				$data = json_decode(file_get_contents($file));
				$scripts = array_merge($scripts, $data);
			}
		}

		if (!$scripts) {
			return false;
		}

		$tables = [];
		$indexes = [];
		$affected = 0;

		foreach ($scripts as $script) {
			$columnExist = true;
			$indexExist = true;
			$alterTable = false;

			if (isset($script->column)) {
				// Store the list of tables that needs to be queried
				if (!isset($tables[$script->table])) {
					$tables[$script->table] = $db->getTableColumns($script->table);
				}

				// Check if the column is in the fields or not
				$columnExist = in_array($script->column, $tables[$script->table]);
			}

			if (isset($script->alter)) {
				$alterTable = true;
			}

			if (isset($script->index)) {
				// Get the list of indexes on a table
				if (!isset($indexes[$script->table])) {
					$indexes[$script->table] = $db->getTableIndexes($script->table);
				}

				$indexExist = in_array($script->index, $indexes[$script->table]);
			}

			if ($alterTable || !$columnExist || !$indexExist) {
				$db->setQuery($script->query);
				$db->Query();

				$affected += 1;
			}
		}

		return $affected;
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

			if (method_exists($this->db, 'hasUTF8mb4Support')) {
				$_cache = $this->db->hasUTF8mb4Support();
				return $_cache;
			}

			// we need to check server version 1st.
			$server_version = $this->db->getVersion();
			if (version_compare($server_version, '5.5.3', '<')) {
				 $_cache = false;
				 return $_cache;
			}

			// now we check for client version
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
	 * Proxy for getErrorNum method for Joomla4 compatibility
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getErrorNum()
	{
		if (method_exists($this->db, 'getErrorNum')) {
			return (int) $this->db->getErrorNum();
		}

		if (method_exists($this->db, 'getErrorNumber')) {
			return (int) $this->db->getErrorNumber();
		}

		if (method_exists($this->db->getConnection(), 'errorCode')) {
			return (int) $this->db->getConnection()->errorCode();
		}

		return (int) $this->db->getConnection()->errno;
	}

	/**
	 * Proxy for getErrorMsg method for Joomla4 compatibility
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getErrorMsg($escaped = false)
	{
		if (method_exists($this->db, 'getErrorMsg')) {
			return $this->db->getErrorMsg($escaped);
		}

		// TODO:: to support joomla 4.0 error num.
		return $this->db->getConnection()->error;
	}

	/**
	 * Proxy for stderr method for Joomla4 compatibility
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function stderr($showSQL = false)
	{
		if (method_exists($this->db, 'stderr')) {
			return $this->db->stderr($showSQL);
		}

		return '';
	}


}
