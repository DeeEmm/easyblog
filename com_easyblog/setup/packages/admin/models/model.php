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

jimport('joomla.application.component.model');

if (class_exists('JModelAdmin')) {
	class EasyBlogAdminMainModel extends JModelAdmin
	{
		public function getForm($data = array(), $loadData = true)
		{
		}
	}
} else {

	class EasyBlogAdminMainModel extends JModel
	{
	}
}


class EasyBlogAdminModel extends EasyBlogAdminMainModel
{
	// Implemented by child
	public $searchables = array();

	public function __construct()
	{
		parent::__construct();

		// override parent _db to use our own db layer.
		$this->_db = EB::db();

		$this->app = JFactory::getApplication();
		$this->input = EB::request();
		$this->config = EB::config();
		$this->my = JFactory::getUser();
		$this->jConfig = EB::jConfig();
	}

	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState()
	{
		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
	{
		$mainframe = EBFactory::getApplication();

		if (method_exists($mainframe, 'getUserStateFromRequest')) {
			return $mainframe->getUserStateFromRequest($key, $request, $default, $type);
		}

		if (method_exists($mainframe, 'getUserState')) {

			$cur_state = $mainframe->getUserState($key, $default);
			$new_state = $mainframe->input->get($request, null, $type);

			// Save the new value only if it was set in this request.
			if ($new_state !== null) {
				$mainframe->setUserState($key, $new_state);
			} else {
				$new_state = $cur_state;
			}

			return $new_state;
		}

		return $default;
	}


	protected function implodeValues($data)
	{
		$db  = EB::db();
		$str = '';

		foreach ($data as $value) {
			$str .= $db->Quote($value);

			if (next($data) !== false) {
				$str .= ',';
			}
		}

		return $str;
	}

	/**
	 * Get searchable columns
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getSearchableItems($query)
	{
		$query = explode(':', $query);

		if (count($query) == 1 || !$this->searchables) {
			return false;
		}

		$column = $query[0];

		if (!in_array(strtoupper($column), $this->searchables) && !in_array(strtolower($column), $this->searchables)) {
			return false;
		}

		$data = new stdClass();
		$data->column = $column;
		$data->query = $query[1];

		return $data;
	}

	protected function bindTable($tableName, $result)
	{
		$binded = array();

		foreach ($result as $row) {
			$table = EB::table($tableName);
			$table->bind($row);
			$binded[] = $table;
		}

		return $binded;
	}
}
