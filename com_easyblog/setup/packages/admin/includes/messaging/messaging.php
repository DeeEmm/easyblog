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

class EasyBlogMessaging extends EasyBlog
{
	/**
	 * Determines if messaging should be enabled
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function hasMessaging($authorId = null)
	{
		$author = JFactory::getUser($authorId);

		// We do not want the author to message themselves.
		if ($this->my->id == $author->id) {
			return false;
		}

		// Determines if jomsocial integrations is enabled
		if ($this->config->get('main_jomsocial_messaging') && EB::jomsocial()->exists()) {
			return true;
		}

		// Determines if EasySocial integrations is enabled
		if ($this->config->get('integrations_easysocial_conversations') && EB::easysocial()->exists()) {
			return true;
		}

		return false;
	}

	/**
	 * Displays the html code for sending a message to a friend
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function html(EasyBlogTableProfile $author)
	{
		// Why would they want to send a message to themselves.
		if ($this->my->id == $author->id) {
			return;
		}

		// Ensure that JomSocial really exists
		if ($this->config->get('main_jomsocial_messaging') && EB::jomsocial()->exists()) {
			return EB::jomsocial()->getMessagingHtml($author->id);
		}

		// Ensure that JomSocial really exists
		if ($this->config->get('integrations_easysocial_conversations') && EB::easysocial()->exists()) {
			return EB::easysocial()->getMessagingHtml($author->id);
		}
	}
}
