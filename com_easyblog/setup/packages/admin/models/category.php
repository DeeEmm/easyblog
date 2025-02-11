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

class EasyBlogModelCategory extends EasyBlogAdminModel
{
	public $total = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.categories.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.categories.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Determines if an alias exists on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function aliasExists($alias, $excludeId = '')
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_category') . ' '
				. 'WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote($alias);

		if ($excludeId) {
			$query .= ' AND ' . $db->nameQuote('id') . '!=' . $db->Quote($excludeId);
		}

		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
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
		$state = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_state', 'filter_state');
		$this->setState('filter_state', $state);

		// Search
		$search = $this->app->getUserStateFromRequest('com_easyblog.categories.search', 'search');
		$this->setState('search', $search);

		// List state information.
		parent::populateState();
	}

	/**
	 * Retrieves the pagination used at the back end
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPagination()
	{
		if (empty($this->_pagination)) {
			$this->_pagination = EB::Pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Retrieves categories from the back end
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getData()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.*,';
		$query[] = '(SELECT COUNT(id) FROM ' . $db->qn('#__easyblog_category');
		$query[] = 'WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote(0) . ') AS depth';
		$query[] = 'FROM ' . $db->qn('#__easyblog_category') . ' AS a';

		// Clause
		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_state', 'filter_state', '', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.categories.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));

		$query[] = 'WHERE ' . $db->qn('lft') . '!=' . $db->Quote(0);

		if ($filter_state) {
			if ($filter_state == 'P') {
				$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote('1');
			}

			if ($filter_state == 'U') {
				$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$query[] = 'AND LOWER (' . $db->qn('title') . ') LIKE ' . $db->Quote('%' . $search . '%');
		}

		$ordering = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_order', 'filter_order', 'lft', 'cmd');
		$orderingDirection = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$query[] = 'ORDER BY ' . $ordering . ' ' . $orderingDirection . ', ' . $db->qn('ordering');

		$queryLimit = $query;

		// Remove the select columns from the query
		array_shift($queryLimit);
		array_shift($queryLimit);
		array_shift($queryLimit);

		$queryLimit = 'SELECT COUNT(1) ' . implode(' ', $queryLimit);

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
			$query[] = ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * Retrieves a list of parent categories on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getParentCategories($contentId, $type = 'all', $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array(), $debug = false)
	{
		// We'll let model categories process this.
		$model = EB::model("Categories");
		$categories = $model->getParentCategories($contentId, $type, $isPublishedOnly, $isFrontendWrite, $exclusion, $debug);

		return $categories;
	}

	/**
	 * Retrieves a list of categories from a specific parent category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getChildCategories($parentId, $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config = EB::config();

		$sortConfig = $config->get('layout_sorting_category','latest');

		$query = 'select a.`id`, a.`title`, a.`alias`, a.`private`';

		$query .= ", (select count(1) from `#__easyblog_post_category` as `pcat`";
		$query .= " INNER JOIN `#__easyblog_post` as p on pcat.`post_id` = p.`id` and p.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= " 	and p.`state` = " . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= " 		where pcat.`category_id` IN (select c.`id` from `#__easyblog_category` as c where (c.`id` = a.`id` OR c.`parent_id` = a.`id`))";
		$query .= ") as cnt";

		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  ' where a.parent_id = ' . $db->Quote($parentId);

		if ($isPublishedOnly) {
			$query	.=  ' and a.`published` = ' . $db->Quote('1');
		}

		if (FH::isMultiLingual() && !FH::isSiteAdmin() && $config->get('layout_composer_category_language', 0)) {
			$query .= EBR::getLanguageQuery('AND', 'a.language');
		}

		if ($isFrontendWrite) {
			$gid = EB::getUserGids();
			$gids = '';

			if (count($gid) > 0) {
				foreach ($gid as $id) {
					$gids .= (empty($gids)) ? $db->Quote($id) : ',' . $db->Quote($id);
				}
			}

			$query .= ' and a.id not in (';
			$query .= ' select id from `#__easyblog_category` as c';
			$query .= ' where not exists (';
			$query .= '		select b.category_id from `#__easyblog_category_acl` as b';
			$query .= '			where b.category_id = c.id and b.`acl_id` = '. $db->Quote(CATEGORY_ACL_ACTION_SELECT);
			$query .= '			and b.type = ' . $db->Quote('group');
			$query .= '			and b.content_id IN (' . $gids . ')';
			$query .= '     )';
			$query .= ' and c.`private` = ' . $db->Quote(CATEGORY_PRIVACY_ACL);
			$query .= ' and c.`parent_id` = ' . $db->Quote($parentId);
			$query .= ')';
		}

		// @task: Process exclusion list.
		if (!empty($exclusion)) {
			$excludeQuery = 'AND a.`id` NOT IN (';
			for ($i = 0; $i < count($exclusion); $i++) {
				$id = $exclusion[ $i ];

				$excludeQuery .= $db->Quote($id);

				if (next($exclusion) !== false) {
					$excludeQuery .= ',';
				}
			}

			$excludeQuery .= ')';

			$query .= $excludeQuery;
		}

		switch ($sortConfig)
		{
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query .= $orderBy;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to publish or unpublish categories
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function publish(&$categories = array(), $publish = 1)
	{
		if (count($categories) > 0) {
			$db = EB::db();

			$tags = implode(',' , $categories);

			$query = 'UPDATE ' . $db->nameQuote('#__easyblog_category') . ' '
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
	 * Returns the number of blog entries created within this category.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getUsedCount($categoryId , $published = false)
	{
		$db = EB::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_post') . ' as a '
				. ' inner join ' . $db->nameQuote('#__easyblog_post_category') . ' as b '
				. ' on a.`id` = b.`post_id`'
				. ' WHERE b.' . $db->nameQuote('category_id') . '=' . $db->Quote($categoryId);

		if ($published) {
			$query	.= ' AND a.' . $db->nameQuote('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		}

		$query .= ' AND a.' . $db->nameQuote('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$db->setQuery($query);

		$result	= $db->loadResult();

		return $result;
	}

	/**
	 * Returns the number of childs for a parent category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getChildCount($categoryId , $published = false)
	{
		$db = EB::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_category') . ' '
				. 'WHERE ' . $db->nameQuote('parent_id') . '=' . $db->Quote($categoryId);

		if ($published) {
			$query	.= ' AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);
		}

		$db->setQuery($query);

		$result	= $db->loadResult();

		return $result;
	}

	/**
	 * Returns all categories
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAllCategories($parentOnly = false)
	{
		$db = EB::db();

		$query = 'SELECT `id`, `title` FROM `#__easyblog_category`';

		if ($parentOnly) {
			$query .= ' WHERE `parent_id`=' . $db->Quote(0);
		}

		$query .= ' ORDER BY `title`';

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Delete existing group custom field mapping with the category id
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function deleteExistingFieldMapping($categoryId)
	{
		$db = EB::db();

		$query 	= 'DELETE FROM ' . $db->quoteName('#__easyblog_category_fields_groups') . ' WHERE ' . $db->quoteName('category_id') . '=' . $db->Quote($categoryId);
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Return category subscribers
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCategorySubscribers($categoryId)
	{
		$db = EB::db();

		$query  = "SELECT *, 'categorysubscription' as `type` FROM `#__easyblog_subscriptions`";
		$query .= " WHERE `uid` = " . $db->Quote($categoryId);
		$query .= " AND `utype` = " . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Ensure that there are no other categories that are default.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function resetDefault()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'UPDATE ' . $db->qn('#__easyblog_category');
		$query[] = 'SET ' . $db->qn('default') . '=' . $db->Quote(0);
		$query[] = 'WHERE ' . $db->qn('default') . '=' . $db->Quote(1);
		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Retrieves the default category from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getDefaultCategory()
	{
		$db = EB::db();

		$gid  = EB::getUserGids();
		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids   .= (empty($gids)) ? $db->Quote($id) : ',' . $db->Quote($id);
			}
		}

		$query	= 'SELECT a.*';
		$query	.= ' FROM `#__easyblog_category` AS a';
		$query	.= ' WHERE a.`published` = ' . $db->Quote('1');
		$query	.= ' AND a.`default` = ' . $db->Quote('1');
		$query	.= ' and a.id not in (';
		$query	.= ' 	select id from `#__easyblog_category` as c';
		$query	.= ' 	where not exists (';
		$query	.= '			select b.category_id from `#__easyblog_category_acl` as b';
		$query	.= '				where b.category_id = c.id and b.`acl_id` = '. $db->Quote(CATEGORY_ACL_ACTION_SELECT);
		$query	.= '				and b.type = ' . $db->Quote('group');
		$query	.= '				and b.content_id IN (' . $gids . ')';
		$query	.= '		)';
		$query	.= '	and c.`private` = ' . $db->Quote(CATEGORY_PRIVACY_ACL);
		$query	.= '	)';
		$query	.= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote('0') . ' AND e.`parent_id` = ' . $db->Quote('0') . ')';
		// no point to do a order by since the default category can have only one.
		$query	.= ' LIMIT 1';

		$db->setQuery($query);
		$result = $db->loadObject();

		if (!$result) {
			return false;
		}

		$category = EB::table('Category');
		$category->bind($result);

		return $category;
	}

	/**
	 * Retrieves the id of the default category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getDefaultCategoryId()
	{
		$category = $this->getDefaultCategory();

		if (!$category) {
			return 0;
		}

		return $category->id;
	}

	/**
	 * Return teamblog count that uses a category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTeamBlogCount($catId)
	{
		$db = EB::db();
		$isBloggerMode  = EBR::isBloggerMode();

		$query = 'select count(1) from `#__easyblog_post` as a';
		$query .= '  inner join `#__easyblog_post_category` as b';
		$query .= '    on a.`id` = b.`post_id`';

		$query .= ' where b.category_id = ' . $db->Quote($catId);
		$query .= '  and a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		if ($isBloggerMode !== false) {
			$query	.= '  and a.`created_by` = ' . $db->Quote($isBloggerMode);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? '0' : $result;
	}

	/**
	 * Retrieve the total number of posts in a category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalPostCount($ids, $options = array())
	{
		if (!$ids || empty($ids)) {
			return false;
		}

		$db	= EB::db();
		$config = EB::config();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		// Determines if this is currently on blogger mode
		$isBloggerMode = EBR::isBloggerMode();

		// Ensure that the id's is an array
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$bloggerId = isset($options['bloggerId']) && $options['bloggerId'] ? $options['bloggerId'] : '';

		// Since the ids passed in is always an array, we need to implode it
		$categoryId = implode(',', $ids);

		// Build the query to count the posts
		$query = 'SELECT COUNT(1) AS ' . $db->quoteName('cnt');

		$query .= ' FROM ' . $db->quoteName('#__easyblog_post_category') . ' AS ' . $db->quoteName('a');
		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_post') . ' AS ' . $db->quoteName('b');
		$query .= ' ON a.' . $db->quoteName('post_id') . ' = b.' . $db->quoteName('id');
		$query .= ' AND b.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);

		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_category') . ' AS ' . $db->quoteName('c');
		$query .= ' ON a.' . $db->quoteName('category_id') . ' = c.' . $db->quoteName('id');

		if (!$showBlockedUserPosts) {
			// exclude blocked users #1978
			$query .= ' INNER JOIN ' . $db->quoteName('#__users') . ' AS ' . $db->quoteName('uu');
			$query .= ' ON b.' . $db->quoteName('created_by') . ' = uu.' . $db->quoteName('id');
		}

		// If the user is a guest, ensure that we only fetch public posts
		if (!$bloggerId) {

			// if ($this->my->guest) {
			// 	$query .= ' AND b.' . $db->quoteName('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			// }


			// Blog privacy setting
			// @integrations: jomsocial privacy
			$file = JPATH_ROOT . '/components/com_community/libraries/core.php';

			$easysocial = EB::easysocial();
			$jomsocial = EB::jomsocial();

			if ($config->get('integrations_es_privacy') && $easysocial->exists() && !FH::isSiteAdmin()) {
				$esPrivacyQuery = $easysocial->buildPrivacyQuery('b');
				$queryPrivacy = $esPrivacyQuery;

				$query .= $queryPrivacy;

			} else if ($config->get('main_jomsocial_privacy') && $jomsocial->exists() && !FH::isSiteAdmin()) {
				require_once($file);

				$my = JFactory::getUser();
				$jsFriends = CFactory::getModel('Friends');
				$friends = $jsFriends->getFriendIds($my->id);
				array_push($friends, $my->id);

				// Insert query here.
				$queryPrivacy = ' AND (';
				$queryPrivacy .= ' (b.`access`= 0) OR';
				$queryPrivacy .= ' ((b.`access` = 20) AND (' . $db->Quote($my->id) . ' > 0)) OR';

				if (empty($friends)) {
					$queryPrivacy .= ' ((b.`access` = 30) AND (1 = 2)) OR';
				} else {
					$queryPrivacy .= ' ((b.`access` = 30) AND (b.' . $db->nameQuote('created_by') . ' IN (' . implode(',', $friends) . '))) OR';
				}

				$queryPrivacy .= ' ((b.`access` = 40) AND (b.' . $db->nameQuote('created_by') .'=' . $my->id . '))';
				$queryPrivacy .= ')';

				$query .= $queryPrivacy;

			} else if ($this->my->guest) {

				$queryPrivacy = ' AND b.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
				$query .= $queryPrivacy;
			}

		}

		// If this is on blogger mode, fetch items created by the current author only
		if ($isBloggerMode !== false) {
			$query .= ' AND b.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		} else if ($bloggerId) {
			$query .= ' AND b.' . $db->quoteName('created_by') . '=' . $db->Quote($bloggerId);
		} else {

			// Get the author id based on the category menu
			$authorId = EB::getCategoryMenuBloggerId();

			if ($authorId) {
				$query .= ' AND b.' . $db->quoteName('created_by') . '=' . $db->Quote($authorId);
			}
		}

		// We only want to retrieve the category provided and its child cats
		$query .= ' WHERE (c.' . $db->quoteName('id') . ' IN (' . $categoryId . ') or c.' . $db->quoteName('parent_id') . ' IN (' . $categoryId . '))';


		$query .= ' AND b.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		// If multi lingual is enabled, we should only fetch posts with the correct language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query	.= ' AND (';
			$query	.= ' b.`language`=' . $db->Quote($language);
			$query	.= ' OR b.`language`=' . $db->Quote('');
			$query	.= ' OR b.`language`=' . $db->Quote('*');
			$query	.= ')';
		}

		$db->setQuery($query);
		$result = $db->loadResultArray();

		if (!$result) {
			return 0;
		}

		return array_sum($result);
	}

	/**
	 * Methods for frontend.
	 */

	/**
	 * Retrieves a list of parent categories with posts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getParentCategoriesWithPost($accessible = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		// Build the initial query
		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_category');
		$query .= ' WHERE ' . $db->quoteName('published') . '=' . $db->Quote(1);
		$query .= ' AND ' . $db->quoteName('parent_id') . '=' . $db->Quote(0);

		// If caller provides us with specific category id's that are accessible by the user
		if (!empty($accessible)) {

			$tmp = '';

			foreach ($accessible as $category) {

				$tmp .= $db->Quote($category->id);

				if (next($accessible) !== false) {
					$tmp .= ',';
				}
			}

			$query .= ' AND ' . $db->quoteName('id') . ' IN(' . $tmp . ')';
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}

		$categories = array();

		foreach ($result as &$row) {

			$row->childs = null;

			// Build the childs for this category
			EB::buildNestedCategories($row->id, $row);

			// Emm... what is this?
			$catIds   = array();
			$catIds[] = $row->id;
			EB::accessNestedCategoriesId($row, $catIds);

			// Get the total number of posts for this category
			$row->cnt = $this->getTotalPostCount($catIds);

			if ($row->cnt > 0) {
				$categories[] = $row->id;
			}
		}

		return $categories;
	}

	/**
	 * Retrieves a list of parent categories with posts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function _getParentIdsWithPost($accessibleCatsIds = array())
	{
		$db	= EB::db();
		$my = JFactory::getUser();

		$query	= 'select * from `#__easyblog_category`';
		$query	.= ' where `published` = 1';
		$query	.= ' and `parent_id` = 0';

		if (!empty($accessibleCatsIds)) {
			$catAccessQuery	= ' `id` IN(';

			if (!is_array($accessibleCatsIds)) {
				$accessibleCatsIds	= array($accessibleCatsIds);
			}

			for ($i = 0; $i < count($accessibleCatsIds); $i++) {
				$catAccessQuery	.= $db->Quote($accessibleCatsIds[ $i ]->id);

				if (next($accessibleCatsIds) !== false) {
					$catAccessQuery	.= ',';
				}
			}
			$catAccessQuery .= ')';

			$query	.= ' and ' . $catAccessQuery;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$validCat = array();

		if (count($result) > 0) {
			for ($i = 0; $i < count($result); $i++) {
				$item =& $result[$i];

				$item->childs = null;
				EB::buildNestedCategories($item->id, $item);

				$catIds = array();
				$catIds[] = $item->id;
				EB::accessNestedCategoriesId($item, $catIds);

				$item->cnt = $this->getTotalPostCount($catIds);

				if ($item->cnt > 0) {
					$validCat[] = $item->id;
				}

			}
		}

		return $validCat;
	}


	/**
	 * Retrieves a list of categories
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCategories($sort = 'latest', $hideEmptyPost = true, $limit = 0 , $inclusion = array(), $pagination = true)
	{
		$db	= EB::db();
		$config = EB::config();

		//blog privacy setting
		$my = JFactory::getUser();

		// Determines if the current access is on blogger mode.
		$isBloggerMode = EBR::isBloggerMode();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$orderBy = '';
		$limitSQL = '';

		$limit = ($limit === 0) ? $this->getState('limit') : $limit;

		if ($limit == '-1' || $limit == '-2') {
			// Use eb list limit
			$limit = $config->get('layout_listlength', 10);
		}

		if ($limit <= '-3') {
			// Use joomla list limit
			$limit = EB::jconfig()->get('list_limit', 10);
		}

		// Reset the limit
		if ($limit !== 'all') {
			$this->setState('limit', $limit);
		}

		$limitstart = $this->input->get('limitstart', 0, 'int');

		if ($limit && $limit !== 'all') {
			$limitSQL = ' LIMIT ';

			if ($pagination) {
				$limitSQL .= $limitstart . ',';
			}

			$limitSQL .= $limit;
		}

		$onlyParent = true;
		$cond = array();

		// Respect inclusion categories
		if (!empty($inclusion)) {

			// we need to turn off the parent_id = 0 so that if categories in inclusion is a child, this query
			// will still work.
			$onlyParent = false;

			$inclusionQuery	= ' AND a.`id` IN(';

			if (!is_array($inclusion)) {
				$inclusion	= array($inclusion);
			}

			$inclusion	= array_values($inclusion);

			for ($i = 0; $i < count($inclusion); $i++) {
				$inclusionQuery	.= $inclusion[ $i ];

				if (next($inclusion) !== false) {
					$inclusionQuery	.= ',';
				}
			}
			$inclusionQuery	.= ')';

			$cond[] = $inclusionQuery;
		}

		// If the request is on blogger mode, only retrieve entries created by the specific author
		if ($isBloggerMode !== false) {
			$cond[] = ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		}

		// Get the current language
		$language = EB::getCurrentLanguage();
		if ($language) {
			$cond[] = ' AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}

		//sorting
		switch($sort)
		{
			case 'popular' :
				$orderBy	= ' ORDER BY `cnt` DESC';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$catAccess = '';
		if ($config->get('main_category_privacy')) {
			// sql for category access
			$catLib = EB::category();
			$catAccess = $catLib::genCatAccessSQL('a.`private`', 'a.`id`');
		}

		// conditions
		$condQuery = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		if ($onlyParent) {
			$condQuery .= ' and a.`parent_id` = 0';
		}

		if ($catAccess) {
			$condQuery .= ' and (';
			$condQuery .= $catAccess;
			$condQuery .= ')';
		}

		$gids = EB::getUserGids('', true);


		// joining the ANDs condition
		$tmp = implode(' ', $cond);
		$condQuery .= $tmp;

		$totalSQL = "";
		$mainSQL = "";

		// header for main query here.
		$headSQL = "select a.*, count(p.`id`) as `cnt`";
		$headSQL .= " from `#__easyblog_category` as a";
		$headSQL .= " 	inner join `#__easyblog_category` as c on a.`lft` <= c.`lft` and a.`rgt` >= c.`rgt`";
		$headSQL .= " 	left join `#__easyblog_post_category` as `pcat` on c.`id` = pcat.`category_id`";
		$headSQL .= " 	left join `#__easyblog_post` as p on pcat.`post_id` = p.`id` and p.`published` = '1' and p.`state` = '0'";

		// if a post has multiple categories and one of the category is not accessible, we should exclude this post. #790
		if ($config->get('main_category_privacy')) {

			$headSQL .= " and not exists (";
			$headSQL .= "    select acp2.post_id from `#__easyblog_post_category` as acp2";
			$headSQL .= "       inner join  `#__easyblog_category` as cat2 on acp2.`category_id` = cat2.`id`";
			$headSQL .= "		where acp2.`post_id` = pcat.`post_id`";
			$headSQL .= "		and (";
			$headSQL .= "				(cat2.`private` = 1 and (" . $this->my->id . " = 0)) OR ";
			$headSQL .= "				(cat2.`private` = 2 and (select count(1) from `#__easyblog_category_acl` as cacl2 where cacl2.`category_id` = cat2.`id` and cacl2.`acl_id` = " . CATEGORY_ACL_ACTION_VIEW . " and cacl2.`content_id` IN (" . $gids . ")) = 0)";
			$headSQL .= "			 )";
			$headSQL .= ")";
		}


		// Only retrieve the post if submit to the respected language
		if ($language) {
			$headSQL .= " AND (`p`." . $db->quoteName('language') . "=" . $db->Quote($language) . " OR `p`." . $db->quoteName('language') . "=" . $db->Quote('*') . " OR `p`." . $db->quoteName('language') . "=" . $db->Quote('') . ")";
		}

		if (!$showBlockedUserPosts) {
			// $headSQL .= " left join `#__users` as uu on p.`created_by` = uu.`id` and uu.`block` = 0";
			$headSQL .= " AND EXISTS (select uu.id from `#__users` as uu where uu.`id` = p.`created_by` and uu.`block` = 0)";
		}

		// $headSQL .= ")";

		$mainSQL = $headSQL . $condQuery;

		// do not show categories that has empty post.
		// we need to wrap the main sql so that we can filter by the post count.
		if ($hideEmptyPost) {
			$mainSQL .= " GROUP BY a.`id` having (count(p.`id`) > 0)";

		} else {
			$mainSQL .= " GROUP BY a.`id`";
		}

		// prepare count query
		$totalSQL = "select count(1) from (" . $mainSQL . ") as x";


		// main query execution.
		$mainSQL = $mainSQL . $orderBy . $limitSQL;

		// echo str_ireplace('#__', 'jos_', $mainSQL);

		$db->setQuery($mainSQL);
		$result	= $db->loadObjectList();

		if ($db->getErrorNum()) {
			throw EB::exception($db->getErrorMsg() . $db->stderr(), 500);
		}

		$cntQuery = $totalSQL;

		$db->setQuery($cntQuery);
		$this->_total = $db->loadResult();

		// Custom EB pagination style
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);
		}

		return $result;
	}

	/**
	 * Retrieves a list of categories
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCategories_old($sort = 'latest', $hideEmptyPost = true, $limit = 0 , $inclusion = array(), $pagination = true)
	{
		$db	= EB::db();
		$config = EB::config();

		//blog privacy setting
		$my = JFactory::getUser();

		// Determines if the current access is on blogger mode.
		$isBloggerMode = EBR::isBloggerMode();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$orderBy = '';
		$limitSQL = '';

		$limit	= ($limit == 0) ? $this->getState('limit') : $limit;

		// Reset the limit
		if ($limit) {
			$this->setState('limit', $limit);
		}

		$limitstart = $this->input->get('limitstart', 0, 'int');

		if ($pagination) {
			$limitSQL = ' LIMIT ' . $limitstart . ',' . $limit;
		}

		// If pagination is false, we'll need to respect the limit passed.
		// This is used by mod_eb_categories. #2649
		if (!$pagination && $limit) {
			$limitSQL = ' LIMIT ' . $limit;
		}

		$onlyParent = true;
		$cond = array();

		// Respect inclusion categories
		if (!empty($inclusion)) {

			// we need to turn off the parent_id = 0 so that if categories in inclusion is a child, this query
			// will still work.
			$onlyParent = false;

			$inclusionQuery	= ' AND a.`id` IN(';

			if (!is_array($inclusion)) {
				$inclusion	= array($inclusion);
			}

			$inclusion	= array_values($inclusion);

			for ($i = 0; $i < count($inclusion); $i++) {
				$inclusionQuery	.= $inclusion[ $i ];

				if (next($inclusion) !== false) {
					$inclusionQuery	.= ',';
				}
			}
			$inclusionQuery	.= ')';

			$cond[] = $inclusionQuery;
		}

		// If the request is on blogger mode, only retrieve entries created by the specific author
		if ($isBloggerMode !== false) {
			$cond[] = ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		}

		// Get the current language
		$language = EB::getCurrentLanguage();
		if ($language) {
			$cond[] = ' AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}

		//sorting
		switch($sort)
		{
			case 'popular' :
				$orderBy	= ' ORDER BY `cnt` DESC';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$catAccess = '';
		if ($config->get('main_category_privacy')) {
			// sql for category access
			$catLib = EB::category();
			$catAccess = $catLib::genCatAccessSQL('a.`private`', 'a.`id`');
		}

		// conditions
		$condQuery = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		if ($onlyParent) {
			$condQuery .= ' and a.`parent_id` = 0';
		}

		if ($catAccess) {
			$condQuery .= ' and (';
			$condQuery .= $catAccess;
			$condQuery .= ')';
		}

		// joining the ANDs condition
		$tmp = implode(' ', $cond);
		$condQuery .= $tmp;

		$totalSQL = "";
		$mainSQL = "";

		$gids = EB::getUserGids('', true);

		// header for main query here.
		$headSQL = "select SQL_CALC_FOUND_ROWS a.*, count(pcat.`id`) as `cnt`";
		$headSQL .= " from `#__easyblog_category` as a";
		$headSQL .= " 	inner join `#__easyblog_category` as c on a.`lft` <= c.`lft` and a.`rgt` >= c.`rgt`";
		$headSQL .= " 	left join `#__easyblog_post_category` as `pcat` on c.`id` = pcat.`category_id`";
		$headSQL .= " 					and exists (select p.`id` from `#__easyblog_post` as p";

		if (!$showBlockedUserPosts) {
			$headSQL .= " 									inner join `#__users` as uu on p.`created_by` = uu.`id` and uu.`block` = 0";
		}

		$headSQL .= " 									where pcat.`post_id` = p.`id` and p.`published` = '1' and p.`state` = '0'";

		// Only retrieve the post if submit to the respected language
		if ($language) {
			$headSQL .= " AND (`p`." . $db->quoteName('language') . "=" . $db->Quote($language) . " OR `p`." . $db->quoteName('language') . "=" . $db->Quote('*') . " OR `p`." . $db->quoteName('language') . "=" . $db->Quote('') . ")";
		}

		$headSQL .= ")";

		// if a post has multiple categories and one of the category is not accessible, we should exclude this post. #790
		if ($config->get('main_category_privacy')) {

			$headSQL .= " 					and not exists (";
			$headSQL .= "						select acp2.post_id from `#__easyblog_post_category` as acp2";
			$headSQL .= "							inner join  `#__easyblog_category` as cat2 on acp2.`category_id` = cat2.`id`";
			$headSQL .= "						where acp2.`post_id` = pcat.`post_id`";
			$headSQL .= "						and (";
			$headSQL .= "						(cat2.`private` = 1 and (" . $this->my->id . " = 0)) OR ";
			$headSQL .= "						(cat2.`private` = 2 and (select count(1) from `#__easyblog_category_acl` as cacl2 where cacl2.`category_id` = cat2.`id` and cacl2.`acl_id` = " . CATEGORY_ACL_ACTION_VIEW . " and cacl2.`content_id` IN ($gids)) = 0)";
			$headSQL .= "						)";
			$headSQL .= " 						)";
		}

		$mainSQL = $headSQL . $condQuery;

		// do not show categories that has empty post.
		// we need to wrap the main sql so that we can filter by the post count.
		if ($hideEmptyPost) {
			$mainSQL .= " GROUP BY a.`id` having (count(pcat.`id`) > 0)";

		} else {
			$mainSQL .= " GROUP BY a.`id`";
		}

		// prepare count query
		$totalSQL = "select count(1) from (" . $mainSQL . ") as x";


		// main query execution.
		$mainSQL = $mainSQL . $orderBy . $limitSQL;

		// echo str_ireplace('#__', 'jos_', $mainSQL);exit;

		$db->setQuery($mainSQL);
		$result	= $db->loadObjectList();

		if ($db->getErrorNum()) {
			throw EB::exception($db->getErrorMsg() . $db->stderr(), 500);
		}

		$cntQuery = 'select FOUND_ROWS()';

		if (EB::isFalangActivated()) {
			$totalSQL = str_replace('SQL_CALC_FOUND_ROWS', '', $totalSQL);
			$cntQuery = $totalSQL;
		}

		$db->setQuery($cntQuery);
		$this->_total = $db->loadResult();

		// Custom EB pagination style
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);
		}

		return $result;
	}

	/**
	 * Retrieve a list of blog posts from a specific list of categories
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function getPosts($categories, $limit = null, $includeAuthors = array(), $excludeAuthors = array(), $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config	= EB::config();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		// Determines if this is currently on blogger mode
		$isBloggerMode = EBR::isBloggerMode();

		$sort = isset($options['sort']) ? $options['sort'] : $config->get('category_ordering_direction', 'DESC');
		$ordering = isset($options['ordering']) ? $options['ordering'] : $config->get('category_ordering', 'created');

		$fieldsFilter = isset($options['fieldsFilter']) ? $options['fieldsFilter'] : null;
		$fieldsFilterRule = isset($options['fieldsFilterRule']) ? $options['fieldsFilterRule'] : 'include';
		$strictMode = isset($options['strictMode']) ? $options['strictMode'] : false;


		// Ordering column should be publish_up if the ordering is configured to be publishing date
		if ($ordering == 'published') {
			$ordering = 'publish_up';
		}

		// use in generating category access sql
		$catAccess = array();
		$catAccess['include'] = $categories;


		$isJSGrpPluginInstalled = false;
		$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled = JPluginHelper::isEnabled('system' , 'eventeasyblog');
		$isJSInstalled = false; // need to check if the site installed jomsocial.

		if (EB::jomsocial()->exists()) {
		  $isJSInstalled = true;
		}

		$includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ((a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';
		if ($config->get('main_includeteamblogpost')) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
		}
		if ($includeJSEvent) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'a');
		}
		if ($includeJSGrp) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'a');
		}

		// Test if easysocial exists on the site
		if (EB::easysocial()->exists()) {

			if (EB::easysocial()->isBlogAppInstalled('group')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('page')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('event')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
			}
		}

		$contributeSQL .= ')';

		$queryWhere = '';

		$query = array();

		$query[] = 'SELECT a.*, ifnull(f.`id`, 0) as `featured`';
		$query[] = ' FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = ' LEFT JOIN `#__easyblog_featured` as f ON a.`id` = f.`content_id` AND f.`type` = ' . $db->Quote('post');

		if (!$showBlockedUserPosts) {
			// exclude block users. #1978
			$query[] = ' INNER JOIN ' . $db->quoteName('#__users') . ' AS uu ON a.`created_by` = uu.`id` and uu.`block` = 0';
		}

		// // CUSTOM FIELDS FILTER DEBUG
		// $fieldsFilter = array(
		// 			'1' => array('3'),
		// 			'2' => array('2', '1')
		// 		);

		$fieldAclQ = '';

		// Custom Fields filter
		if ($fieldsFilter) {
			$fieldQueries = array();
			$filterCount = $strictMode ? 0 : count($fieldsFilter);

			$gid = EB::getUserGids();
			$gids = '';

			if (count($gid) > 0) {
				foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $id : ',' . $id;
				}
			}

			$fieldAclQ .= ' AND (';
			$fieldAclQ .= ' facl.' . $db->quoteName('acl_id') . ' IN(' . $gids . ')';
			$fieldAclQ .= ' AND facl.' . $db->quoteName('acl_type') . ' = ' . $db->Quote('read');
			$fieldAclQ .= ' OR facl.' . $db->quotename('id') . ' IS NULL';
			$fieldAclQ .= ' )';

			foreach ($fieldsFilter as $fieldId => $values) {

				if ($strictMode) {
					foreach ($values as $value) {
						$filterCount++;
						$fieldQ = 'select distinct `post_id` from `#__easyblog_fields_values` as fv';
						$fieldQ .= ' LEFT JOIN `#__easyblog_fields` as f ON fv.`field_id` = f.`id`';
						$fieldQ .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_fields_groups_acl') . ' AS facl';
						$fieldQ .= ' ON f.' . $db->quoteName('group_id') . ' = facl.' . $db->quoteName('group_id');
						$fieldQ .= ' WHERE `field_id` = ' . $db->Quote($fieldId) . ' AND `value` = ' . $db->Quote($value);

						// We need to check whether the user is belong to one of the group
						$fieldQ .= $fieldAclQ;

						$fieldQueries[] = $fieldQ;
					}
				} else {
					$fieldValueQuery = array();

					foreach ($values as $value) {
						$fieldValueQuery[] = '`value` = ' . $db->Quote($value);
					}

					$fieldValueQuery = (count($fieldValueQuery) ? implode(' OR ', $fieldValueQuery) : '');

					$fieldQ = 'select distinct `post_id` from `#__easyblog_fields_values` as fv';
					$fieldQ .= ' LEFT JOIN `#__easyblog_fields` as f ON fv.`field_id` = f.`id`';
					$fieldQ .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_fields_groups_acl') . ' AS facl';
					$fieldQ .= ' ON f.' . $db->quoteName('group_id') . ' = facl.' . $db->quoteName('group_id');
					$fieldQ .= ' WHERE `field_id` = ' . $db->Quote($fieldId) . ' AND (';
					$fieldQ .= $fieldValueQuery;
					$fieldQ .= ')';

					// We need to check whether the user is belong to one of the group
					$fieldQ .= $fieldAclQ;

					$fieldQueries[] = $fieldQ;
				}
			}

			$union = (count($fieldQueries) > 1) ? implode(') UNION ALL (', $fieldQueries) : $fieldQueries[0];
			$union = '(' . $union . ')';

			$filterCount = $filterCount - 1;

			// AND condition Or strictMode enabled.
			$fieldQuery = '(select * from (' . $union . ') as x group by x.`post_id` having (count(x.`post_id`) > ' . $filterCount . ')) as customFields';

			// if this is exclude mode, we use OR condition.
			// or strictMode turn off.
			// OR condition
			if (!$strictMode || $fieldsFilterRule != 'include') {
				$fieldQuery = '(select * from (' . $union . ') as x group by x.`post_id`) as customFields';
			}

			// To include or exclude the post
			if ($fieldsFilterRule == 'include') {
				$query[] = ' INNER JOIN ' . $fieldQuery . ' ON customFields.`post_id` = a.`id`';
			} else {
				$query[] = ' LEFT JOIN ' . $fieldQuery . ' ON customFields.`post_id` = a.`id`';
				$queryWhere .= ' AND customFields.`post_id` IS NULL';
			}

		}

		// Build the WHERE clauses
		$query[] = 'WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query[] = $queryWhere;

		// If this is on blogger mode, fetch items created by the current author only
		if ($isBloggerMode !== false) {
			$query[] = ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		} else {

			// Get the author id based on the category menu
			$authorId = EB::getCategoryMenuBloggerId();

			if ($authorId) {
				$query[] = ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($authorId);
			}
		}

		//sql for blog contribution
		$query[] = $contributeSQL;

		// sql for category access
		$catLib = EB::category();
		if ($config->get('main_category_privacy')) {
			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $catAccess);
			$query[] = 'AND (' . $catAccessSQL . ')';
		} else {
			$catAccessSQL = $catLib->genBasicSQL('a.`id`', $catAccess);
			if ($catAccessSQL) {
				$query[] = ' AND ' . $catAccessSQL;
			}
		}

		// If user is a guest, ensure that they can really view the blog post
		// if ($this->my->guest) {
		// 	$query[] = 'AND a.' . $db->quoteName('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		// }

		// Blog privacy setting
		// @integrations: jomsocial privacy
		$file = JPATH_ROOT . '/components/com_community/libraries/core.php';

		$easysocial = EB::easysocial();
		$jomsocial = EB::jomsocial();

		if ($config->get('integrations_es_privacy') && $easysocial->exists() && !FH::isSiteAdmin()) {
			$esPrivacyQuery = $easysocial->buildPrivacyQuery('a');
			$queryPrivacy = $esPrivacyQuery;

			$query[] = $queryPrivacy;

		} else if ($config->get('main_jomsocial_privacy') && $jomsocial->exists() && !FH::isSiteAdmin()) {
			require_once($file);

			$my = JFactory::getUser();
			$jsFriends = CFactory::getModel('Friends');
			$friends = $jsFriends->getFriendIds($my->id);
			array_push($friends, $my->id);

			// Insert query here.
			$queryPrivacy = ' AND (';
			$queryPrivacy .= ' (a.`access`= 0) OR';
			$queryPrivacy .= ' ((a.`access` = 20) AND (' . $db->Quote($my->id) . ' > 0)) OR';

			if (empty($friends)) {
				$queryPrivacy .= ' ((a.`access` = 30) AND (1 = 2)) OR';
			}
			else
			{
				$queryPrivacy .= ' ((a.`access` = 30) AND (a.' . $db->nameQuote('created_by') . ' IN (' . implode(',', $friends) . '))) OR';
			}

			$queryPrivacy .= ' ((a.`access` = 40) AND (a.' . $db->nameQuote('created_by') .'=' . $my->id . '))';
			$queryPrivacy .= ')';

			$query[] = $queryPrivacy;

		} else if ($this->my->id == 0) {

			$queryPrivacy = ' AND a.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			$query[] = $queryPrivacy;
		}




		// Ensure that the blog posts is available site wide
		// $query[] = 'AND a.' . $db->quoteName('source_id') . '=' . $db->Quote('0');

		// Filter by language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query[] = 'AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}


		// Explicitly include authors
		if ($includeAuthors) {
			$query[] = 'AND a.`created_by` IN (';

			for ($i = 0; $i < count($includeAuthors); $i++) {

				$query[] = $db->Quote($includeAuthors[$i]);

				if (next($includeAuthors) !== false) {
					$query[] = ',';
				}
			}

			$query[] = ')';
		}

		// Explicitly exclude authors
		if (!empty($excludeAuthors)) {
			$query[] = 'AND a.`created_by` NOT IN (';

			for ($i = 0; $i < count($excludeAuthors); $i++) {

				$query[] = $db->Quote($excludeAuthors[$i]);

				if (next($excludeAuthors) !== false) {
					$query[] = ',';
				}
			}

			$query[] = ')';
		}

		$queryCount = implode(' ', $query);

		// Order the posts
		$query[] = 'ORDER BY a.' . $db->quoteName($ordering) . ' ' . $sort;

		// Set the pagination
		if (!is_null($limit)) {

			// Glue back the sql queries into a single string.
			$queryCount = str_ireplace('SELECT a.*, ifnull(f.`id`, 0) as `featured`', 'SELECT COUNT(1)', $queryCount);

			$db->setQuery($queryCount);
			$count = $db->loadResult();

			$limit = ($limit == 0) ? $this->getState('limit') : $limit;
			$limitstart = $this->input->get('limitstart', 0, 'int');

			// Set the limit
			$query[] = 'LIMIT ' . $limitstart . ',' . $limit;

			$this->_pagination = EB::pagination($count, $limitstart, $limit);
		}

		// Glue back the sql queries into a single string.
		$query = implode(' ', $query);

		// // Debug
		// echo str_ireplace('#__', 'jos_', $query);exit;

		$db->setQuery($query);

		if ($db->getErrorNum() > 0) {
			throw EB::exception($db->getErrorMsg() . $db->stderr(), $db->getErrorNum());
		}

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves a list of active authors for a category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getActiveAuthors($categoryId)
	{
		$config = EB::config();
		$db = EB::db();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = array();

		$query[] = 'SELECT DISTINCT(a.' . $db->quoteName('created_by') . ') FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');

		if (!$showBlockedUserPosts) {
			$query[] = 'INNER JOIN ' . $db->quoteName('#__users') . ' AS uu ON a.`created_by` = uu.`id` and uu.`block` = 0';
		}

		$query[] = 'where b.' . $db->quoteName('category_id') . ' = ' . $db->Quote($categoryId);

		// Glue back the queries into a single string
		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadColumn();

		if (!$result) {
			return $result;
		}

		$authors = array();

		// preload users.
		EB::user($result);

		foreach ($result as $id) {
			$author = EB::user($id);
			$authors[] = $author;
		}

		return $authors;
	}

	/**
	 * Retrieves the count of active authors for a category
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getActiveAuthorsCount($categoryId)
	{
		$config = EB::config();
		$db = EB::db();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = array();

		$query[] = 'SELECT count(DISTINCT(a.' . $db->quoteName('created_by') . ')) FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');

		if (!$showBlockedUserPosts) {
			$query[] = 'INNER JOIN ' . $db->quoteName('#__users') . ' AS uu ON a.`created_by` = uu.`id` and uu.`block` = 0';
		}

		$query[] = 'where b.' . $db->quoteName('category_id') . ' = ' . $db->Quote($categoryId);

		// Glue back the queries into a single string
		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}


	/**
	 * Method to get total category created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotalCategory($userId = 0)
	{
		$db		= EB::db();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_category');

		if ($userId) {
			$where[]  = '`created_by` = ' . $db->Quote($userId);
		}


		$extra = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$query = $query . $extra;

		$db->setQuery($query);

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Determine if the category exists on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isExist($categoryName, $excludeCatIds='0')
	{
		$db = EB::db();

		$query  = 'SELECT COUNT(1) FROM #__easyblog_category';
		$query  .= ' WHERE `title` = ' . $db->Quote($categoryName);
		if ($excludeCatIds != '0') {
			$query  .= ' AND `id` != ' . $db->Quote($excludeCatIds);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Duplicates custom field groups for a category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function duplicateFieldGroups(EasyBlogTableCategory $originalCategory, EasyBlogTableCategory $newCategory)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_category_fields_groups');
		$query[] = 'WHERE ' . $db->qn('category_id') . '=' . $db->Quote($originalCategory->id);
		$query = implode(' ', $query);

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (!$rows) {
			return false;
		}

		foreach ($rows as $row) {
			$group = EB::table('CategoryFieldGroup');
			$group->bind($row);

			$group->id = null;
			$group->category_id = $newCategory->id;
			$group->store();
		}

		return true;
	}

	/**
	 * Duplicates acl items for a category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function duplicateAcl(EasyBlogTableCategory $originalCategory, EasyBlogTableCategory $newCategory)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_category_acl');
		$query[] = 'WHERE ' . $db->qn('category_id') . '=' . $db->Quote($originalCategory->id);
		$query = implode(' ', $query);

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (!$rows) {
			return false;
		}

		foreach ($rows as $row) {
			$acl = EB::table('CategoryAcl');
			$acl->bind($row);

			$acl->id = null;
			$acl->category_id = $newCategory->id;
			$acl->store();
		}

		return true;
	}

	/**
	 * Check if user subscribed to category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isCategorySubscribedUser($categoryId, $userId, $email)
	{
		$db	= EB::db();

		$query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query  .= ' WHERE `uid` = ' . $db->Quote($categoryId);
		$query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);

		$query  .= ' AND (`user_id` = ' . $db->Quote($userId);
		$query  .= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Check if user subscribed to category based on email
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isCategorySubscribedEmail($categoryId, $email)
	{
		$db	= EB::db();

		// lets check if this item already cached or not
		if (EB::cache()->exists($categoryId, 'cats')) {
			$data = EB::cache()->get($categoryId, 'cats');

			if (isset($data['subs'])) {
				return true;
			} else {
				return false;
			}
		}

		// PHP 8.1 compatibility
		if (is_null($email)) {
			$email = '';
		}

		$query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query  .= ' WHERE `uid` = ' . $db->Quote($categoryId);
		$query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);

		$query  .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Adding user into category subscription
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function addCategorySubscription($categoryId, $email, $userId = '0', $fullname = '')
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$date = EB::date();
			$subscriber = EB::table('Subscriptions');

			$subscriber->utype = EBLOG_SUBSCRIPTION_CATEGORY;
			$subscriber->uid = $categoryId;

			$subscriber->email = $email;
			if ($userId != '0') {
				$subscriber->user_id = $userId;
			}

			$subscriber->fullname = $fullname;
			$subscriber->created = $date->toSql();
			$state = $subscriber->store();

			if ($state) {
				$category = EB::table('Category');
				$category->load($categoryId);

				// lets send confirmation email to subscriber.
				$helper = EB::subscription();
				$template = $helper->getTemplate();

				$template->uid = $subscriber->id;
				$template->utype = 'categorysubscription';
				$template->user_id = $subscriber->user_id;
				$template->uemail = $email;
				$template->ufullname = $fullname;
				$template->ucreated = $subscriber->created;
				$template->targetname = $category->title;
				$template->targetlink = EBR::getRoutedURL('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $categoryId, false, true);

				$helper->addMailQueue($template);
			}

			return $state;
		}
	}

	/**
	 * Update user into category subscription based on email
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function updateCategorySubscriptionEmail($sid, $userid, $email)
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {

			$subscriber = EB::table('Subscriptions');
			$subscriber->load($sid);
			$subscriber->user_id  = $userid;
			$subscriber->email    = $email;
			$subscriber->store();
		}
	}

	/**
	 * Check if current logged in user group allow in category ACL.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function allowAclCategory($catId = 0)
	{
		$db = EB::db();

		$gid = EB::getUserGids();
		$gids = '';

		if (count($gid) > 0) {
			$temp = array();
			foreach ($gid as $id) {
				$temp[] = $db->quote($id);
			}

			$gids = implode(',', $temp);
		}

		$query  = 'SELECT COUNT(1) FROM `#__easyblog_category_acl`';
		$query .= ' WHERE `acl_id` = ' . $db->quote('1');
		$query .= ' AND `status` = ' . $db->quote('1');
		$query .= ' AND `category_id` = ' . $db->quote($catId);
		if ($gids) {
			$query .= ' AND `content_id` IN (' . $gids . ')';
		}

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Preload categories used by posts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function preloadByPosts(array $postIds, $options = array())
	{
		$db = EB::db();

		$ordering = EB::normalize($options, 'ordering', true);

		$query = 'select a.*, b.`post_id`, b.`primary` from `#__easyblog_category` as a';
		$query .= ' inner join `#__easyblog_post_category` as b on a.`id` = b.`category_id`';
		$query .= ' where b.`post_id` IN (' . implode(',' , $postIds) . ')';

		if ($ordering) {
			$query .= ' order by a.`lft` asc';
		}

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Return categories hierachy and sort by ordering column.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function generateCategoryFilterList()
	{
		$db = EB::db();

		$query = "select a.*, (SELECT COUNT(id) FROM `#__easyblog_category` WHERE `lft` < a.`lft` AND `rgt` > a.`rgt`) AS depth";
		$query .= " from `#__easyblog_category` as a";
		$query .= " order by a.`lft`, a.`ordering`";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Retrieves all the child parent categories
	 *
	 * @since	6.0.5
	 * @access	public
	 */
	public function getChildParentCategories($parentId, $categories = [])
	{
		if (!$parentId) {
			return $categories;
		}

		$db = EB::db();

		$query = [];
		$query[] = 'SELECT `id`, `title`, `parent_id` FROM ' . $db->qn('#__easyblog_category');
		$query[] = 'WHERE ' . $db->qn('id') . '=' . $db->Quote($parentId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $categories;
		}

		$categories[] = $result[0];

		return $this->getChildParentCategories($result[0]->parent_id, $categories);
	}
}
