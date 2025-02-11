<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewFeeds extends EasyBlogAdminView
{
	/**
	 * Allows caller to import feeds
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function download()
	{
		// Get the id's from the request.
		$id = $this->input->get('id', 0, 'int');

		// Load the feed data
		$feed = EB::table('Feed');
		$feed->load($id);

		if (!$id || !$feed->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_FEEDS_INVALID_FEED_ID_PROVIDED'));
		}

		// Set this into processing mode first.
		$feed->flag = true;
		// $feed->store();

		// Import the feed
		$result = EB::feeds()->import($feed);

		// Set the last import date
		$feed->last_import = EB::date()->toSql();

		// Reset the flag
		$feed->flag = false;

		// Store the feed item now
		$feed->store();

		return $this->ajax->resolve($result);
	}
}
