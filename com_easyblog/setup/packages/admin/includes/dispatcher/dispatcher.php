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

class EasyBlogDispatcher extends EasyBlog
{
	public $dispatcher = null;
	/**
	 * Dispatcher initialization as per joomla version
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		if (FH::isJoomla4()) {
			$this->dispatcher = EBFactory::getApplication();
		}

		if (FH::isJoomla31()) {
			$this->dispatcher = JDispatcher::getInstance();
		}
	}

	public function trigger($eventName, $args = [], $group = null)
	{
		if ($group) {
			JPluginHelper::importPlugin($group);
		}
		if (FH::isJoomla4()) {
			return $this->dispatcher->triggerEvent($eventName, $args);
		}

		if (FH::isJoomla31()) {
			return $this->dispatcher->trigger($eventName, $args);
		}
	}
}
