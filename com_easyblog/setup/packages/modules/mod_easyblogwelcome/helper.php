<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class modEasyBlogWelcomeHelper extends EasyBlog
{
	/**
	 * Retrieves the return url
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getReturnURL($params)
	{
		$my = JFactory::getUser();

		$type = $my->guest ? 'login' : 'logout';

		// Get the menu id to redirect to
		$itemid = $params->get($type);

		// Default to stay on the same page.
		$return = JUri::getInstance()->toString();

		// Check for menu item redirection
		if ($itemid) {
			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getItem($itemid);

			// If there's a menu item
			if ($item) {
				$return = $item->link . '&Itemid=' . $itemid;
			}
		}

		return base64_encode($return);
	}

	/**
	 * Determines if Joomla has two factor enabled
	 *
	 * @since	5.4.2
	 * @access	public
	 */
	public function hasTwoFactor()
	{
		static $cache = null;

		if (is_null($cache)) {
			$twoFactorMethods = JAuthenticationHelper::getTwoFactorMethods();
			$hasTwoFactor = count($twoFactorMethods) > 1;

			$cache = $hasTwoFactor;

		}

		return $cache;
	}

	public function getBloggerProfile($userid)
	{
		if(empty($userid)) {
			return false;
		}

		$blogger = EB::user($userid);

		$integrate	= new EasyBlogIntegrate();
		$profile	= $integrate->integrate($blogger);

		$profile->displayName   = $blogger->getName();

		return $profile;
	}
}
