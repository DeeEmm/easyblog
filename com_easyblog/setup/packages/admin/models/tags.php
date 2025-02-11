<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyBlogModelTags extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.tags.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Populate current stats
	 *
	 * @since	5.2
	 * @access	public
	 */
	protected function populateState()
	{
		parent::populateState();
	}

	/**
	 * Generates pagination for the back end
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getPagination()
	{
		if ($this->pagination) {
			return $this->pagination;
		}

		$pagination = EB::Pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));

		return $pagination;
	}

	/**
	 * Generates the listing of tags at the back end
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getData()
	{
		$db = EB::db();
		$query = array();
		$query[] = 'SELECT a.* FROM ' . $db->qn('#__easyblog_tag') . ' AS a';
		$query[] = 'WHERE 1';

		// Filters
		$filter = $this->app->getUserStateFromRequest('com_easyblog.tags.filter_state', 'filter_state', '', 'word');

		if ($filter == 'P') {
			$query[] = 'AND ' . $db->qn('a.published') . '=' . $db->Quote('1');
		}

		if ($filter == 'U') {
			$query[] = 'AND ' . $db->qn('a.published') . '=' . $db->Quote('0');
		}

		// Search
		$search = $this->app->getUserStateFromRequest('com_easyblog.tags.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));

		if ($search) {
			$query[] = 'AND LOWER(' . $db->qn('a.title') . ') LIKE ' . $db->Quote('%' . $search . '%');
		}

		// Ordering
		$ordering = $this->app->getUserStateFromRequest('com_easyblog.tags.filter_order', 'filter_order', 'a.ordering', 'cmd');
		$orderingDirection = $this->app->getUserStateFromRequest('com_easyblog.tags.filter_order_Dir', 'filter_order_Dir', '', 'word');

		// $query[] = 'ORDER BY ' . $ordering . ' ' . $orderingDirection . ', ' . $db->qn('ordering');
		$query[] = 'ORDER BY ' . $ordering . ' ' . $orderingDirection;

		$query = implode(' ', $query);

		// echo $query;exit;

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

	/**
	 * Set the row's state given the column
	 *
	 * @since	5.2
	 * @access	private
	 */
	private function setColumn($ids, $column, $state = true)
	{
		if (!$ids) {
			return false;
		}

		$db = EB::db();
		$tags = '';

		foreach ($ids as $id) {
			$tags .= $id;

			if (next($ids) !== false) {
				$tags .= ',';
			}
		}

		$state = (int) $state;

		$query = 'UPDATE ' . $db->quoteName('#__easyblog_tag');
		$query .= ' SET ' . $db->quoteName($column) . '=' . $db->Quote($state);

		$query .= ' WHERE ' . $db->quoteName('id') . ' IN (' . $tags . ')';

		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Method to unpublish tags
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function unpublishItems($ids)
	{
		$state = $this->setColumn($ids, 'published', false);

		foreach ($ids as $id) {
			$tag = EB::table('Tag');
			$tag->load($id);

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_TAG_UNPUBLISH', 'tag', array(
				'link' => 'index.php?option=com_easyblog&view=tags&layout=form&id=' . $tag->id,
				'tagTitle' => JText::_($tag->title)
			));
		}

		return $state;
	}

	/**
	 * Method to publish tags
	 *
	 * @since 5.2
	 * @access public
	 */
	public function publishItems($ids)
	{
		$state = $this->setColumn($ids, 'published', true);

		foreach ($ids as $id) {
			$tag = EB::table('Tag');
			$tag->load($id);

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_TAG_PUBLISH', 'tag', array(
				'link' => 'index.php?option=com_easyblog&view=tags&layout=form&id=' . $tag->id,
				'tagTitle' => JText::_($tag->title)
			));
		}

		return $state;
	}

	public function searchTag($title)
	{
		$db = EB::db();

		$query	= 'SELECT ' . $db->nameQuote('id') . ' '
				. 'FROM ' 	. $db->nameQuote('#__easyblog_tag') . ' '
				. 'WHERE ' 	. $db->nameQuote('title') . ' = ' . $db->quote($title) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result = $db->loadObject();

		return $result;
	}

	/**
	 * Get post count for tag
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function getUsedCount($tagId, $published = false)
	{
		$db = EB::db();

		$query = 'SELECT COUNT(a.tag_id) FROM `#__easyblog_post_tag` as a';
		$query .= ' INNER JOIN `#__easyblog_post` as b ON a.post_id = b.id';
		$query .= ' WHERE `tag_id` = ' . $db->Quote($tagId);

		if ($published) {
			$query .= ' AND a.`published` = ' . $db->Quote(1);
		}

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Allows caller to set the state of the default
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function setDefault($ids)
	{
		return $this->setColumn($ids, 'default', true);
	}

	/**
	 * Retrieves tags created by an author author
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getBloggerTags($authorId = null, $sort = 'asc', $search = '', $limit = null)
	{
		$authorId = JFactory::getUser($authorId)->id;
		$db = EB::db();

		$query = array();

		$queryCount = 'SELECT count(distinct a.`id`) FROM `#__easyblog_tag` AS a';
		$queryCount .= ' LEFT JOIN `#__easyblog_post_tag` AS b ON a.`id` = b.`tag_id`';
		$queryCount .= ' WHERE a.`created_by` = ' . $db->Quote($authorId);

		$query[] = 'SELECT SQL_CALC_FOUND_ROWS a.*, COUNT(b.' . $db->quoteName('id') . ') AS ' . $db->quoteName('post_count');
		$query[] = 'FROM ' . $db->quoteName('#__easyblog_tag') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->quoteName('#__easyblog_post_tag') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('tag_id');
		$query[] = 'WHERE a.' . $db->quoteName('created_by') . '=' . $db->Quote($authorId);

		if ($search) {
			$query[] = 'AND a.' . $db->quoteName('title') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$queryCount .= ' AND a.' . $db->quoteName('title') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
		}

		$query[] = 'GROUP BY a.' . $db->quoteName('id');

		if ($sort == 'post') {
			$query[] = 'ORDER BY COUNT(b.' . $db->quoteName('id') . ') DESC';
		}

		if ($sort == 'asc') {
			$query[] = 'ORDER BY a.' . $db->quoteName('title') . ' ASC';
		}

		if ($sort == 'desc') {
			$query[] = 'ORDER BY a.' . $db->quoteName('title') . ' DESC';
		}

		if ($sort == 'created') {
			$query[] = 'ORDER BY a.' . $db->quoteName('created') . 'DESC';
		}

		$limit = ($limit == 0) ? $this->getState('limit') : $limit;

		if ($limit) {
			$this->setState('limit', $limit);
		}

		$limitstart = $this->input->get('limitstart', 0, 'int');

		$limitSQL = 'LIMIT ' . $limitstart . ',' . $limit;

		$query[] = $limitSQL;

		$query = implode(' ', $query);

		// echo $query;exit;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		// Get total tags
		$db->setQuery($queryCount);
		$this->total = $db->loadResult();

		return $result;
	}

	/**
	 * Allows caller to remove default
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function removeDefault($ids)
	{
		return $this->setColumn($ids, 'default', false);
	}

	/**
	 * Method to publish or unpublish tags
	 *
	 * @access public
	 * @return array
	 */
	public function publish(&$tags, $publish = 1)
	{
		if (count($tags) > 0) {
			$db = EB::db();

			$tags = implode(',' , $tags);

			$query = 'UPDATE ' . $db->nameQuote('#__easyblog_tag') . ' '
					. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
					. 'WHERE ' . $db->nameQuote('id') . ' IN (' . $tags . ')';
			$db->setQuery($query);

			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Method to get total tags created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotalTags($userId = 0)
	{
		$db = EB::db();
		$where = array();

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_tag');

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$query = $query . $extra;

		$db->setQuery($query);

		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Displays a list of tags created by the author
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function _buildQueryByBlogger($bloggerId, $sort = 'title')
	{
		$db = EB::db();

		$query = 'select a.`id`, a.`title`, a.`alias`, a.`created`, count(b.`id`) as `post_count`, a.`published`';
		$query .=  ' from #__easyblog_tag as a';
		$query .=  '    left join #__easyblog_post_tag as b';
		$query .=  '    on a.`id` = b.`tag_id`';
		$query .=  ' where a.created_by = ' . $db->Quote($bloggerId);
		$query .=  ' group by (a.`id`)';

		if ($sort == 'post') {
			$query .=  ' order by count(b.`id`) desc';
		} else {
			$query .=  ' order by a.`title`';
		}

		return $query;
	}

	/**
	 * Retrieves the list of tags created by an author
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getTagsByBlogger($bloggerId, $usePagination = true, $sort = 'title')
	{
		$db = EB::db();

		$query = $this->_buildQueryByBlogger($bloggerId, $sort);

		$result = null;
		if ($usePagination) {
			$pagination = $this->getPaginationByBlogger($bloggerId, $sort);
			$result = $this->_getList($query, $pagination->limitstart, $pagination->limit);
		} else {
			$result = $this->_getList($query);
		}

		return $result;
	}

	public function getPaginationByBlogger($bloggerId, $sort = 'title')
	{
		jimport('joomla.html.pagination');
		$this->_pagination = EB::pagination($this->getTotalByBlogger($bloggerId , $sort), $this->getState('limitstart'), $this->getState('limit'));
		return $this->_pagination;
	}

	public function getTotalByBlogger($bloggerId, $sort = 'title')
	{
		// Lets load the content if it doesn't already exist
		$query = $this->_buildQueryByBlogger($bloggerId, $sort);
		$total = $this->_getListCount($query);

		return $total;
	}

	public function isExist($tagName, $excludeTagIds='0')
	{
		$db = EB::db();

		$query  = 'SELECT COUNT(1) FROM #__easyblog_tag';
		$query  .= ' WHERE `title` = ' . $db->Quote($tagName);
		if($excludeTagIds != '0')
			$query  .= ' AND `id` != ' . $db->Quote($excludeTagIds);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Generates a list of tags on the site
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	public function getTagCloud($limit='', $order='title', $sort='asc', $checkAccess = false, $search = '', $categoryBased = false, $usePagination = false)
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config = EB::config();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$isBloggerMode = EBR::isBloggerMode();
		$queryExclude = '';
		$excludeCats = array();

		$countQuery = 'select count(1) from `#__easyblog_tag` as a';

		$query = 'select SQL_CALC_FOUND_ROWS a.*, count(c.`id`) as `post_count`';
		$query .= ' from `#__easyblog_tag` as a';
		$query .= '    left join `#__easyblog_post_tag` as b';
		$query .= '    on a.`id` = b.`tag_id`';
		$query .= '    left join `#__easyblog_post` as c';
		$query .= '    on b.`post_id` = c.`id`';
		$query .= '    and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= '    and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		if ($checkAccess) {
			// if ($my->id == 0) {
			// 	$query .= '    and c.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			// }

			// Blog privacy setting
			// @integrations: jomsocial privacy
			$file = JPATH_ROOT . '/components/com_community/libraries/core.php';

			$easysocial = EB::easysocial();
			$jomsocial = EB::jomsocial();

			if ($config->get('integrations_es_privacy') && $easysocial->exists() && !FH::isSiteAdmin()) {
				$esPrivacyQuery = $easysocial->buildPrivacyQuery('c');
				$queryPrivacy = $esPrivacyQuery;

				$query .= $queryPrivacy;

			} else if ($config->get('main_jomsocial_privacy') && $jomsocial->exists() && !FH::isSiteAdmin()) {
				require_once($file);

				$my = JFactory::getUser();
				$jsFriends = CFactory::getModel('Friends');
				$friends = $jsFriends->getFriendIds($my->id);
				array_push($friends, $my->id);

				// Insert query here.
				$queryPrivacy = " AND (";
				$queryPrivacy .= " (c.`access`= 0) OR";
				$queryPrivacy .= " ((c.`access` = 20) AND (" . $db->Quote($my->id) . " > 0)) OR";

				if (empty($friends)) {
					$queryPrivacy .= " ((c.`access` = 30) AND (1 = 2)) OR";
				}
				else
				{
					$queryPrivacy .= " ((c.`access` = 30) AND (c." . $db->nameQuote('created_by') . " IN (" . implode(",", $friends) . "))) OR";
				}

				$queryPrivacy .= " ((c.`access` = 40) AND (c." . $db->nameQuote('created_by') ."=" . $my->id . "))";
				$queryPrivacy .= ")";

				$query .= $queryPrivacy;

			} else if ($this->my->id == 0) {

				$queryPrivacy = " AND c.`access` = " . $db->Quote(BLOG_PRIVACY_PUBLIC);
				$query .= $queryPrivacy;
			}

			// category access here
			$config = EB::config();
			if ($config->get('main_category_privacy')) {
				$catLib = EB::category();
				$catAccessSQL = $catLib->genAccessSQL('c.`id`');

				$query .= ' AND (' . $catAccessSQL . ')';
			}
		}

		if ($categoryBased) {
			$query .= '    and c.`category_id` = ' . $db->Quote($categoryBased);
		}

		if ($isBloggerMode !== false) {
			$query .= '    and c.`created_by` = ' . $db->Quote($isBloggerMode);
		}

		if (!$showBlockedUserPosts) {
			// exclude block users. #1978
			$query .= '    LEFT JOIN `#__users` AS uu ON c.`created_by` = uu.`id`';
		}

		$query .= ' where a.`published` = ' . $db->Quote('1');
		$countQuery .= " where a.`published` = " . $db->Quote('1');

		// Ensure that the count also respects the search
		if (!empty($search)) {
			$query .= ' AND a.`title` LIKE ' . $db->Quote('%' . $search . '%');
			$countQuery .= ' AND a.`title` LIKE ' . $db->Quote('%' . $search . '%');
		}

		// Get the current language
		$language = EB::getCurrentLanguage();

		// Filter by language
		if ($language) {
			$query .= ' AND (a.`language` =' . $db->Quote($language) . ' OR a.`language` =' . $db->Quote('*') . ' OR a.`language` =' . $db->Quote('') . ')';
			$countQuery .= ' AND (a.`language` =' . $db->Quote($language) . ' OR a.`language` =' . $db->Quote('*') . ' OR a.`language` =' . $db->Quote('') . ')';
		}

		if (!$showBlockedUserPosts) {
			// exclude block users. #1978
			$query .= ' AND (uu.`block` = 0 OR uu.`id` IS NULL)';
		}

		$query .= ' group by (a.`id`)';

		//order
		if ($order == 'created') {
			$query .= ' ORDER BY `created`';
		}

		if ($order == 'postcount') {
			$query .= ' order by `post_count`';
		}

		if ($order == 'title' || !$order) {
			$query .= ' order by a.`title`';
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


	public function getTags($count="")
	{
		$db = EB::db();

		$query = ' SELECT `id`, `title`, `alias` ';
		$query .= ' FROM #__easyblog_tag ';
		$query .= ' WHERE `published` = 1 ';
		$query .= ' ORDER BY `title`';

		if (!empty($count)) {
			$query .= ' LIMIT ' . $count;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Searches for a tags given a specific keyword
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function search($keyword, $wordSearch = true, $limit = null)
	{
		$db = EB::db();

		$query = array();

		$search = $wordSearch ? '%' . $keyword . '%' : $keyword . '%';

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_tag');
		$query[] = 'WHERE ' . $db->quoteName('title') . ' LIKE ' . $db->Quote($search);
		$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote(1);

		// Get the current language
		$language = EB::getCurrentLanguage();

		// Filter by language
		if ($language) {
			$query[] = 'AND (' . $db->quoteName('language') . ' = ' . $db->Quote($language) . ' OR ' . $db->quoteName('language') . ' = ' . $db->Quote('*') . ' OR ' . $db->quoteName('language') . ' = ' . $db->Quote('') . ')';
		}

		if ($limit) {
			$query[] = 'LIMIT ' . $limit;
		}

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}


	/**
	 * Searches for a tags given a specific keyword
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function suggest($keyword, $limit = null)
	{
		return $this->search($keyword, false, $limit);
	}

	/**
	 * *********************************************************************
	 * These part of codes will used in tag clod tags.
	 * *********************************************************************
	 */


	public function _buildQueryByTagBlogs()
	{
		$db = EB::db();

		$query =  'select count(a.`tag_id`) as `cnt`, b.*';
		$query .= ' from `#__easyblog_post_tag` as a';
		$query .= '   inner join `#__easyblog_post` as b on a.`post_id` = b.`id`';
		$query .= ' group by (a.`post_id`)';
		$query .= ' order by `cnt` desc';

		return $query;
	}


	public function getTagBlogs()
	{
		$db = EB::db();

		$query = $this->_buildQueryByTagBlogs();
		$pagination = $this->getPaginationByTagBlogs();

		$result = $this->_getList($query, $pagination->limitstart, $pagination->limit);

		return $result;
	}

	public function getPaginationByTagBlogs()
	{
		jimport('joomla.html.pagination');
		$this->_pagination = EB::pagination($this->getTotalByTagBlogs(), $this->getState('limitstart'), $this->getState('limit'));
		return $this->_pagination;
	}

	public function getTotalByTagBlogs()
	{
		// Lets load the content if it doesn't already exist
		$query = $this->_buildQueryByTagBlogs();
		$total = $this->_getListCount($query);

		return $total;
	}

	public function getTeamBlogCount($tagId)
	{
		$db = EB::db();
		$config = EB::config();

		$isBloggerMode = EBR::isBloggerMode();
		$extraQuery = '';

		$query = 'select count(1) from `#__easyblog_post` as a';
		$query .= '  inner join `#__easyblog_post_tag` as b';
		$query .= '    on a.`id` = b.`post_id`';

		$query .= ' where b.`tag_id` = ' . $db->Quote($tagId);
		$query .= '  and (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = '';
		if ($config->get('main_includeteamblogpost')) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a', array('concateOperator'=>'AND'));
		}

		$contributeSQL .= ')';
		$query .= $contributeSQL;

		if ($isBloggerMode !== false) {
			$query .= '  and a.`created_by` = ' . $db->Quote($isBloggerMode);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? '0' : $result;
	}

	/**
	 * Get a private post count
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function getTagPrivateBlogCount($tagId)
	{
		$db = EB::db();

		$isBloggerMode = EBR::isBloggerMode();

		$query = 'select count(1) from `#__easyblog_post` as a';
		$query .= '  inner join `#__easyblog_post_tag` as b';
		$query .= '    on a.`id` = b.`post_id`';
		$query .= '    and b.`tag_id` = ' . $db->Quote($tagId);
		$query .= '  where a.`access` = ' . $db->Quote(BLOG_PRIVACY_PRIVATE);
		$query .= '  and a.`published` = ' . $db->Quote(1);

		if ($isBloggerMode !== false) {
			$query .= '  and a.`created_by` = ' . $db->Quote($isBloggerMode);
		}

		// category access here
		$config = EB::config();
		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccessSQL = $catLib->genAccessSQL('a.`id`');
			$query .= ' AND (' . $catAccessSQL . ')';
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? '0' : $result;
	}

	/**
	 * Delete blog posts association with the tags
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function deleteAssociation($postId)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_post_tag') . ' '
				. 'WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Delete blog posts association with the tags
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function deleteAssociationByTagId($tagId)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_post_tag') . ' '
				. 'WHERE ' . $db->nameQuote('tag_id') . '=' . $db->Quote($tagId);
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Retrieve a list of tags on the site
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getDefaultTagsTitle()
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->quoteName('title') . ' FROM ' . $db->quoteName('#__easyblog_tag');
		$query .= ' WHERE ' . $db->quoteName('default') . '=' . $db->Quote(1);

		$db->setQuery($query);

		$result = $db->loadColumn();

		return $result;
	}

	/**
	 * Retrieve a default tags
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getDefaultTags()
	{
		$db = EB::db();

		$query = 'SELECT `id` FROM `#__easyblog_tag` '
				. 'WHERE `default` = 1';

		$db->setQuery($query);
		$tags = $db->loadResultArray();

		return $tags;
	}


	public function preloadByPosts(array $postIds)
	{
		$db = EB::db();

		$query = 'select a.*, b.`post_id` from `#__easyblog_tag` as a';
		$query .= ' inner join `#__easyblog_post_tag` as b on a.`id` = b.`tag_id`';
		$query .= ' where b.`post_id` IN (' . implode(',' , $postIds) . ')';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Delete tags from particular user
	 *
	 * @since	5.1.0
	 * @access	public
	 */
	public function deleteUserTags($userId)
	{
		$db = EB::db();

		// Select tags that belong to this user
		$query = array();
		$query[] = 'SELECT id FROM ' . $db->quoteName('#__easyblog_tag');
		$query[] = 'WHERE ' . $db->quoteName('created_by') . '=' . $db->Quote($userId);
		$query = implode(' ', $query);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$results) {
			return true;
		}

		$tags = array();

		foreach ($results as $tag) {
			$tags[] = $tag->id;
		}

		$tags = implode(',', $tags);

		// delete association for each tag
		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_post_tag');
		$query[] = 'WHERE ' . $db->quoteName('tag_id') . ' IN (' . $tags . ')';
		$query = implode(' ', $query);

		$db->setQuery($query);
		$db->Query();

		// Lastly we delete all the tags
		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_tag');
		$query[] = 'WHERE ' . $db->quoteName('id') . ' IN (' . $tags . ')';
		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}


	/**
	 * Method to retrive those tags which missing tag metadata.
	 *
	 * @since	6.0.11
	 * @access	public
	 */
	public function getMissingMetaTags()
	{
		$db = EB::db();

		$query = 'SELECT t.`id` FROM ' . $db->qn('#__easyblog_tag') . ' AS t';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT m.`id` FROM ' . $db->qn('#__easyblog_meta') . ' AS m';
		$query .= ' WHERE m.`content_id` = t.`id`';
		$query .= ' AND m.`type` = ' . $db->Quote('tag');
		$query .= ')';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
