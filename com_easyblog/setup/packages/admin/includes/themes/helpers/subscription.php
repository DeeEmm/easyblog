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

class EasyBlogThemesHelperSubscription
{
	/**
	 * Renders a CTA form to request user to subscribe
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function form($user, $type = EBLOG_SUBSCRIPTION_SITE, $uid = null)
	{
		$config = EB::config();
		$acl = EB::acl();

		if (!$config->get('main_sitesubscription') || !$acl->get('allow_subscription')) {
			return false;
		}

		static $cache = [];
		$index = $user->id . $type . $uid;

		if (!isset($cache[$index])) {
			// PHP 8.1 compatibility
			$user->name = is_null($user->name) ? '' : $user->name;
			$user->email = is_null($user->email) ? '' : $user->email;

			$theme = EB::themes();
			$theme->set('uid', $uid);
			$theme->set('type', $type);
			$theme->set('user', $user);

			$cache[$index] = $theme->output('site/helpers/subscription/form');
		}

		return $cache[$index];
	}
}