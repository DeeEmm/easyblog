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

require_once(__DIR__ . '/model.php');

class EasyBlogModelMediaManager extends EasyBlogAdminModel
{
	private $data = null;
	private $pagination = null;
	private $total = null;

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
	 * Retrieves a list of objects from the database given the uri
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getObjects($uris = array())
	{
		$db = EB::db();

		if (is_array($uris)) {
			foreach ($uris as &$uri) {
				$uri = $db->Quote($uri);
			}
		}

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_media');

		if (is_array($uris)) {
			$query[] = 'WHERE ' . $db->qn('uri') . ' IN(';
			$query[] = implode(',', $uris);
			$query[] = ')';
		} else {
			$query[] = 'WHERE ' . $db->qn('uri') . ' = ' . $db->Quote($uris);
		}

		$query = implode(' ', $query);
		$db->setQuery($query);
		
		$result = $db->loadObjectList('uri');

		if (!$result) {
			return;
		}
		
		return $result;
	}

	/**
	 * Retrieves a list of objects from the database given the uri
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPlaceObjects($placeId)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_media');
		$query[] = 'WHERE ' . $db->qn('place') . ' = ' . $db->Quote($placeId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		
		$result = $db->loadObjectList();

		if (!$result) {
			return;
		}
		
		return $result;
	}


	/**
	 * Retrieves a list of articles on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPosts($userId = null)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('published') . '!=' . $db->Quote(EASYBLOG_POST_BLANK);
		$query[] = 'and ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		// If user is a site admin, we want to show everything
		if (!FH::isSiteAdmin()) {
			$user = JFactory::getUser($userId);
			$query[] = 'AND ' . $db->quoteName('created_by') . '=' . $db->Quote($user->id);
		}

		$query[] = 'ORDER BY `id` DESC';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}
}
