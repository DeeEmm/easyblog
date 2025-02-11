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

jimport('joomla.filesystem.file');

class EasyBlogJFBConnect extends EasyBlog
{
	/**
	 * Ensures the JFBConnect is installed on the site
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public function exists()
	{
		$factory = JPATH_ROOT . '/components/com_jfbconnect/libraries/factory.php';
		$exists = JFile::exists($factory);

		if (!$exists) {
			return false;
		}

		require_once($factory);
		return true;
	}

	/**
	 * Renders the JFBConnect plugin tag {JFBConnect}
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public function getTag()
	{
		if (!$this->exists()) {
			return;
		}

		$tag = '{JFBCLogin}';
		$key = JFBCFactory::config()->get('social_tag_admin_key');

		if ($key) {
			$tag = '{JFBCLogin key=' . $key . '}';
		}

		return $tag;
	}

	public function getButtons()
	{
		if (!$this->exists()) {
			return;
		}

		$providers = JFBCFactory::getAllProviders();
		$params = array('buttonSize' => 'icon_text_button');
		$buttons = '';

		foreach ($providers as $provider) {
			$buttons .= $provider->loginButton($params);
		}

		return $buttons;
	}
}
