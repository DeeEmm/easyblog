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

class EasyBlogModelRevisions extends EasyBlogAdminModel
{
	private $total = null;
	public $searchables = array('id');

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.revisions.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.revisions.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Populate current stats
	 *
	 * @since	5.1
	 * @access	public
	 */
	protected function populateState()
	{
		$category = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_category', 'filter_category', '*', 'int');
		$this->setState('filter_category', $category);

		$author = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_blogger', 'filter_blogger', '*', 'int');
		$this->setState('filter_blogger', $author);

		parent::populateState();
	}

	/**
	 * Deletes a list of revisions from a specific post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteRevisions($postId)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_revisions');
		$query[] = 'WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);

		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->query();
	}

	/**
	 * Retrieves all available revisions for specific post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getAllRevisions($postId, $ordering = 'asc')
	{
		$db = EB::db();
		$query  = 'SELECT a.*, IF(b.`revision_id` is null, 0, 1) AS `current`';
		$query .= ' FROM `#__easyblog_revisions` AS a';
		$query .= ' LEFT JOIN `#__easyblog_post` AS b';
		$query .= ' ON a.id = b.`revision_id`';
		$query .= ' WHERE a.`post_id` = '.$db->Quote($postId);

		$ordering = strtoupper($ordering);

		$query .= ' ORDER BY a.`id` ' . $ordering;
		$db->setQuery($query);

		$result = $db->loadObjectList();

		// Bind to revision table and return retult
		$revisions = $this->bindTable('Revision', $result);

		return $revisions;
	}

	/**
	 * Retrieves all available revisions with pending state.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isWaitingApproval($postId)
	{
		$db = EB::db();

		$query = 'SELECT count(*) FROM `#__easyblog_revisions`';
		$query .= ' WHERE `post_id` = ' . $db->Quote($postId);
		$query .= ' AND `state` = ' . $db->Quote(EASYBLOG_REVISION_PENDING);
		$db->setQuery($query);

		$result = $db->loadResult() > 0 ? true : false;

		return $result;
	}

	/**
	 * Retrieves the next ordering for the revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getNextOrder($postId)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT MAX(' . $db->quoteName('ordering') . ') FROM ' . $db->quoteName('#__easyblog_revisions');
		$query[] = 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($postId);

		$query = implode(' ', $query);
		$db->setQuery($query);
		$ordering = $db->loadResult();

		if (!$ordering) {
			return 1;
		}

		return $ordering + 1;
	}


	/**
	 * Retrieves a list of draft posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItems()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.* FROM ' . $db->qn('#__easyblog_revisions') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_post') . ' AS b';
		$query[] = 'ON a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');

		// Need this for table joining
		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_category', 'filter_category', '', 'int');
		
		if ($categoryFilter) {
			$query[] = ' LEFT JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS cat';
			$query[] = ' ON b.' . $db->quoteName('id') . ' = cat.' . $db->qn('post_id');
		}

		$query[] = $this->buildItemsQueryWhere();

		$order = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_order', 'filter_order', 'a.id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_order_Dir', 'filter_order_Dir','DESC', 'word');

		$query[] = ' ORDER BY '. $order .' ' . $direction;

		$query = implode(' ', $query);

		// First we get the total number of records before pagination
		$queryLimit = 'SELECT COUNT(1) ' . str_ireplace(array('SELECT a.*'), '', $query);
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

	private function buildItemsQuery()
	{
		$db	 = EB::db();

		$queryWhere = $this->buildItemsQueryWhere();

		//need this for table joining
		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_category', 'filter_category', '', 'int');

		$query = 'select a.* from ' . $db->qn('#__easyblog_revisions') . ' as a';
		$query .= ' inner join ' . $db->qn('#__easyblog_post') . ' as b on a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');
		if ($categoryFilter) {
			$query .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS cat';
			$query .= ' ON b.' . $db->quoteName('id') . ' = cat.' . $db->quoteName('post_id');
		}

		$query .= $queryWhere;

		$order = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_order', 'filter_order', 'a.id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_order_Dir', 'filter_order_Dir','DESC', 'word');

		$query .= ' ORDER BY '. $order .' ' . $direction;

		// echo $query;

		return $query;
	}


	private function buildItemsQueryWhere()
	{
		$db	 = EB::db();


		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_category', 'filter_category', '', 'int');
		$authorFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_blogger', 'filter_blogger', '', 'int');

		$query = ' where a.' . $db->qn('state') . ' = ' . $db->Quote(EASYBLOG_REVISION_DRAFT);

		// Process search
		$search = $this->app->getUserStateFromRequest('com_easyblog.drafts.search', 'search', '', 'string');
		$search = $db->getEscaped(EBString::strtolower($search));		

		if ($search) {
			// If there is a : in the search query
			$column = 'b.title';
			$value = $search;

			$customSearch = $this->getSearchableItems($search);

			if ($customSearch) {
				$column = 'a.' . strtolower($customSearch->column);
				$value = $customSearch->query;
			}

			$query .= ' AND ' . $db->qn($column) . ' LIKE (' . $db->Quote('%' . $value . '%') . ')';
		}

		if ($categoryFilter) {
			$query .= ' AND cat.`category_id` = ' . $db->Quote($categoryFilter);
		}

		if ($authorFilter ) {
			$query .= ' AND b.`created_by` = ' . $db->Quote($authorFilter);
		}

		return $query;

	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getItemsTotal()
	{
		// Load total number of rows
		if (!$this->total) {

			$db = EB::db();

			$queryWhere = $this->buildItemsQueryWhere();

			$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_category', 'filter_category', '', 'int');

			$query = 'select count(1) from ' . $db->qn('#__easyblog_revisions') . ' as a';
			$query .= ' inner join ' . $db->qn('#__easyblog_post') . ' as b on a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');

			if ($categoryFilter) {
				$query .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS cat';
				$query .= ' ON b.' . $db->quoteName('id') . ' = cat.' . $db->quoteName('post_id');
			}

			$query .= $queryWhere;

			$db->setQuery($query);

			$this->total 	= $db->loadResult();
		}

		return $this->total;
	}

	/**
	 * Retrieves the pagination for administration
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItemsPagination()
	{
		$pagination = EB::Pagination($this->total, $this->getState('limitstart'), $this->getState('limit') );

		return $pagination;
	}

	/**
	 * methods used in frontend
	 */

	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if ($this->total) {
			return $this->total;
		}

		$query = $this->_buildQuery();
		$this->total = @$this->_getListCount($query);

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
		if ($this->pagination) {
			return $this->pagination;
		}

		jimport('joomla.html.pagination');
		$this->pagination = EB::pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));

		return $this->pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery()
	{
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.* FROM ' . $db->qn('#__easyblog_revisions') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_post') . ' AS b';
		$query[] = 'ON a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');
		$query[] = $where;
		$query[] = $orderby;

		$query = implode(' ', $query);

		return $query;
	}

	/**
	 * Builds the where clause for drafts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function _buildQueryWhere()
	{
		$db = EB::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.revisions.filter_state', 'filter_state', '', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.revisions.search', 'search', '', 'string');
		$search = $db->getEscaped(EBString::strtolower($search));

		$where = array();

		if (! $filter_state) {
			$where[] = 'a.' . $db->qn('state') . ' IN (' . $db->Quote(EASYBLOG_REVISION_DRAFT) . ',' . $db->Quote(EASYBLOG_REVISION_FINALIZED) . ')';
		}

		// Get draft created by the current user
		$my = JFactory::getUser();

		$where[] = 'a.' . $db->qn('created_by') . '=' . $db->Quote($my->id);

		if ($search) {
			$where[] = ' LOWER( a.' . $db->qn('title') . ' ) LIKE \'%' . $search . '%\' ';
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.revisions.filter_order', 		'filter_order', 	'created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.revisions.filter_order_Dir',	'filter_order_Dir',	'desc', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData( $usePagination = true )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			if ($usePagination) {
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}

	/*
	 * Discards all drafts for the specific user.
	 *
	 * @param	int		$userID		The subject's id.
	 * @return	boolean	true on success false otherwise.
	 */
	public function discard( $userId )
	{
		$db		= EB::db();
		// $query	= 'DELETE FROM ' . $db->qn( '#__easyblog_revisions' ) . ' '
		// 		. 'WHERE ' . $db->qn( 'created_by' ) . '=' . $db->Quote( $userId ) . ''
		// 		. 'AND `state` = ' . $db->Quote( EASYBLOG_REVISION_PENDING );

		// $db->setQuery( $query );
		// return $db->Query();
	}

	/**
	 * Method to get revisions item data
	 * @since	5.0
	 * @access	public
	 * @param	array
	 * @return array
	 */
	public function getRevisions($options = array())
	{
		$db = EB::db();

		$where = array();

		if (isset($options['userId']) && $options['userId']) {
			$where[] = "a." . $db->qn('created_by') . " = " . $db->Quote($options['userId']);
		}

		if (isset($options['postId']) && $options['postId']) {
			$where[] = "a." . $db->qn('post_id') . " = " . $db->Quote($options['postId']);
		}

		if (isset($options['state'])) {
			$where[] = "a." . $db->qn('state') . " = " . $db->Quote($options['state']);
		} else {
			// we only retrieve items whichs under 'drafts' or 'finalized'
			$where[] = "a." . $db->qn('state') . " IN (" . $db->Quote(EASYBLOG_REVISION_DRAFT) . "," . $db->Quote(EASYBLOG_REVISION_FINALIZED) . ")";
		}

		if (isset($options['search']) && $options['search']) {
			$where[] = " LOWER( b." . $db->qn('title') . " ) LIKE '%" . $options['search'] . "%' ";
		}

		$where = ( count( $where ) ? " WHERE " . implode( " AND ", $where ) : "" );

		// total count sql
		$totalQuery = "select count(1) from " . $db->qn('#__easyblog_revisions') . " as a";
		$totalQuery .= " inner join " . $db->qn('#__easyblog_post') . " as b";
		$totalQuery .= " on a." . $db->qn('post_id') . " = b." . $db->qn('id');
		$totalQuery .= $where;

		// echo $totalQuery;exit;


		$db->setQuery($totalQuery);
		$this->total = $db->loadResult();


		// actual query here.
		$query = "select a.* from " . $db->qn('#__easyblog_revisions') . " as a";
		$query .= " inner join " . $db->qn('#__easyblog_post') . " as b";
		$query .= " on a." . $db->qn('post_id') . " = b." . $db->qn('id');
		$query .= $where;
		$query .= " order by a." . $db->qn('post_id') . " desc, a." . $db->qn('ordering') . " desc";

		$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		return $this->_data;
	}

	/**
	 * Method to get count for a revision type for a blog post
	 * @since	5.0
	 * @access	public
	 * @param	array
	 * @return array
	 */
	public function getRevisionCount($postId, $type)
	{
		static $_cache = array();
		$db = EB::db();

		// check if this is a caching or not.
		if ($type == 'cache' && is_array($postId) && $postId) {

			// preload the default array
			foreach($postId as $pid) {
				$_cache[$pid] = array();
			}

			$query = "select a.`id`,";
			$query .= " (select count(1) from `#__easyblog_revisions` as r1 where r1.`post_id` = a.`id` and r1.`state` = " . $db->Quote(EASYBLOG_REVISION_DRAFT). " ) as " . $db->Quote('draft') . ",";
			$query .= " (select count(1) from `#__easyblog_revisions` as r2 where r2.`post_id` = a.`id` and r2.`state` = " . $db->Quote(EASYBLOG_REVISION_FINALIZED). " ) as " . $db->Quote('finalized');
			$query .= " from `#__easyblog_post` as a where a.`id` IN (" . implode(',', $postId) . ")";

			$db->setQuery($query);

			$results = $db->loadObjectList();

			if ($results) {
				foreach($results as $item) {

					$_cache[$item->id][EASYBLOG_REVISION_DRAFT] = $item->draft;
					$_cache[$item->id][EASYBLOG_REVISION_FINALIZED] = $item->finalized;
				}
			}

			return true;

		} else {

			if (! isset($_cache[$postId])) {

				$query = "select count(1) as " . $db->qn('cnt') . ", a." . $db->qn('state') . " from " . $db->qn('#__easyblog_revisions') . " as a";
				$query .= " inner join " . $db->qn('#__easyblog_post') . " as b";
				$query .= " on a." . $db->qn('post_id') . " = b." . $db->qn('id');
				$query .= " where a." . $db->qn('post_id') . " = " . $db->Quote($postId);
				$query .= " group by a." . $db->qn('state');

				$db->setQuery($query);

				$results = $db->loadObjectList();

				if ($results) {
					foreach($results as $row) {
						$_cache[$postId][$row->state] = $row->cnt;
					}
				} else {
					// we know this post do not have any revisions. return 0 to each state
					$_cache[$postId][EASYBLOG_REVISION_FINALIZED] = 0;
					$_cache[$postId][EASYBLOG_REVISION_DRAFT] = 0;
				}
			}

			// must use strict compare all else the checking will failed
			if ($type === 'all') {

				// when the type is all, we only want to return the count of finalized and draft. Never return pending revision here.
				$draftCnt = isset($_cache[$postId][EASYBLOG_REVISION_DRAFT]) ? $_cache[$postId][EASYBLOG_REVISION_DRAFT] : 0;
				$finalizeCnt = isset($_cache[$postId][EASYBLOG_REVISION_FINALIZED]) ? $_cache[$postId][EASYBLOG_REVISION_FINALIZED] : 0;

				return $draftCnt + $finalizeCnt;
			}

			return isset($_cache[$postId][$type]) ? $_cache[$postId][$type] : 0;
		}

	}

	/**
	 * Method to pre-load posts revisions.
	 * @since	5.0
	 * @access	public
	 * @param	array
	 * @return array
	 */
	public function preload(array $revIds)
	{
		$db = EB::db();
		$results = array();

		if ($revIds) {
			$query = array();
			$query[] = 'select a.* from `#__easyblog_revisions` as a';
			$query[] = 'where a.`id` IN ( ' . implode(',' , $revIds) . ')';

			$db->setQuery($query);
			$results = $db->loadObjectList();
		}

		return $results;
	}

	/**
	 * Method to clean up revisiosn records based on the history revision limit settings.
	 * @since	5.1
	 * @access	public
	 * @param	int Post id
	 * @param	int Current used revision id
	 * @param	array options
	 * @return boolean
	 */
	public function cleanUp($postId, $latestRevId, $options = array())
	{
		$toLatest = $options && isset($options['toLatest']) && $options['toLatest'] ? true : false;
		$toLimit = $options && isset($options['toLimit']) && $options['toLimit'] ? $options['toLimit'] : false;

		if ($toLatest === false && $toLimit === false) {
			// nothign to process
			return true;
		}

		if ($toLatest) {
			$this->cleanUpToLatest($postId, $latestRevId);
		} else if ($toLimit) {
			$this->cleanUpToLimit($postId, $latestRevId, $toLimit);
		}

		return true;
	}

	/**
	 * Method to clean up revisiosn records based on the history revision limit settings.
	 * @since	5.1
	 * @access	public
	 * @return boolean
	 */
	private function cleanUpToLatest($postId, $latestRevId)
	{
		$db = EB::db();

		$query = "delete from `#__easyblog_revisions`";
		$query .= " where `post_id` = " . $db->Quote($postId);

		if (is_array($latestRevId)) {
			$query .= " and `id` NOT IN (" . implode(',', $latestRevId) . ")";
		} else {
			$query .= " and `id` != " . $db->Quote($latestRevId);
		}

		$db->setQuery($query);
		$db->query();

	}

	/**
	 * Method to clean up revisiosn records based on the history revision limit settings.
	 * @since	5.1
	 * @access	public
	 * @return boolean
	 */
	private function cleanUpToLimit($postId, $latestRevId, $max)
	{
		$db = EB::db();

		// get the usable revisions id.
		$query = "select `id` from `#__easyblog_revisions`";
		$query .= " where `post_id` = " . $db->Quote($postId);
		$query .= " and `state` = " . $db->Quote(EASYBLOG_REVISION_FINALIZED);
		$query .= " order by `modified` desc";
		$query .= " limit $max";

		$db->setQuery($query);
		$results = $db->loadColumn();

		// dump($results);

		$results[] = $latestRevId;
		$ids = array_unique($results);

		// now we remove any revisions that is no from the 'usable' ids.
		$query = "delete from `#__easyblog_revisions`";
		$query .= " where `post_id` = " . $db->Quote($postId);
		$query .= " and `id` NOT IN (" . implode(',', $ids) . ")";

		$db->setQuery($query);
		$db->query();

	}


	/**
	 * To retrieve latest draft revision id for a blog if there is any.
	 * @since	5.1
	 * @access	public
	 * @param	post id
	 * @return
	 */
	public function getLatestDraftRevision($postId)
	{
		$db = EB::db();

		$query = "select * from `#__easyblog_revisions`";
		$query .= " where `post_id` = " . $db->Quote($postId);
		$query .= " and `state` = " . $db->Quote(EASYBLOG_REVISION_DRAFT);
		$query .= " order by `id` desc limit 1";

		$db->setQuery($query);
		$result = $db->loadObject();

		if (! $result) {
			return false;
		}

		$table = EB::table('Revision');
		$table->bind($result);

		return $table;
	}

	/**
	 * Delete all draft revision of a blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeDraftRevisions($postId)
	{
		$db = EB::db();

		$query = "delete from `#__easyblog_revisions`";
		$query .= " where `post_id` = " . $db->Quote($postId);
		$query .= " and `state` = " . $db->Quote(EASYBLOG_REVISION_DRAFT);

		$db->setQuery($query);
		$db->query();

		return true;
	}
}
