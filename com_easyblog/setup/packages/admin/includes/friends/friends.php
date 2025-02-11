<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogFriends extends EasyBlog
{
	/**
	 * Determines if the "Add As Friend" should appear on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasIntegrations(EasyBlogTableProfile $author)
	{
		// Do not display the link if the user is trying to view his own post
		if ($author->id == $this->my->id) {
			return;
		}

		// If configured to integrate with jomsocial and jomsocial exists on the site
		if ($this->config->get('main_jomsocial_friends') && EB::jomsocial()->exists()) {
			return true;
		}

		// If configured to integrate with easysocial and exists on the site
		if ($this->config->get('integrations_easysocial_friends') && EB::easysocial()->exists()) {
			return true;
		}

		return false;
	}

	/**
	 * Displays the friend link for the author
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function html(EasyBlogTableProfile $author)
	{
		// Do not display the link if the user is trying to view his own post
		if ($author->id == $this->my->id) {
			return;
		}

		if ($this->config->get('integrations_easysocial_friends')) {
			return EB::easysocial()->getFriendsHtml($author->id);
		}

		if ($this->config->get('main_jomsocial_friends')) {
			return EB::jomsocial()->getFriendsHtml($author->id);
		}

		return;
	}
}
