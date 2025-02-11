<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelDashboard extends EasyBlogAdminModel
{
	public $pagination = null;
	public $total = null;

	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.dashboard.limit', 'limit', $this->app->getCfg('list_limit') , 'int');

		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the pagination
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPagination()
	{
		$pagination = EB::pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));

		return $pagination;
	}

	/**
	 * Retrieves a list of blog post created by the user
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getEntries($authorId = null, $params = array())
	{
		if ($authorId) {
			// Get the author's id
			$authorId = JFactory::getUser($authorId)->id;
		}

		$category = isset($params['category']) && $params['category'] ? $params['category'] : false;
		$state = isset($params['state']) && $params['state'] !== 'all' ? $params['state'] : 'all';
		$search = isset($params['search']) && $params['search'] ? $params['search'] : '';
		$limit = isset($params['limit']) && $params['limit'] ? $params['limit'] : 0;
		$limit	= ($limit == 0) ? $this->getState('limit') : $limit;

		$sort = isset($params['sort']) ? $params['sort'] : 'created';
		$ordering = isset($params['ordering']) ? $params['ordering'] : 'desc';

		// we need to reset the limit state.
		if ($limit) {
			$this->setState('limit', $limit);
		}

		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');
		$limitSQL = 'LIMIT ' . $limitstart . ',' . $limit;

		$db = EB::db();

		$where = array();

		// EASYBLOG_DASHBOARD_TRASHED = -1
		$trashFilter = $state == EASYBLOG_DASHBOARD_TRASHED;

		// We need to do this since state.trash and published.published ihas the same value
		if ($trashFilter) {
			$where[] = 'WHERE a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_TRASHED);
		} else {
			$where[] = 'WHERE a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

			if ($state !== 'all') {
				$where[] = 'AND a.' . $db->quoteName('published') . '=' . $db->Quote((int) $state);
			} else {
				// if state equals to all, we will exclude pending post and draft post.
				$excludeState = array(EASYBLOG_POST_PENDING, EASYBLOG_POST_DRAFT);
				$where[] = 'AND a.' . $db->quoteName('published') . ' NOT IN (' . implode(',', $excludeState) . ')';
			}
		}

		if ($authorId) {
			$where[] = 'AND (';
			$where[] = ' (a.' . $db->quoteName('created_by') . '=' . $db->Quote($authorId) . ')';
			$where[] = ' OR ';
			$where[] = ' (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);
			$where[] = ' 		AND 1 <= (select count(1) FROM `#__easyblog_team_users` as tu WHERE tu.`team_id` = a.`source_id` AND tu.`user_id` = ' . $db->Quote($authorId) . ' AND tu.`isadmin` = 1))';
			$where[] = ')';
		}

		if ($category) {
			$where[] = 'AND b.' . $db->quoteName('category_id') . '=' . $db->Quote($params['category']);
		}

		if ($search) {
			$where[] = 'AND(';
			$where[] = 'a.' . $db->quoteName('title') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$where[] = 'OR';
			$where[] = 'a.' . $db->quoteName('intro') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$where[] = 'OR';
			$where[] = 'a.' . $db->quoteName('content') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$where[] = ')';
		}

		$where = implode(' ', $where);

		$query = array();
		$query[] = 'SELECT DISTINCT a.* FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';

		// Join with the category
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');

		//where
		$query[] = $where;

		// Apply ordering
		$query[] = 'ORDER BY ' . $db->quoteName($sort) . ' ' . $ordering;

		// Apply limit
		$query[] = $limitSQL;

		$query = implode(' ', $query);

		// echo $query;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Run query for pagination
		$query = array();

		$query[] = 'SELECT count(DISTINCT a.`id`) FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');

		//where
		$query[] = $where;

		$query = implode(' ', $query);
		$db->setQuery($query);

		$this->total = $db->loadResult();

		// if (empty($this->_pagination)) {
		// 	jimport('joomla.html.pagination');
		// 	$this->pagination	= EB::pagination( $this->total , $limitstart , $limit );

		// 	// var_dump($this->pagination);
		// }

		return $result;
	}
}
