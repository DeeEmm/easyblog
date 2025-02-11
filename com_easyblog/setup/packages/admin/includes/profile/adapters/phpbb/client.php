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
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(dirname(__FILE__)) . '/default/client.php');

class EasyBlogProfilePhpbb extends EasyBlogProfileDefault
{
	public function __construct(EasyBlogTableProfile $profile)
	{
		parent::__construct($profile);

		$this->path = $this->config->get('layout_phpbb_path');
	}

	/**
	 * Determines if phpbb exists on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists()
	{
		$file = JPATH_ROOT . '/' . $this->path . '/config.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require($file);

		// Split the path to get the exact driver
		$dbmsArray = explode("\\",$dbms);

		// Alwasy get the last array
		$driver = end($dbmsArray);

		$options = array('driver' => $driver, 'host' => $dbhost, 'user' => $dbuser, 'password' => $dbpasswd, 'database' => $dbname, 'prefix' => $table_prefix);
		$this->db = JDatabaseDriver::getInstance($options);

		return true;
	}

	/**
	 * Retrieves the profile link
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getLink()
	{
		$default = parent::getLink();

		// Check if phpbb exists on the site
		if (!$this->exists()) {
			return $default;
		}

		$query = array();
		$query[] = 'SELECT ' . $this->db->qn('user_id');
		$query[] = 'FROM ' . $this->db->qn('#__users');
		$query[] = 'WHERE LOWER(' . $this->db->qn('username') . ') = LOWER(' . $this->db->Quote($this->profile->user->username) . ')';

		$query = implode(' ', $query);

		$this->db->setQuery($query, 0, 1);

		$result = $this->db->loadResult();

		if (!$result) {
			return $default;
		}

		$link = JURI::root() . rtrim($this->path) . '/memberlist.php?mode=viewprofile&u=' . $result;

		return $link;
	}
}
