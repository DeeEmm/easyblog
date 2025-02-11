<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelFavourites extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.favouries.limit', 'limit', $this->app->getCfg('list_limit'), 'int');

		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Determine if the post is already been favourited
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function isFavourited($postId, $userId)
	{
		$db = EB::db();

		$query = 'SELECT `id` FROM ' . $db->nameQuote('#__easyblog_favourites');
		$query .= ' WHERE ' . $db->nameQuote('postId') . ' = ' . $db->Quote($postId);
		$query .= ' AND ' . $db->nameQuote('userId') . ' = ' . $db->Quote($userId);

		$db->setQuery($query);

		$exists = $db->loadResult();

		return $exists;
	}

	/**
	 * Remove post from user's favourites list
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function removeFavourites($postId, $userId, $type = 'post')
	{
		$db = EB::db();

		$query = array();

		$query[] = 'DELETE FROM ' . $db->nameQuote('#__easyblog_favourites');
		$query[] = 'WHERE ' . $db->nameQuote('postId') . ' = ' . $db->Quote($postId);
		$query[] = 'AND ' . $db->nameQuote('userId') . ' = ' . $db->Quote($userId);

		if ($type) {
			$query[] = 'AND ' . $db->nameQuote('type') . ' = ' . $db->Quote($type);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Retrieves the pagination
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getPagination()
	{
		$pagination = EB::pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));

		return $pagination;
	}

	/**
	 * Retrieve lists of user's favourites post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getFavouritesPost($options = array())
	{
		$db = EB::db();

		// Normalize options
		$userId = isset($options['userId']) ? $options['userId'] : $this->my->id;
		$limit = isset($options['limit']) && $options['limit'] ? $options['limit'] : 0;
		$limit = ($limit == 0) ? $this->getState('limit') : $limit;
		$search = isset($options['search']) && $options['search'] ? $options['search'] : '';

		// we need to reset the limit state.
		if ($limit) {
			$this->setState('limit', $limit);
		}

		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');
		$limitSQL = 'LIMIT ' . $limitstart . ',' . $limit;

		$mainQuery = array();
		$query = array();

		$querySelector = 'SELECT DISTINCT a.`id`';

		$mainQuery[] = 'FROM ' . $db->nameQuote('#__easyblog_post') . ' AS a';
		$mainQuery[] = 'INNER JOIN ' . $db->nameQuote('#__easyblog_favourites') . ' AS b';
		$mainQuery[] = 'ON ' . $db->nameQuote('a.id') . ' = ' . $db->nameQuote('b.postId');
		$mainQuery[] = 'WHERE ' . $db->nameQuote('b.userId') . ' = ' . $db->Quote($userId);

		if ($search) {
			$mainQuery[] = 'AND(';
			$mainQuery[] = 'a.' . $db->quoteName('title') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$mainQuery[] = 'OR';
			$mainQuery[] = 'a.' . $db->quoteName('intro') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$mainQuery[] = 'OR';
			$mainQuery[] = 'a.' . $db->quoteName('content') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$mainQuery[] = ')';
		}

		$mainQuery = implode(' ', $mainQuery);

		// Apply limit
		$query[] = $querySelector;
		$query[] = $mainQuery;
		$query[] = $limitSQL;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		// Run query for pagination
		$querySelector = 'SELECT count(1)';
		$query = array();

		$query[] = $querySelector;
		$query[] = $mainQuery;

		$query = implode(' ', $query);
		$db->setQuery($query);

		$this->total = $db->loadResult();

		$posts = array();

		if ($results) {
			foreach ($results as $result) {
				$post = EB::post($result->id);
				$posts[] = $post;
			}
		}

		return $posts;
	}
}