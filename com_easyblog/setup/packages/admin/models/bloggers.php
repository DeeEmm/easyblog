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

class EasyBlogModelBloggers extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;
	public $data = null;
	public $searchables = array('id', 'email');

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.users.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves a list of Joomla user groups from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getJoomlaUserGroups($userId = '')
	{
		$db = EB::db();

		$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
		$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
		$query .= ' FROM #__usergroups AS a';
		$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';

		// condition
		$where  = array();

		if (!empty($cid)) {
			$where[] = ' a.`id` = ' . $db->quote($cid);
		}

		$where = count($where) ? ' WHERE ' .implode(' AND ', $where) : '';

		$query .= $where;

		// grouping and ordering
		$query .= ' GROUP BY a.id';
		$query .= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (!$this->total) {
			$query = $this->_buildQuery();
			$this->total = $this->_getListCount($query);
		}

		return $this->total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (!$this->pagination) {
			$this->pagination = EB::Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery($browsingMode = false, $exclusion = array(), $isCount = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($browsingMode, $exclusion);
		$orderby = $this->_buildQueryOrderBy($browsingMode);

		$db = EB::db();

		if ($browsingMode) {

			$query = array();
			$query[] = 'SELECT a.*, b.`content_id` AS `featured` FROM ' . $db->qn('#__users') . ' AS a ';
			$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_featured') . ' AS b ';
			$query[] = 'ON a.`id` = b.`content_id` AND b.`type`=' . $db->Quote('blogger');
			$query[] = $where;
			$query[] = $orderby;

			$query = implode(' ', $query);

		} else {

			$query  = "select count( p.id ) as `totalPost`, COUNT( DISTINCT(g.content_id) ) as `featured`,";
			$query .= " a.*, users.`ordering`";

			if ($isCount) {
				$query  = "select count(a.id)";
			}

			$query .= "	from `#__users` as a";
			$query .= "	inner join `#__user_usergroup_map` as up on a.`id` = up.`user_id`";
			$query .= "	inner join `#__easyblog_acl_group` as ag  on up.group_id = ag.content_id";
			$query .= "	inner join `#__easyblog_acl` as acl on ag.`acl_id` = acl.`id`";
			$query .= "	inner join `#__easyblog_users` as users on a.`id` = users.`id`";

			$query .= " 	left join `#__easyblog_post` as p on a.`id` = p.`created_by`";
			$query .= " 		and `p`.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query .= " 		and p.`state` = " . $db->Quote(EASYBLOG_POST_NORMAL);
			$query .= " 	left join `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= " . $db->Quote('blogger');

			$query .= "	where acl.`action` = " . $db->Quote('add_entry');
			$query .= "	and ag.type = " . $db->Quote('group');
			$query .= "	and ag.status = " . $db->Quote('1');
			$query .= $where;
			$query .= " group by a.`id`";
			$query .= $orderby;

		}

		return $query;
	}

	/**
	 * Builds the where clause
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function _buildQueryWhere($browsingMode = false, $exclusion = array())
	{
		$db = EB::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.users.filter_state', 'filter_state', '', 'word' );

		// Process search
		$search = $this->app->getUserStateFromRequest('com_easyblog.users.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$where = array();

		if ($filter_state == 'P') {
			$where[] = $db->qn( 'a.block' ) . '=' . $db->Quote( '0' );
		}

		if ($filter_state == 'U') {
			$where[] = $db->qn( 'a.block' ) . '=' . $db->Quote( '1' );
		}

		if ($search) {
			// If there is a : in the search query
			$value = $search;

			$customSearch = $this->getSearchableItems($search);

			if ($customSearch) {
				$column = 'a.' . strtolower($customSearch->column);
				$value = $customSearch->query;
				$where[] = $db->qn($column) . ' LIKE (' . $db->Quote('%' . $value . '%') . ')';
			} else {
				$where[] = '(' . $db->qn('a.name') . ' LIKE (' . $db->Quote('%' . $value . '%') . ') OR ' . $db->qn('a.username') . ' LIKE (' . $db->Quote('%' . $value . '%') . '))';
			}
		}

		if (!empty($exclusion)) {
			$exclusion = implode(',', $exclusion);
			$where[] = $db->qn('a.id') . ' NOT IN(' . $db->Quote($exclusion) . ')';
		}

		if ($where) {
			if ($browsingMode) {
				$where = (count($where)) ? ' where '.implode( ' AND ', $where ) : '';
			} else {
				$where = (count($where) > 1) ? ' AND '.implode( ' AND ', $where ) : ' AND ' . $where[0];
			}

			return $where;
		}

		return '';
	}

	/**
	 * Constructs the group by statement
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function _buildQueryGroupBy()
	{
		$db = EB::db();
		$query = 'GROUP BY a.' . $db->qn('id');

		return $query;
	}

	/**
	 * Constructs the order by clause
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function _buildQueryOrderBy($browsingMode = false)
	{
		$ordering = $this->app->getUserStateFromRequest('com_easyblog.users.filter_order', 'filter_order', 'a.id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.users.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		// Since there is no way to reset only specific user state from request
		// then we do the replacement here
		if ($browsingMode && $ordering == 'users.ordering') {
			$ordering = 'a.id';
		}

		$query = 'ORDER BY ' . $ordering . ' ' . $direction;

		return $query;
	}

	/**
	 * Retrieves a list of users
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getUsers($isBrowse = false, $exclusion = array())
	{
		// Lets load the content if it doesn't already exist
		if (!$this->data) {
			$db = EB::db();

			$query = $this->_buildQuery($isBrowse, $exclusion);
			$cntQuery = $this->_buildQuery($isBrowse, $exclusion, true);

			$this->total = $this->_getListCount($cntQuery);
			$this->pagination = EB::Pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));

			// apply limit
			$query .= " LIMIT " . $this->getState('limitstart') . ', ' . $this->getState('limit');

			$db->setQuery($query);
			$this->data = $db->loadObjectList();
		}

		return $this->data;
	}


	/**
	 * Method to publish or unpublish categories
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function publish(&$categories = array(), $state = 1)
	{
		if (!$categories) {
			return false;
		}

		if (!is_array($categories)) {
			$categories = array($categories);
		}

		$categories = implode(',', $categories);

		$db = EB::db();

		$query = array();
		$query[] = 'UPDATE ' . $db->qn('#__easyblog_category');
		$query[] = 'SET ' . $db->qn('published') . '=' . $db->Quote($state);
		$query[] = 'WHERE ' . $db->qn('id') . ' IN(' . $categories . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Method to update blogger ordering
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function orderingUpdate($userId, $orderId)
	{
		if (!$userId || !$orderId) {
			return false;
		}

		$db = EB::db();

		// retrieve the current user ordering which user want to update it
		$query = 'SELECT `ordering` FROM ' . $db->qn('#__easyblog_users');
		$query .= ' WHERE ' . $db->qn('id') . ' = ' . $db->Quote($userId);

		$db->setQuery($query);
		$currentNumber = $db->loadObject();

		// minus 1 for those more than the current ordering number 
		$query = 'UPDATE ' . $db->qn('#__easyblog_users');
		$query .= ' SET ' . $db->qn('ordering') . ' = ' . $db->qn('ordering') . ' - 1';
		$query .= ' WHERE ' . $db->qn('ordering') . ' > ' . $db->Quote($currentNumber->ordering);
		$query .= ' AND ' . $db->qn('ordering') . ' <= ' . $db->Quote($orderId);

		$db->setQuery($query);
		$db->query();

		// increase 1 for those less than current ordering number
		$query = 'UPDATE ' . $db->qn('#__easyblog_users');
		$query .= ' SET ' . $db->qn('ordering') . ' = ' . $db->qn('ordering') . ' + 1';
		$query .= ' WHERE ' . $db->qn('ordering') . ' < ' . $db->Quote($currentNumber->ordering);
		$query .= ' AND ' . $db->qn('ordering') . ' >= ' . $db->Quote($orderId);

		$db->setQuery($query);
		$db->query();

		// only update that ordering value as what user set
		$query = 'UPDATE ' . $db->qn('#__easyblog_users');
		$query .= ' SET ' . $db->qn('ordering') . ' = ' . $db->Quote($orderId);
		$query .= ' WHERE ' . $db->qn('id') . ' = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	 * Method to reset blogger ordering from lower blogger id to higher.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function resetBloggerOrdering()
	{
		$db = EB::db();

		$query = 'SELECT `id` FROM ' . $db->qn('#__easyblog_users');
		$query .= 'order by ' . $db->qn('id') . 'asc';
		$db->setQuery($query);
		$results = $db->loadObjectList();

		$counter = 1;

		foreach ($results as $row) {

			$query = 'UPDATE ' . $db->qn('#__easyblog_users');
			$query .= ' SET ' . $db->qn('ordering') . ' = ' . $db->Quote($counter);
			$query .= ' WHERE ' . $db->qn('id') . ' = ' . $db->Quote($row->id);

			$db->setQuery($query);
			$db->query();

			$counter++;
		}

		return true;		
	}

	/**
	 * Method to retrive those user who missing blogger meta data.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getMissingMetaBloggers()
	{
		$db = EB::db();

		$query = 'SELECT u.`id` FROM ' . $db->qn('#__users') . ' AS u';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT m.`id` FROM ' . $db->qn('#__easyblog_meta') . ' AS m';
		$query .= ' WHERE m.`content_id` = u.`id`';
		$query .= ' AND m.`type` = ' . $db->Quote('blogger');
		$query .= ')';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;		
	}

	/**
	 * Generates a list of blogger on the site
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function getBloggerCloud($limit = '', $order = 'postcount', $sort = 'asc', $usePagination = false)
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config = EB::config();

		$queryExclude = '';
		$excludeCats = array();

		$query = 'SELECT DISTINCT a.`id`, a.`nickname`,';

		$query .= ' COUNT(a.`id`) as `post_count`';
		$query .= ' FROM `#__easyblog_users` AS a';
		$query .= ' LEFT JOIN `#__easyblog_post` AS b';
		$query .= ' ON a.`id` = b.`created_by`';

		// exclude block user
		$query .= ' LEFT JOIN `#__users` AS c';
		$query .= ' ON b.`created_by` = c.`id`';
		$query .= ' WHERE (c.`block` = 0 OR c.`id` IS NULL)';

		$query .= ' GROUP BY (a.`id`)';

		// order
		if ($order == 'postcount') {
			$query .= ' ORDER BY `post_count`';
		}

		if ($order == 'name' || !$order) {
			$query .= ' ORDER BY a.`nickname`';
		}

		//sort
		if ($sort == 'asc') {
			$query .= ' ASC ';
		} else {
			$query .= ' DESC ';
		}

		$limitstart = $this->getState('limitstart', 0);

		//limit
		if (!empty($limit)) {
			if ($usePagination) {
				$query .= " LIMIT $limitstart, $limit";

			} else {
				$query	.= ' LIMIT ' . (INT) $limit;
			}
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if ($limit && $usePagination) {

			$db->setQuery($countQuery);
			$this->total = $db->loadResult();

			$this->pagination = EB::pagination($this->total, $limitstart, $limit);

		}

		return $result;
	}

	/**
	 * Searches for a usertags given a specific keyword
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function search($keyword, $wordSearch = true, $limit = null)
	{
		$db = EB::db();

		$query = array();

		$search = $wordSearch ? '%' . $keyword . '%' : $keyword . '%';

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__users');
		$query[] = 'WHERE ' . $db->quoteName('username') . ' LIKE ' . $db->Quote($search);
		$query[] = 'AND ' . $db->quoteName('block') . ' = ' . $db->Quote(0);

		if ($limit) {
			$query[] = 'LIMIT ' . $limit;
		}

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Searches for a usertags given a specific keyword
	 *
	* @since	5.4.0
	 * @access	public
	 */
	public function suggest($keyword, $limit = null)
	{
		return $this->search($keyword, false, $limit);
	}
}