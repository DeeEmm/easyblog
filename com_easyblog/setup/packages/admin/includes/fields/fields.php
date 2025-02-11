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

class EasyBlogFields extends EasyBlog
{
	static $fields = [];

	public function __construct()
	{
		parent::__construct();

		// Initialize items
		$this->initialize();
	}

	/**
	 * Retrieves a list of fields
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function initialize()
	{
		if (empty(self::$fields)) {
			$files 	= JFolder::files(dirname(__FILE__) . '/types', '.', false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'abstract.php', 'index.html'));

			// Include base library
			require_once(__DIR__ . '/types/abstract.php');

			foreach ($files as $file) {

				require_once($file);

				$name = str_ireplace('.php', '', basename($file));
				$class = 'EasyBlogFieldsType' . ucfirst($name);

				$obj = new $class();

				self::$fields[$name] = $obj;
			}
		}

		return self::$fields;
	}

	/**
	 * Retrieve the field
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function get($type)
	{
		return self::$fields[$type];
	}

	/**
	 * Retrieves a list of fields
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getItems()
	{
		return self::$fields;
	}

	/**
	 * Trigger onbeforesave method for each fields
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function onBeforeSave(&$data, $type)
	{
		$field = $this->get($type);

		if (method_exists($field, 'onBeforeSave')) {
			return $field->onBeforeSave($data);
		}
	}
}
