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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelFeeds extends EasyBlogAdminModel
{
	public $total = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.feeds.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.feeds.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the pagination used at the back end
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getPagination()
	{
		$pagination = EB::Pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));
		return $pagination;
	}

	/**
	 * Method to build the query for the feeds
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();

		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->nameQuote('#__easyblog_feeds');
		$query .= $where . ' ';
		$query .= $orderby;

		$queryLimit = $query;
		$queryLimit = str_replace('SELECT *', '', $queryLimit);

		// count total of the existing feeds
		$queryLimit = 'SELECT COUNT(1) ' . $queryLimit;

		$db->setQuery($queryLimit);
		$this->total = (int) $db->loadResult();

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		// Reset the limitstart (perhaps caused by other filters)
		if ($this->total <= $limitstart) {
			$limitstart = 0;
			$this->setState('limitstart', 0);
		}

		if ($limit) {
			$query .= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	public function _buildQueryWhere()
	{
		$db = EB::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.feeds.filter_state', 'filter_state', '', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.feeds.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('1');
			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where)? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$filter_order = $this->app->getUserStateFromRequest('com_easyblog.feeds.filter_order', 'filter_order', 'created', 'cmd');
		$filter_order_Dir = $this->app->getUserStateFromRequest('com_easyblog.feeds.filter_order_Dir',	'filter_order_Dir',	'', 'word');

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', id';

		return $orderby;
	}

	/**
	 * Retrieves a list of feeds URL from the back end
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getData()
	{
		$data = $this->_buildQuery();

		return $data;
	}

	/**
	 * Determines if a feed item has already been imported
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isFeedItemImported($feedId, $uid)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_feeds_history');
		$query[] = 'WHERE ' . $db->quoteName('feed_id') . '=' . $db->Quote($feedId);
		$query[] = 'AND ' . $db->quoteName('uid') . '=' . $db->Quote($uid);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$exists = $db->loadResult() > 0;

		return $exists;
	}

	/**
	 * Retrieves a list of feed items that needs to be imported
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPendingFeeds($limit = 1, $debug = false)
	{
		$limit = (int) $limit;

		$db = EB::db();
		$now = EB::date();
		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_feeds');
		$query[] = 'WHERE ' . $db->quoteName('cron') . '=' . $db->quote(1);
		$query[] = 'AND ' . $db->quoteName('flag') . '=' . $db->quote(0);
		$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->quote(EASYBLOG_POST_PUBLISHED);

		if (!$debug) {
			$query[] = 'AND (';
			$query[] = $db->quote($now->toSql()) . '>= DATE_ADD(' . $db->quoteName('last_import') . ', INTERVAL ' . $db->quoteName('interval') . ' MINUTE)';
			$query[] = 'OR';
			$query[] = $db->quoteName('last_import') . '=' . $db->Quote('0000-00-00 00:00:00');
			$query[] = ')';
		}

		$query[] = 'ORDER BY ' . $db->quoteName('last_import');
		$query[] = 'LIMIT ' . $limit;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$feeds = array();

		foreach ($result as $row) {
			$feed = EB::table('Feed');
			$feed->bind($row);

			$feeds[] = $feed;
		}

		return $feeds;
	}

	/**
	 * Publishes feeds
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function publish(&$feeds = array(), $publish = 1)
	{
		if (count($feeds) > 0) {
			
			$db	= EB::db();

			$query	= 'UPDATE ' . $db->nameQuote('#__easyblog_feeds') . ' '
					. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (';

			for ($i = 0; $i < count($feeds);$i++) {
				
				$query .= $db->Quote($feeds[$i]);

				if (next($feeds) !== false) {
					$query	.= ',';
				}
			}

			$query .= ')';

			$db->setQuery($query);

			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
	}
}