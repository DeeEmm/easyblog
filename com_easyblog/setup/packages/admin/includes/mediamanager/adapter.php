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

class EBMMAdapter extends EasyBlog
{
	/**
	 * Stores the adapter object
	 * @var mixed
	 */
	private $source = null;

	public function __construct($source, $lib)
	{
		parent::__construct();

		$this->lib = $lib;
		$this->source = $this->getSource($source);
	}

	/**
	 * Maps unknown function calls back to the adapter
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->source, $method), $arguments);
	}

	/**
	 * Retrieves the adapter object given the source type
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getSource($source)
	{
		static $sources = array();

		if (isset($sources[$source])) {
			return $sources[$source];
		}

		// Load adapter
		$path = __DIR__ . '/adapters/' . strtolower($source) . '.php';

		require_once($path);

		$class = 'EasyBlogMediaManager' . ucfirst($source) . 'Source';

		$instance = new $class($this->lib);

		$sources[$source] = $instance;

		return $instance;
	}
}
