<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelSubscriptions extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;
	public $data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.subscriptions.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getSubscriptions($sort = 'latest', $filter='site')
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery()
	{

		$db = EB::db();
		$mainframe = JFactory::getApplication();

		$filter = $mainframe->getUserStateFromRequest('com_easyblog.subscriptions.filter', 'filter', EBLOG_SUBSCRIPTION_SITE, 'word');

		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();

		$query  = '';

		if ($filter	== EBLOG_SUBSCRIPTION_ENTRY) {
			$query .= 'SELECT a.*, b.`title` as `bname`, c.`name`, c.`username`';
			$query .= '  FROM `#__easyblog_subscriptions` a';
			$query .= '    inner join `#__easyblog_post` b on a.`uid` = b.`id`';
			$query .= '    left join `#__users` c on a.`user_id` = c.`id`';

		} else if ($filter == EBLOG_SUBSCRIPTION_CATEGORY) {
			$query .= 'SELECT a.*, b.`title` as `bname`, c.`name`, c.`username`';
			$query .= '  FROM `#__easyblog_subscriptions` a';
			$query .= '    inner join `#__easyblog_category` b on a.`uid` = b.`id`';
			$query .= '    left join `#__users` c on a.`user_id` = c.`id`';

		} else if ($filter == EBLOG_SUBSCRIPTION_SITE) {
			$query .= 'SELECT a.*, '.$db->Quote('site').' as `bname`, c.`name`, c.`username`';
			$query .= '  FROM `#__easyblog_subscriptions` a';
			$query .= '    left join `#__users` c on a.`user_id` = c.`id`';

		} else if ($filter == EBLOG_SUBSCRIPTION_TEAMBLOG) {
			$query .= 'SELECT a.*, b.`title` as `bname`, c.`name`, c.`username`';
			$query .= '  FROM `#__easyblog_subscriptions` a';
			$query .= '    inner join `#__easyblog_team` b on a.`uid` = b.`id`';
			$query .= '    left join `#__users` c on a.`user_id` = c.`id`';

		} else {
			$query .= 'SELECT a.*, b.`name` as `bname`, b.`username` as `busername`, c.`name`, c.`username`';
			$query .= '  FROM `#__easyblog_subscriptions` a';
			$query .= '    inner join `#__users` b on a.`uid` = b.`id`';
			$query .= '    left join `#__users` c on a.`user_id` = c.`id`';
		}

		$query .= $where;
		$query .= $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();

		$filter = $mainframe->getUserStateFromRequest('com_easyblog.subscriptions.filter', 'filter', EBLOG_SUBSCRIPTION_SITE, 'word');
		$search = $mainframe->getUserStateFromRequest('com_easyblog.subscriptions.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$queryWhere = ' WHERE a.`utype` = ' . $db->Quote($filter);

		$where = array();

		if ($search) {
			$where[] = ' LOWER( a.`email` ) LIKE \'%' . $search . '%\'';

			if ($filter == EBLOG_SUBSCRIPTION_BLOGGER) {
				$where[] = ' LOWER( b.`name` ) LIKE \'%' . $search . '%\'';

			} else if ($filter != EBLOG_SUBSCRIPTION_SITE) {
				$where[] = ' LOWER( b.`title` ) LIKE \'%' . $search . '%\'';

			}

			$where = implode(' OR ', $where);

			$queryWhere .= ' AND (' . $where . ')';
		}

		return $queryWhere;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest( 'com_easyblog.subscriptions.filter_order', 		'filter_order', 	'bname', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'com_easyblog.subscriptions.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir . ', a.`email`';

		return $orderby;
	}

	/**
	 * Method to get the total number of records
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}


	/**
	 * Method to get a pagination object
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = EB::Pagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	public function getSiteSubscribers()
	{
		$db = EB::db();

		$query  = "SELECT *, 'sitesubscription' as `type` FROM `#__easyblog_subscriptions` where `utype` = " . $db->Quote(EBLOG_SUBSCRIPTION_SITE);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Allows a caller to remove a subscription
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteSubscriptions($uid, $type)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->qn('#__easyblog_subscriptions');
		$query[] = 'WHERE ' . $db->qn('uid') . '=' . $db->Quote($uid);
		$query[] = 'AND ' . $db->qn('utype') . '=' . $db->Quote($type);

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Allows a caller to remove a subscription
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function deleteUserSubscriptions($key, $type = 'user_id')
	{
		$db = EB::db();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->qn('#__easyblog_subscriptions');
		$query[] = 'WHERE ' . $db->qn($type) . '=' . $db->Quote($key);

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Retrieves a list of subscriptions a user has
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getSubscriptionsByUser($userId = null)
	{
		$user = JFactory::getUser($userId);
		$id = $user->id;

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_subscriptions');
		$query[] = 'WHERE ' . $db->qn('user_id') . '=' . $db->Quote($id);

		$query = implode(' ', $query);
		
		$db->setQuery($query);

		$rows = $db->loadObjectlist();

		if (!$rows) {
			return $rows;
		}

		$subscriptions = array();

		foreach ($rows as $row) {
			$subscription = EB::table('Subscriptions');
			$subscription->bind($row);

			$subscriptions[] = $subscription;
		}

		return $subscriptions;
	}
}
