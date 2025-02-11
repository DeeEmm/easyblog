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

class EasyBlogProfile extends EasyBlog
{
	public $profile;
	
	public function __construct($options = array())
	{
		parent::__construct();

		$this->profile = $options[0];
	}

	/**
	 * Retrieves the user's profile link
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getLink()
	{
		$options = $this->config->get('layout_avatar_link_name');
		$source = $this->config->get('layout_avatarIntegration');

		// If Modify author's name and avatar links is set to no,
		// always make source as 'default'
		if (!$options) {
			$source = 'default';
		}

		$adapter = $this->getAdapter($source);

		return $adapter->getLink();
	}

	/**
	 * Loads the adapter for a profile type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAdapter($adapter)
	{
		$namespace = strtolower($adapter);
		$className = 'EasyBlogProfile' . ucfirst($adapter);

		$file = __DIR__ . '/adapters/' . $namespace . '/client.php';

		require_once($file);

		$obj = new $className($this->profile);

		return $obj;
	}
}
