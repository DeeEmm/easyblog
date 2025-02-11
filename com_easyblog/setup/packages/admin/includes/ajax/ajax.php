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

use Foundry\Libraries\Ajax;

class EasyBlogAjax extends Ajax
{
	/**
	 * Override the addCommand behavior of the parent for now as we need to capture for exceptions
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function addCommand($type, &$data)
	{
		foreach ($data as &$arg) {
			if ($arg instanceof EasyBlogException) {
				$arg = $arg->toArray();
			}
		}

		return parent::addCommand($type, $data);
	}

	/**
	 * Determines if the current namespace is a valid namespace
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isValidNamespace($namespace)
	{
		static $valid = null;

		if (is_null($valid)) {
			$valid = false;

			// Legacy uses '.' as separator, we need to replace occurences of '.' with /
			$namespace = str_ireplace('.', '/', $namespace);
			$namespace = explode('/', $namespace);

			// All calls should be made a minimum out of 3 parts of dots (.)
			if (count($namespace) >= 4) {
				$valid = true;
			}
		}

		return $valid;
	}

	/**
	 * Process the namespace and execute accordingly
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function execute($namespace)
	{
		// Process namespace string.
		// Legacy uses '.' as separator, we need to replace occurences of '.' with /
		$namespace = str_ireplace('.', '/', $namespace);
		$namespace = explode('/', $namespace);

		/**
		 * Namespaces are broken into the following
		 *
		 * site/views/viewname/methodname - Front end ajax calls
		 * admin/views/viewname/methodname - Back end ajax calls
		 */
		list($location, $type, $name, $method) = $namespace;

		// EasyBlog only supports view and controllers
		if (!in_array($type, ['views', 'controllers'])) {
			$this->fail(JText::_('Ajax calls are currently only serving views and controllers.'));
			return $this->send();
		}

		// Get the location
		$location = strtolower($location);
		$name = strtolower($name);

		$path = JPATH_ROOT;

		if ($location === 'admin') {
			$path .= '/administrator';
		}

		$path .= '/components/com_easyblog';

		if ($type === 'views') {

			// Include the base view so we don't have to include them manually in each views
			if ($location === 'site') {
				require_once($path . '/views/views.php');
			}

			$path .= '/' . $type . '/' . $name . '/view.ajax.php';
		}

		if ($type === 'controllers') {
			$path .= '/' . $type . '/' . $name . '.php';
		}

		$classType = $type == 'views' ? 'View' : 'Controller';
		$class = 'EasyBlog' . $classType . preg_replace('/[^A-Z0-9_]/i', '', $name);

		if (!class_exists($class)) {
			$exists = file_exists($path);

			if (!$exists) {
				$this->fail('Ajax file does not exist on the site');
				return $this->send();
			}

			require_once($path);
		}

		$obj = new $class();

		if (!method_exists($obj, $method)) {
			$this->fail(JText::sprintf('The method %1s does not exists.', $method));
			return $this->send();
		}

		// Call the method
		$obj->$method();
	}
}
