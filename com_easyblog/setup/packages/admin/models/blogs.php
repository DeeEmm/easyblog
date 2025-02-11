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

class EasyBlogModelBlogs extends EasyBlogAdminModel
{
	// Temporary storage for pagination
	public $total;

	public $searchables = array('id', 'permalink');

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.blogs.limitstart', 'limitstart', 0, 'int');

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
		// Publishing state
		$state = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_state', 'filter_state');
		$this->setState('filter_state', $state);

		// Category
		$category = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_category', 'filter_category');
		$this->setState('filter_category', $category);

		// Post Type
		$postType = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_posttype', 'filter_posttype');
		$this->setState('filter_posttype', $postType);

		// Language
		$language = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_language', 'filter_language');
		$this->setState('filter_language', $language);

		// Ordering
		$order = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order', 'filter_order');
		$this->setState('filter_order', $order);

		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir');
		$this->setState('filter_order_Dir', $orderDirection);

		// Author
		$author = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_blogger', 'filter_blogger');
		$this->setState('filter_blogger', $author);

		// List state information.
		parent::populateState();
	}

	/**
	 * Retrieves a list of blog posts created on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getData($userId = null)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildDataQueryWhere();

		// Get the db
		$db = EB::db();

		// Get custom sorting
		$customSorting = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_sort_by', 'filter_sort_by', '', 'word');

		$query = 'SELECT DISTINCT a.`id`';

		if ($customSorting == 'most_rated') {
			$query .= ', count(r.`uid`) as total_rated';
		}

		$query .= ' FROM ' . $db->nameQuote('#__easyblog_post') . ' AS a ';

		// Get the current state
		$state = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_state', 'filter_state', '', 'word' );

		if ($state == 'F') {
			$query 	.= ' INNER JOIN #__easyblog_featured AS `featured`';
			$query	.= ' ON a.`id` = featured.`content_id` AND featured.`type` = "post"';
		}

		// Always join with the category table
		$query .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS cat';
		$query .= ' ON a.' . $db->quoteName('id') . ' = cat.' . $db->quoteName('post_id');

		// Filter by tags
		$tag = $this->input->get('tagid', 0, 'int');

		if ($tag) {
			$query	.= ' INNER JOIN #__easyblog_post_tag AS b ';
			$query	.= 'ON a.`id`=b.`post_id` AND b.`tag_id`=' . $db->Quote($tag);
		}

		$query	.= ' LEFT JOIN #__easyblog_featured AS f ';
		$query	.= ' ON a.`id` = f.`content_id` AND f.`type`="post"';

		if ($customSorting == 'most_rated' || $customSorting == 'highest_rated') {
			$query .= ' LEFT JOIN #__easyblog_ratings AS r';
			$query .= ' ON a.`id` = r.`uid`';
		}

		$customQuery = '';

		if ($customSorting) {
			$direction = 'DESC';

			switch ($customSorting) {
				case 'latest':
					$ordering = 'a.`created`';
					break;
				case 'oldest':
					$ordering = 'a.`created`';
					$direction = 'ASC';
					break;
				case 'popular':
					$ordering = 'a.`hits`';
					break;
				case 'highest_rated':
					$ordering = 'r.`value`';
					break;
				case 'most_rated':
					$ordering = 'total_rated';
					$customQuery .= ' GROUP BY a.`id` ';
					break;
				default:
					$ordering = 'a.`id`';
					break;
			}

		} else {
			$ordering = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order', 'filter_order', 'a.id', 'cmd');
			$direction = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		}

		$query .= $where;
		$query .= $customQuery;

		// prepare the query used for total count
		$cntQuery = $query;

		$query .= ' ORDER BY '. $ordering .' ' . $direction .', ordering';

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		$mainQuery = $query;

		if ($limit) {
			$mainQuery = $query . ' LIMIT ' . $limitstart . ',' . $limit;
		}

		// $db->setQuery($mainQuery);
		// $data = $db->loadObjectList();

		// Get Total
		// $queryLimit = 'select FOUND_ROWS()';

		$queryLimit = str_replace('SELECT DISTINCT a.`id`', 'SELECT COUNT(DISTINCT(a.`id`))', $cntQuery);

		$db->setQuery($queryLimit);
		$this->total = (int) $db->loadResult();

		// Reset the limitstart (perhaps caused by other filters)
		if ($this->total <= $limitstart) {
			$limitstart = 0;
			$this->setState('limitstart', 0);
		}

		// Rerun the query with new limitstart
		if ($limit) {
			$mainQuery = $query . ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$db->setQuery($mainQuery);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * Builds the where statement
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function _buildDataQueryWhere()
	{
		$db = EB::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_state', 'filter_state', '', 'word' );
		$filter_category = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_category', 'filter_category', '', 'int' );
		$filter_blogger = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_blogger' , 'filter_blogger' , '' , 'int' );
		$filter_language = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_language' , 'filter_language' , '' , '' );
		$filter_posttype = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_posttype', 'filter_posttype', '', 'word');

		$endDate = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_end_date', 'filter_end_date', '', '');
		$startDate = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_start_date', 'filter_start_date', '', '');



		// Filter by source
		$source = $this->input->get('filter_source', '-1', 'default');

		$where = array();

		switch($filter_state) {
			case 'U':
				// Unpublished posts
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;

			case 'S':
				// Scheduled posts
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_SCHEDULED);
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;

			case 'T':
				// trashed posts
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_TRASHED);
				break;

			case 'A':
				// archived posts
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_ARCHIVED);
				break;

			case 'P':
				// Published posts only
				$where[] = 'a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;

			case 'FP':
				// Frontpage Post
				$where[] = 'a.`frontpage` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);

			default:
				$where[] = 'a.' . $db->qn('published') . ' IN (' . $db->Quote(EASYBLOG_POST_PUBLISHED) . ',' . $db->Quote(EASYBLOG_POST_UNPUBLISHED) . ',' . $db->Quote(EASYBLOG_POST_SCHEDULED) . ')';
				$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;
		}

		if ($source != '-1') {
			$where[]	= 'a.' . $db->nameQuote( 'source' ) . '=' . $db->Quote( $source );
		}

		if ($filter_category) {
			$where[] = ' cat.`category_id` = ' . $db->Quote($filter_category);
		}

		if ($filter_posttype) {
			if ($filter_posttype == 'text') {
				$where[] = 'a.' . $db->nameQuote('posttype') . '=' . $db->Quote('');
			} else {
				$where[] = 'a.' . $db->nameQuote('posttype') . '=' . $db->Quote($filter_posttype);
			}
		}

		if ($filter_blogger) {
			$where[] = ' a.`created_by` = ' . $db->Quote($filter_blogger);
		}

		if ($filter_language && $filter_language != '*') {
			$where[] = ' a.`language`= ' . $db->Quote($filter_language);
		}


		// Process search
		$search = $this->app->getUserStateFromRequest('com_easyblog.blogs.search', 'search', '', 'string');

		if ($search) {
			// If there is a : in the search query
			$column = 'a.title';
			$value = $search;

			$customSearch = $this->getSearchableItems($search);

			if ($customSearch) {
				$column = 'a.' . strtolower($customSearch->column);
				$value = $customSearch->query;
			}

			$where[] = $db->qn($column) . ' LIKE ' . $db->Quote('%' . $value . '%');
		}

		if ($filter_state == 'date' && ($endDate || $startDate)) {

			if ($startDate) {
				$startDate = EB::date($startDate)->toSql();

				$where[] = ' a.`created` > ' . $db->Quote($startDate);
			}

			if ($endDate) {
				$endDate = EB::date($endDate . ' +23 hour +59 minutes')->toSql();
			} else {
				// By default filter up until today date
				$endDate = EB::date()->toSql();
			}

			$where[] = ' a.`created` < ' . $db->Quote($endDate);
		}

		$where = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

		return $where;
	}

	/**
	 * Retrieve pagination for the post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getDataPagination()
	{
		$pagination = EB::Pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));
		return $pagination;
	}

	/*
	 * common method used in frontend and backend
	 */
	public function _buildQueryOrderBy()
	{
		$ordering = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order', 'filter_order', 'a.id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir',	'DESC', 'word');

		$query = ' ORDER BY '. $ordering .' ' . $direction .', ordering';

		return $query;
	}

	/**
	 * Retrieves a list of scheduled posts
	 * The first parameter 'limit' must set it to 1 for prevent those social network treat it as spam 
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getScheduledPosts($limit = 1)
	{
		$db = EB::db();

		// Get the current date
		$date = EB::date();

		$query 	= array();
		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('publish_up') . '<=' . $db->Quote($date->toSql());
		$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_SCHEDULED);
		$query[] = 'AND ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'ORDER BY ' . $db->quoteName('id');

		if ($limit) {
			$query[] = 'LIMIT ' . $limit;
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$blog = EB::table('Post');
			$blog->bind($row);

			$posts[] = $blog;
		}

		return $posts;
	}

	/**
	 * Retrieves a list of scheduled autopost
	 * The first parameter 'limit' must set it to 1 for prevent those social network treat it as spam
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getScheduledAutoposts($limit = 1)
	{
		$db = EB::db();

		// Get the current date
		$date = EB::date();

		$query  = array();
		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post') . ' as a';
		$query[] = 'WHERE NOT EXISTS (SELECT post_id FROM ' . $db->quoteName('#__easyblog_oauth_logs') . ' as b where b.`post_id` = a.`id`)';
		$query[] = 'AND ' . $db->quoteName('autopost_date') . '<=' . $db->Quote($date->toSql());
		$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND ' . $db->quoteName('autopost_date') . ' != ' . $db->Quote('0000-00-00 00:00:00');
		$query[] = 'AND ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'ORDER BY ' . $db->quoteName('id');

		if ($limit) {
			$query[] = 'LIMIT ' . $limit;
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$blog = EB::table('Post');
			$blog->bind($row);

			$posts[] = $blog;
		}

		return $posts;
	}

	/**
	 * Retrieves a list of post for re-posting
	 *
	 * @since	5.2.8
	 * @access	public
	 */
	public function getCategoryRepostingPosts($limit = 1)
	{
		$db = EB::db();

		// Get the current date
		$now = EB::date()->toSql();

		$query = "select distinct a.`id`, c.`repost_autoposting_interval`";
		$query .= " from `#__easyblog_post` as a";
		$query .= "	inner join `#__easyblog_post_category` as b on a.`id` = b.`post_id` and b.`primary` = 1";
		$query .= "	inner join `#__easyblog_category` as c on b.`category_id` = c.`id`";
		$query .= "	inner join `#__easyblog_oauth_logs` as d on a.`id` = d.`post_id`";
		$query .= " where c.`repost_autoposting` = 1";
		$query .= " and c.`repost_autoposting_interval` > 0";
		$query .= " and a.`reautopost` = 0";
		$query .= " and d.`status` = 1";
		$query .= " AND a.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= " AND a.`state` = " . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= " GROUP BY a.`id` HAVING (MAX(d.created) <= DATE_SUB(" . $db->Quote($now) . ", INTERVAL c.`repost_autoposting_interval` DAY))";

		if ($limit) {
			$query .= " LIMIT " . $limit;
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		return $result;
	}


	public function updateCategoryRepostingFlag($ids)
	{
		if (!$ids) {
			return false;
		}

		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$db = EB::db();

		$query = "update `#__easyblog_post` set `reautopost` = 1";
		$query .= " where `id` IN (" . implode(',', $ids) . ")";

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}


	/**
	 * Get all blog posts thare are scheduled to be unpublised
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUnpublishScheduledPosts()
	{
		$db = EB::db();
		$date = EB::date();

		$query = array();

		$query[] = 'SELECT id, created_by FROM' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('publish_down') . ' > ' . $db->quoteName('publish_up');
		$query[] = 'AND ' . $db->quoteName('publish_down') . ' <= ' . $db->Quote($date->toSql());
		$query[] = 'AND ' . $db->quoteName('publish_down') . ' != ' . $db->Quote('0000-00-00 00:00:00');
		$query[] = 'AND ' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND ' . $db->quoteName('state') . ' = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Unpublishes blog posts that are scheduled to be unpublished
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublishScheduledPosts()
	{
		$db = EB::db();
		$date = EB::date();

		$query = array();

		$query[] = 'UPDATE ' . $db->quoteName('#__easyblog_post');
		$query[] = 'SET ' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);
		$query[] = 'WHERE ' . $db->quoteName('publish_down') . ' > ' . $db->quoteName('publish_up');
		$query[] = 'AND ' . $db->quoteName('publish_down') . ' <= ' . $db->Quote($date->toSql());
		$query[] = 'AND ' . $db->quoteName('publish_down') . ' != ' . $db->Quote('0000-00-00 00:00:00');
		$query[] = 'AND ' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND ' . $db->quoteName('state') . ' = ' . $db->Quote(EASYBLOG_POST_NORMAL);


		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Sets the publish state of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function publish(&$ids = [], $publish = 1)
	{
		if (!$ids) {
			return false;
		}

		$db = EB::db();

		foreach ($ids as $id) {
			$post = EB::post((int) $id);

			if ($publish == 1) {
				$post->publish();
			}

			if ($publish == 0) {
				$post->unpublish();
			}
		}

		return true;
	}

	/**
	 * Deletes a list of blog posts flagged as trash
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function emptyTrash()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_TRASHED);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return false;
		}

		foreach ($result as $row) {
			$post = EB::post($row->id);
			$post->delete();
		}

		return true;
	}

	public function getTotalPublished($uid)
	{
		$db		= EB::db();
		$query	= 'SELECT COUNT(1) AS `total`' .
				  ' FROM ' . $db->nameQuote( '#__easyblog_post' ) .
				  ' WHERE ' . $db->nameQuote( 'created_by' ) . '=' . $db->Quote( $uid ) .
				  ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED) .
				  ' AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		//blog privacy setting
		$my = JFactory::getUser();
		if ($my->id == 0) {
			$query .= ' AND `access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		$db->setQuery( $query );

		$result	= $db->loadResult();
		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Retrieves the total number of pending posts from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPending()
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_post') . 'as a';
		$query[] = ' INNER JOIN ' . $db->qn('#__easyblog_revisions') . 'as b ON a.`id` = b.`post_id`';
		$query[] = 'WHERE ' . $db->qn('b.state') . '=' . $db->Quote(EASYBLOG_REVISION_PENDING);
		$query[] = ' AND ' . $db->qn('a.state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	public function getTotalUnpublished( $uid )
	{
		$db		= EB::db();
		$query	= 'SELECT COUNT(1) AS `total`' .
				  ' FROM ' . $db->nameQuote( '#__easyblog_post' ) .
				  ' WHERE ' . $db->nameQuote( 'created_by' ) . '=' . $db->Quote( $uid ) .
				  ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_UNPUBLISHED) .
				  ' AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
			$query .= ' AND `access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Retrieves a list of blog posts on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getBlogs($userId = null, $options = array())
	{
		$query = $this->_buildQuery($userId);

		// Apply limit for the blogs
		$query	.= ' LIMIT ' . $this->getState('limitstart') . ',' . $this->getState('limit');

		$db = EB::db();
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$post = EB::table('Post');

			$post->bind($row);

			$posts[] = $post;
		}

		return $posts;
	}

	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$db = EB::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_post' )
				. $where;

		$ordering = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order', 'filter_order', 'id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir',	'DESC', 'word');

		$query .= ' ORDER BY '. $ordering .' ' . $direction .', ordering';

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= EB::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(EBString::strtolower( $search ) ) );

		$where = array();

		//blog privacy setting
		$my = JFactory::getUser();
		if ($my->id == 0) {
			$where[] = $db->nameQuote('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);

			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);

			}
		}

		if ($search) {
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Load total number of rows
		if (empty($this->_total)) {
			$db = EB::db();
			$query = 'SELECT COUNT(1) FROM `#__easyblog_post`';
			$db->setQuery($query);

			$this->_total = $db->loadResult();
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination(  $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Retrieves a list of post for auto archiving process
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function getAutoAchivingPosts($limit = 10)
	{
		$db = EB::db();
		$config = EB::config();

		// Get the current date
		$now = EB::date()->toSql();
		$months = $config->get('main_archiving_duration', 12);

		$query = "select a.`id`";
		$query .= " from `#__easyblog_post` as a";
		$query .= " WHERE a.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= " AND a.`state` = " . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= " AND a.`created` <= DATE_SUB(" . $db->Quote($now) . ", INTERVAL " . $months . " MONTH)";

		if ($limit) {
			$query .= " LIMIT " . $limit;
		}

		$db->setQuery($query);
		$results = $db->loadColumn();

		return $results;
	}

	/**
	 * Export Post Templates
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function exportTemplates($ids)
	{
		$db = EB::db();
		
		$columns = array(
			'user_id',
			'title',
			'data',
			'created',
			'system',
			'core',
			'screenshot',
			'published',
			'datafix',
			'doctype',
			'ordering'
		);

		$columns = implode(',', $db->nameQuote($columns));

		$query = array();
		$query[] = 'SELECT ' . $columns . ' FROM `#__easyblog_post_templates`';
		$query[] = 'WHERE `id` IN(' . implode(',', $db->Quote($ids)) . ')';

		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
