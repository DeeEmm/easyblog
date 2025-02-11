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

use Foundry\Tables\Table;

class EasyBlogTable extends Table
{
	/**
	 * Tired of fixing conflicts with JTable::getInstance . We'll overload their method here.
	 *
	 * @param   string  $type    The type (name) of the JTable class to get an instance of.
	 * @param   string  $prefix  An optional prefix for the table class name.
	 * @param   array   $config  An optional array of configuration values for the JTable object.
	 *
	 * @return  mixed    A JTable object if found or boolean false if one could not be found.
	 *
	 * @link    http://docs.joomla.org/JTable/getInstance
	 * @since   11.1
	 */
	public static function getInstance($type, $prefix = 'JTable', $config = [])
	{
		// Sanitize and prepare the table class name.
		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$tableClass = 'EasyBlogTable' . ucfirst($type);

		if (strtolower($type) == 'user') {
			$type = 'Blogger';
		}

		// Only try to load the class if it doesn't already exist.
		if (!class_exists($tableClass)) {

			// Search for the class file in the JTable include paths.
			$path = dirname( __FILE__ ) . '/' . strtolower( $type ) . '.php';

			// Import the class file.
			include_once $path;
		}

		$table = parent::getInstance($type, 'EasyBlogTable', $config);

		return $table;
	}

	/**
	 * Retrieves the database object
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getDBO()
	{
		$db = EB::db();

		return $db;
	}

	/**
	 * Method to reset class properties to the defaults set in the class
	 * definition. It will ignore the primary key as well as any private class
	 * properties.
	 *
	 * @return  void
	 *
	 * @link    http://docs.joomla.org/JTable/reset
	 * @since   11.1
	 */
	public function reset()
	{
		$properties = get_object_vars($this);
		$columns = [];

		foreach ($properties as $key => $value) {
			if ($key != $this->_tbl_key && strpos($key, '_') !== 0) {
				$columns[] = $value;
			}
		}

		return $columns;
	}
}
