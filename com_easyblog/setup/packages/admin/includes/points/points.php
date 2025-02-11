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

class EasyBlogPoints
{
	/**
	 * Determines if points should be displayed beneath the author box.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasIntegrations()
	{
		$config = EB::config();

		if ($config->get('integrations_easysocial_points') && EB::easysocial()->exists()) {
			return true;
		}

		return false;
	}

	/**
	 * Renders the html output for achievements
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function html(EasyBlogTableProfile $profile)
	{
		// Ensure that EasySocial is initialized
		EB::easysocial()->init();

		$user = FD::user($profile->id);
		$points = $user->getPoints();

		$theme = EB::themes();
		$theme->set('points', $points);
		$theme->set('user', $user);
		$html = $theme->output('site/integrations/easysocial/points');

		return $html;
	}
}
