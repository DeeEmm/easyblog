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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelAssets extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Delete assets that are related to the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteAssets($postId)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->qn('#__easyblog_post_assets') . ' WHERE ' . $db->qn('post_id') . '=' . $db->Quote($postId);

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Get the assets based on blog id
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostAssets($id)
	{
		static $_cache = array();

		if (! isset($_cache[$id])) {
			$db = EB::db();

			// Try to look for the permalink
			$query = array();
			$query[] = 'SELECT ' . $db->quoteName('key') . ',' . $db->quoteName('value') . ' FROM ' . $db->quoteName('#__easyblog_post_assets');
			$query[] = 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($id);

			// Join the query now
			$query = implode(' ', $query);

			$db->setQuery($query);
			$assets = $db->loadObjectList();

			$_cache[$id] = $assets;
		}


		return $_cache[$id];
	}
}
