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

class EasyBlogModelBlog extends EasyBlogAdminModel
{
	public $_total = null;
	public $_pagination = null;

	public function __construct()
	{
		parent::__construct();

		
		$jConfig = EB::jConfig();

		$limit = $jConfig->get('list_limit') == 0 ? 5 : $jConfig->get('list_limit');
		$limitstart = $this->input->get('limitstart', 0, 'int');


		if ($limit != 0) {
			$limitstart = (int) floor(($limitstart / $limit) * $limit);
		} else {
			$limitstart = 0;
		}

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Determines if the blog post is featured
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isFeatured($id)
	{
		static $_cache = array();
		$db = EB::db();

		if (!isset($_cache[$id])) {
			$query = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_featured');
			$query[] = 'WHERE ' . $db->quoteName('content_id') . '=' . $db->Quote($id);
			$query[] = 'AND ' . $db->quoteName('type') . '=' . $db->Quote('post');

			$query = implode(' ', $query);

			$db->setQuery($query);
			$count = $db->loadResult();

			$_cache[$id] = $count;
		}

		return $_cache[$id] > 0;
	}

	/**
	 * Computes the total number of hits for blog posts created by any specific user throughout the site.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTotalHits($userId)
	{
		$db = EB::db();

		$query = 'SELECT SUM(`hits`) FROM ' . $db->nameQuote('#__easyblog_post') . ' '
				. 'WHERE ' . $db->nameQuote('created_by') . '=' . $db->Quote($userId);
		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves a list of comments from a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getBlogComment($id, $limitFrontEnd = 0, $sort = 'asc', $lite = false, $options = array())
	{
		$config = EB::config();
		$limit = (int) $config->get('comment_pagination');

		$limitstart = $this->input->get('limitstart', 0, 'int');

		if ($limit != 0) {
			$limitstart = (int) floor(($limitstart / $limit) * $limit);
		} else {
			$limitstart = 0;
		}

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$db = EB::db();
		$config = EB::getConfig();
		$sort = $config->get('comment_sort','asc');

		if ($lite) {
			$query = 'SELECT a.* FROM `#__easyblog_comment` a';
			$query .= ' LEFT JOIN #__users AS c ON a.`created_by` = c.`id`';
			$query .= ' WHERE a.`post_id` = '.$db->Quote($id);
			$query .= ' AND a.`published` = 1';

			// exclude block users. #1978
			$query .= ' AND (c.`block` = 0 OR c.`id` IS NULL)';
		} else {
			$countSyntax = 'count(b.id) - 1';

			// If just get replies of a comment, do not need to minus 1
			if (isset($options['replies']) && $options['replies']) {
				$countSyntax = 'count(b.id)';
			}

			$query = 'SELECT a.*, (' . $countSyntax . ') AS `depth`,';
			$query .= ' (select count(1) from `#__easyblog_comment` as cc
							where  cc.`post_id` = a.`post_id` and cc.`published` = 1 and cc.`lft` > a.`lft` and cc.`rgt` < a.`rgt`) as `childs`';
			$query .= ' FROM `#__easyblog_comment` AS a';
			$query .= ' INNER JOIN `#__easyblog_comment` AS b';

			if (isset($options['replies']) && $options['replies']) {
				$query .= ' ON b.`lft` > ' . $options['lft'] . ' AND b.`rgt` < ' . $options['rgt'] ;
			}

			$query .= ' LEFT JOIN `#__users` AS c ON a.`created_by` = c.`id`';
			$query .= ' WHERE a.`post_id` = '.$db->Quote($id);
			$query .= ' AND b.`post_id` = '.$db->Quote($id);
			$query .= ' AND a.`published` = 1';
			$query .= ' AND b.`published` = 1';
			$query .= ' AND a.`lft` BETWEEN b.`lft` AND b.`rgt`';

			// exclude block users. #1978
			$query .= ' AND (c.`block` = 0 OR c.`id` IS NULL)';

			if (isset($options['state']) && $options['state']) {
				$state = $options['state'] == 'parentOnly' ? '= 0' : '!= 0';
				$query .= ' AND a.`parent_id` ' . $state;
			}

			$query .= ' GROUP BY a.`id`';
		}

		// prepare the query to get total comment
		$queryTotal = 'SELECT COUNT(1) FROM (';
		$queryTotal .= $query;
		$queryTotal .= ') AS x';

		// continue the query.
		$limit = $this->getState('limit');
		$limitstart = $this->getState('limitstart');

		switch ($sort) {
			case 'desc':
				$query .= ' ORDER BY a.`rgt` desc';
			break;
			default:
				$query .= ' ORDER BY a.`lft` asc';
			break;
		}

		if ($limitFrontEnd > 0) {
			$query  .= ' LIMIT ' . $limitFrontEnd;
		} else {
			$query  .= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		if ($limitFrontEnd <= 0) {
			$db->setQuery($queryTotal);
			$this->_total = $db->loadResult();

			jimport('joomla.html.pagination');

			$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);
		}
		// var_dump($query);exit;

		// the actual content sql
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		// Format the comments
		$result = EB::comment()->format($result);

		return $result;
	}

	/**
	 * Generates the navigation data of a blog post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getBlogNavigation(EasyBlogPost $post, $exclusion = [])
	{
		$excludeIds = null;

		// Default to exclude the current post that we are trying to get the navigation for
		if (is_null($exclusion)) {
			$excludeIds = [$post->id];
		}

		if (!is_null($exclusion) && is_array($exclusion)) {
			$excludeIds = array_merge($exclusion, [$post->id]);
		}

		// Get the menu params associated with this post
		$params = $post->getMenuParams();

		$navigationType = $params->get('post_navigation_type', 'site');
		$paginationType = $params->get('pagination_style', 'normal');

		$model = EB::model('Blog');
		$navigation = $model->getPostNavigation($post, $navigationType, $excludeIds);

		$posts = [];

		if ($navigation->prev) {
			$posts[] = EB::post($navigation->prev->id);
		}

		if ($navigation->next) {
			$posts[] = EB::post($navigation->next->id);
		}

		if ($posts) {
			EB::cache()->insert($posts);
		}

		if ($navigation->prev) {
			$navigation->prev->link = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $navigation->prev->id);
			$navigation->prev->title = EBString::strlen($navigation->prev->title) > 50 ? EBString::substr($navigation->prev->title, 0, 50) . JText::_('COM_EASYBLOG_ELLIPSES') : $navigation->prev->title;
		}

		if ($paginationType == 'autoload' && $navigationType == 'random') {
			$navigation->next = false;
		}

		if ($navigation->next) {
			$nextPost = EB::post($navigation->next->id);

			$navigation->next->link = $nextPost->getPermalink();
			$navigation->next->title = EBString::strlen($navigation->next->title) > 50 ? EBString::substr($navigation->next->title, 0, 50) . JText::_('COM_EASYBLOG_ELLIPSES') : $navigation->next->title;
		}

		return $navigation;
	}

	/**
	 * Retrieves a list of featured posts from the site
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function getFeaturedBlog($categories = array(), $limit = null)
	{
		$config = EB::config();
		$my = JFactory::getUser();
		$db = EB::db();
		$max = is_null($limit) ? EBLOG_MAX_FEATURED_POST : $limit;

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$isJSGrpPluginInstalled = false;
		$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled = JPluginHelper::isEnabled('system', 'eventeasyblog');
		$isJSInstalled = false; // need to check if the site installed jomsocial.

		$file = JPATH_ROOT . '/components/com_community/libraries/core.php';
		$exists = JFile::exists($file);

		if ($exists) {
			$isJSInstalled = true;
		}

		$includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;

		$jsEventPostIds = '';
		$jsGrpPostIds = '';

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

		// Only process the contribution sql for EasySocial if EasySocial really exists.
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


		// Determine if this is blogger mode
		$isBloggerMode = EBR::isBloggerMode();

		$query = array();

		$query[] = 'SELECT a.*, 1 as `featured` FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_featured') . ' AS c';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = c.' . $db->quoteName('content_id');
		$query[] = 'AND c.' . $db->quoteName('type') . '=' . $db->Quote('post');

		if (!$showBlockedUserPosts) {
			// exclude block users. #1978
			$query[] = 'INNER JOIN ' . $db->quoteName('#__users') . ' AS uu ON a.`created_by` = uu.`id` and uu.`block` = 0';
		}

		$query[] = 'WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		// If this is blogger mode, we need to filter by author
		if ($isBloggerMode !== false) {
			$query[] = 'AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($isBloggerMode);
		}

		$query[] = $contributeSQL;

		// When language filter is enabled, we need to detect the appropriate contents
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query[] = 'AND(';
			$query[] = 'a.' . $db->quoteName('language') . '=' . $db->Quote($language);
			$query[] = 'OR';
			$query[] = 'a.' . $db->quoteName('language') . '=' . $db->Quote('');
			$query[] = 'OR';
			$query[] = 'a.' . $db->quoteName('language') . '=' . $db->Quote('*');
			$query[] = ')';
		}

		// Explicitly include posts only from these categories
		if (!empty($categories)) {

			// To support both comma separated categories an array of categories
			if (!is_array($categories)) {
				$categories = explode(',', $categories);
			}
		}

		// Privacy for blog
		if ($my->guest) {
			$query[] = 'AND a.' . $db->quoteName('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}


		// category access
		// sql for category access
		$catLib = EB::category();

		$options = array();

		if ($categories) {
			$options['include'] = $categories;
		}

		if ($config->get('main_category_privacy')) {
			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $options);
			$query[] = 'AND (' . $catAccessSQL . ')';
		} else {
			$catAccessSQL = $catLib->genBasicSQL('a.`id`', $options);
			if ($catAccessSQL) {
				$query[] = ' AND ' . $catAccessSQL;
			}
		}

		// Ordering
		$query[] = 'ORDER BY a.' . $db->quoteName('created') . ' DESC';

		if ($max > 0) {
			$query[] = 'LIMIT ' . $max;
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Checks if
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function aliasExists($permalink, $id)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('permalink') . '=' . $db->Quote($permalink);

		if ($id != 0) {
			$query[] = 'AND ' . $db->quoteName('id') . '!=' . $db->Quote($id);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Retrieves the total number of blog posts pending moderation
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalModeration()
	{
		$db = EB::db();

		$query = 'select count(1) from ' . $db->qn('#__easyblog_revisions') . ' as a';
		$query .= ' inner join ' . $db->qn('#__easyblog_post') . ' as b on a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');
		$query .= ' where a.' . $db->qn('state') . ' = ' . $db->Quote(EASYBLOG_REVISION_PENDING);

		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Get array of blogs defined by parameters
	 *
	 * @param	$type			str
	 * @param	$typeId			int
	 * @param	$sort			str
	 * @param	$max			int
	 * @param	$published		str
	 * @param	$search			bool
	 * @param	$frontpage		bool
	 * @param	$excludeBlogs	array
	 * @param	$pending		bool
	 * @param	$dashboard		bool
	 * @param	$protected		bool
	 * @param	$excludeCats	array
	 * @param	$includeCats	array
	 *
	*/
	public function getBlogsBy($type,
								$typeId = 0,
								$sort = '',
								$max = 0,
								$published = EBLOG_FILTER_PUBLISHED,
								$search = false,
								$frontpage = false,
								$excludeBlogs = array(),
								$pending = false,
								$dashboard = false,
								$protected = true,
								$excludeCats = array(),
								$includeCats = array(),
								$postType = null,
								$limitType = 'listlength',
								$pinFeatured = true,
								$includeAuthors = array(),
								$excludeAuthors = array(),
								$excludeFeatured = false,
								$includeTags = array(),
								$options = array())
	{

		$db = EB::db();
		$my = JFactory::getUser();

		$config = EB::config();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		// used in imagewall module
		$hasImageOnly = false;

		// override the type
		if ($type == 'image' || $type == 'categoryimage') {
			$hasImageOnly = true;
			$type = ($type == 'categoryimage') ? 'category' : '';
		}

		// flag to determine if query should use select count on paginatin or not.
		// #1557
		$useSelectCount = true;

		$queryPagination = true;
		$queryWhere = '';
		$queryOrder = '';
		$queryLimit = '';
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$queryExcludePrivateJSGrp = '';

		// Normalize options
		$fieldsFilter = isset($options['fieldsFilter']) ? $options['fieldsFilter'] : null;
		$strictMode = isset($options['strictMode']) ? $options['strictMode'] : false;
		$fieldsFilterRule = isset($options['fieldsFilterRule']) ? $options['fieldsFilterRule'] : 'include';
		$paginationType = isset($options['paginationType']) ? $options['paginationType'] : 'normal';

		// if pagination == none, means the caller do not want to query to follow
		// pagination limit start.
		if ($paginationType == 'none') {
			$queryPagination = false;
		}


		// if exclude featured post, then we should not pin the featured posts as it no longer make sense. #2999
		if ($excludeFeatured) {
			$pinFeatured = false;
		}

		// use in generating category access sql
		$catAccess = array();

		// Get excluded categories
		$excludeCats = !empty($excludeCats) ? $excludeCats : array();

		// Determines if the user is viewing a blogger mode menu item
		$isBloggerMode = EBR::isBloggerMode();

		// What is this for?
		$teamBlogIds = '';

		// What?
		$customOrdering = '';

		if (!empty($sort) && is_array($sort)) {
			$customOrdering = isset($sort[1]) ? $sort[1] : '';
			$sort = isset($sort[0]) ? $sort[0] : '';
		}

		// Sorting options
		$sort = empty($sort) ? $config->get('layout_postorder', 'latest') : $sort;

		$isJSGrpPluginInstalled = false;
		$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled = JPluginHelper::isEnabled('system', 'eventeasyblog');
		$isJSInstalled = false; // need to check if the site installed jomsocial.

		$file = JPATH_ROOT . '/components/com_community/libraries/core.php';
		$exists = JFile::exists($file);

		if ($exists) {
			$isJSInstalled = true;
		}

		$includeJSGrp = ($type != 'teamblog' && !$dashboard && $isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent = ($type != 'teamblog' && !$dashboard && $isEventPluginInstalled && $isJSInstalled) ? true : false;

		$jsEventPostIds = '';
		$jsGrpPostIds = '';

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ((a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($config->get('main_includeteamblogpost') || $type == 'teamblog' || $dashboard) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
		}

		if ($includeJSEvent) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'a');
		}

		if ($includeJSGrp) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'a');
		}

		if ($includeJSGrp || $includeJSGrp) {
			$useSelectCount = false;
		}

		// Only process the contribution sql for EasySocial if EasySocial really exists.
		if ($type != 'teamblog' && !$dashboard && EB::easysocial()->exists()) {

			if (EB::easysocial()->isBlogAppInstalled('group')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('page')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('event')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('group') || EB::easysocial()->isBlogAppInstalled('page') || EB::easysocial()->isBlogAppInstalled('event')) {
				$useSelectCount = false;
			}
		}

		$contributeSQL .= ')';

		//get teamblogs id.
		$query = '';

		//check if the request come with statastic or not.
		$statType = $this->input->getString('stat','');
		$statId = '';

		if (!empty($statType)) {
			$statId = ($statType == 'tag') ? $this->input->getString('tagid','') : $this->input->getString('catid','');
		}

		if (!empty($excludeBlogs)) {

			$queryExclude .= ' AND a.`id` NOT IN (';

			for ($i = 0; $i < count($excludeBlogs); $i++) {

				$queryExclude .= $db->Quote($excludeBlogs[ $i ]);

				if (next($excludeBlogs) !== false) {
					$queryExclude .= ',';
				}
			}
			$queryExclude .= ')';
		}

		// Exclude postings from specific categories
		if (!empty($excludeCats)) {
			$catAccess['exclude'] = $excludeCats;
		}

		$queryInclude = '';
		// Respect inclusion categories
		if (!empty($includeCats)) {
			$catAccess['include'] = $includeCats;
		}

		// Explicitly include authors
		if ($includeAuthors) {
			$queryExclude .= ' AND a.`created_by` IN (';

			for ($i = 0; $i < count($includeAuthors); $i++) {

				$queryExclude .= $db->Quote($includeAuthors[$i]);

				if (next($includeAuthors) !== false) {
					$queryExclude .= ',';
				}
			}

			$queryExclude .= ')';
		}

		// Explicitly exclude authors
		if (!empty($excludeAuthors)) {
			$queryExclude .= ' AND a.`created_by` NOT IN (';

			for ($i = 0; $i < count($excludeAuthors); $i++) {

				$queryExclude .= $db->Quote($excludeAuthors[$i]);

				if (next($excludeAuthors) !== false) {
					$queryExclude .= ',';
				}
			}

			$queryExclude .= ')';
		}

		// Explicitly include tags
		if ($includeTags) {
			$queryExclude .= ' AND t.`tag_id` IN (';

			for ($i = 0; $i < count($includeTags); $i++) {

				$queryExclude .= $db->Quote($includeTags[$i]);

				if (next($includeTags) !== false) {
					$queryExclude .= ',';
				}
			}

			$queryExclude .= ')';
		}

		switch ($published) {
			case EBLOG_FILTER_PENDING:
				$queryWhere = ' WHERE a.`published` = ' . $db->Quote(EASYBLOG_POST_PENDING);
				break;
			case EBLOG_FILTER_ALL:
				$queryWhere = ' WHERE (a.`published` = 1 OR a.`published`=0 OR a.`published`=2 OR a.`published`=3) ';
				$queryWhere .= ' AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;
			case EBLOG_FILTER_SCHEDULE:
				$queryWhere = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_SCHEDULED);
				break;
			case EBLOG_FILTER_UNPUBLISHED:
				$queryWhere = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_UNPUBLISHED);
				break;
			case EBLOG_FILTER_DRAFT:
				$queryWhere = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_DRAFT);
				break;
			case EBLOG_FILTER_PUBLISHED:
			default:
				$queryWhere = ' WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
				$queryWhere .= ' AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
				break;
		}

		// do not list out protected blog in rss
		if ($this->input->get('format', '', 'cmd') == 'feed' && $config->get('main_password_protect')) {
			$queryWhere .= ' AND a.`blogpassword`="" ';
		}

		// Blog privacy setting
		// @integrations: jomsocial privacy
		$file = JPATH_ROOT . '/components/com_community/libraries/core.php';

		$easysocial = EB::easysocial();
		$jomsocial = EB::jomsocial();

		if ($config->get('integrations_es_privacy') && $easysocial->exists() && !FH::isSiteAdmin() && $type != 'teamblog' && !$dashboard) {
			$esPrivacyQuery = $easysocial->buildPrivacyQuery('a');
			$queryWhere .= $esPrivacyQuery;

			$useSelectCount = false;

		} else if ($config->get('main_jomsocial_privacy') && $jomsocial->exists() && !FH::isSiteAdmin() && $type != 'teamblog' && !$dashboard) {
			require_once($file);

			$my = JFactory::getUser();
			$jsFriends = CFactory::getModel('Friends');
			$friends = $jsFriends->getFriendIds($my->id);
			array_push($friends, $my->id);

			// Insert query here.
			$queryWhere .= ' AND (';
			$queryWhere .= ' (a.`access`= 0) OR';
			$queryWhere .= ' ((a.`access` = 20) AND (' . $db->Quote($my->id) . ' > 0)) OR';

			if (empty($friends)) {
				$queryWhere .= ' ((a.`access` = 30) AND (1 = 2)) OR';
			}
			else
			{
				$queryWhere .= ' ((a.`access` = 30) AND (a.' . $db->nameQuote('created_by') . ' IN (' . implode(',', $friends) . '))) OR';
			}

			$queryWhere .= ' ((a.`access` = 40) AND (a.' . $db->nameQuote('created_by') .'=' . $my->id . '))';
			$queryWhere .= ')';

			$useSelectCount = false;

		} else {
			if ($my->id == 0) {
				$queryWhere .= ' AND a.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}
		}

		if ($isBloggerMode !== false) {
			$queryWhere .= ' AND a.`created_by` = ' . $db->Quote($isBloggerMode);
		}

		// refactored codes for #2014
		$contentId = '';
		$isIdArray = false;
		if (is_array($typeId)) {
			foreach ($typeId as $id) {
				$contentId .= $id;

				if ($id !== end($typeId)) {
					$contentId = ',';
				}
			$isIdArray = true;
			}
		} else {
			$contentId = $typeId;
		}

		if ($contentId) {

			switch ($type) {
				case 'category':

					$catAccess['type'] = $typeId;

					if ($isBloggerMode === false) {
						$catBloggerId = EB::getCategoryMenuBloggerId();
						if (!empty($catBloggerId)) {
							$queryWhere .= ' AND a.`created_by` = ' . $db->Quote($catBloggerId);
						}
					}

					break;
				case 'blogger':
					$queryWhere .= ($isIdArray) ? ' AND a.`created_by` IN ('. $contentId .')' : ' AND a.`created_by` = ' . $db->Quote($contentId);

					// Checking for the author_alias, if the viewer is not the viewed blogger, don't show the post that has author_alias
					// Only applied to type=blogger
					if ($config->get('layout_composer_author_alias', false) && $my->id != $contentId) {
						$queryWhere .= ' AND (a.`author_alias` IS NULL OR a.`author_alias` = ' . $db->Quote('') . ') ';
					}

					break;
				case 'teamblog':
					$queryWhere .= ' AND (a.source_type = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);
					$queryWhere .= ($isIdArray) ? ' AND a.source_id IN ('. $contentId .')' : ' AND a.`source_id` = ' . $db->Quote($contentId);
					$queryWhere .= ')';
					break;
				default :
					break;
			}
		}

		// @rule: Filter for `source` column type.
		if (!is_null($postType)) {
			switch ($postType) {
				case 'microblog':
					$queryWhere .= ' AND a.`posttype` != ' . $db->Quote('');
				break;
				case 'posts':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote('');
				break;
				case 'quote':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote('quote');
				break;
				case 'link':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote('link');
				break;
				case 'photo':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote('photo');
				break;
				case 'video':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote('video');
				break;
				case 'twitter':
					$queryWhere .= ' AND a.`posttype` = ' . $db->Quote('twitter');
				break;

			}
		}

		if ($type == 'blogger' || $type == 'teamblog') {

			if (! empty($statType)) {

				if ($statType == 'category') {
					$catAccess['statType'] = $statId;
				} else {
					$queryWhere .= ' AND t.`tag_id` = ' . $db->Quote($statId);
				}
			}
		}

		if ($search) {
			$queryWhere .= ' AND a.`title` LIKE ' . $db->Quote('%' . $search . '%');
		}

		if ($frontpage) {
			$queryWhere .= ' AND a.`frontpage` = ' . $db->Quote('1');
		}

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();

		if ($filterLanguage) {
			$queryWhere .= EBR::getLanguageQuery('AND', 'a.language');
		}

		if ($protected == false) {
			$queryWhere .= ' AND a.`blogpassword` = ""';
		}

		$catLib = EB::category();
		// Category privacy
		if ($config->get('main_category_privacy')) {
			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $catAccess);
			$queryWhere .= ' AND (' . $catAccessSQL . ')';

			$useSelectCount = false;
		} else {
			$catAccessSQL = $catLib->genBasicSQL('a.`id`', $catAccess);
			if ($catAccessSQL) {
				$queryWhere .= ' AND ' . $catAccessSQL;
			}
		}

		// check if we only want to fetch blog posts with images only #853
		if ($hasImageOnly) {
			$queryWhere .= ' AND (a.`image` != ' . $db->Quote('') . ' OR a.`media` NOT like ' . $db->Quote('{"images":[]%') . ')';
		}


		// retrieve posts based on last x of days.
		// for now this is used in most popular posts module. #1589
		$postsDuration = (isset($options['retrievalDuration']) && $options['retrievalDuration']) ? $options['retrievalDuration'] : 0;
		$postsDuration = (int) $postsDuration;

		if ($postsDuration) {
			$now = EB::date()->toSql();
			$queryWhere .= ' AND a.`created` <= ' . $db->Quote($now) . ' and a.`created` >= DATE_ADD(' . $db->Quote($now) . ', INTERVAL -' . $postsDuration . ' DAY)';
		}

		// exlude featured posts #2999
		if ($excludeFeatured) {
			$queryWhere .= ' AND NOT EXISTS (';
			$queryWhere .= '	SELECT ff.`content_id` FROM `#__easyblog_featured` AS ff WHERE ff.`content_id` = a.`id` AND ff.`type` = ' . $db->Quote('post'); 
			$queryWhere .= ')';
		}

		// get the default sorting.
		$defaultSorting = ($customOrdering) ? $customOrdering : $config->get('layout_postsort', 'desc');

		$queryOrder = ' ORDER BY ';

		$sortableItems = array('latest', 'published', 'popular', 'active', 'alphabet', 'modified', 'random');

		if ($frontpage && $pinFeatured) {
			$queryOrder .= ' f.`created` DESC,';
		}

		switch ($sort) {
			case 'latest':
				$queryOrder .= ' a.`created` ' . $defaultSorting;
				break;
			case 'published':
				$queryOrder .= ' a.`publish_up` ' . $defaultSorting;
				break;
			case 'popular':
				$queryOrder .= ' a.`hits` ' . $defaultSorting;
				break;
			case 'active':
				$queryOrder .= ' a.`publish_down` ' . $defaultSorting;
				break;
			case 'alphabet':
				$queryOrder .= ' a.`title` ' . $defaultSorting;
				break;
			case 'modified':
				$queryOrder .= ' a.`modified` ' . $defaultSorting;
				break;
			case 'random':
				$queryOrder .= ' `random_id` ';
				break;
			default :
				break;
		}

		$originalLimit = $max ? $max : EB::getViewLimit();
		$limit = $originalLimit;

		$limitstart = $this->input->get('limitstart', 0, 'int');

		$paginationStyle = FH::normalize($options, 'paginationStyle', 'fixed');

		if ($paginationStyle == 'fixed') {
			// In case limit has been changed, adjust it.
			// This to ensure that the pagination is incrementated in a fixed value.
			// eg: 
			//  site.com/index.php?limitstart=4 --> limit 0, 5
			//  site.com/index.php?limitstart=5 --> limit 5, 5
			//  site.com/index.php?limitstart=6 --> limit 5, 5
			//  site.com/index.php?limitstart=10 --> limit 10, 5
			$limitstart = (int) ($originalLimit != 0 ? (floor($limitstart / $originalLimit) * $originalLimit) : 0);
		}

		if ($limitstart < 0) {
			$limitstart = 0;
		}

		// For the loadmore style, we add additional one limit for the loadmore button visibility
		if ($paginationType == 'loadmore') {
			$limit = $originalLimit + 1;
		}

		$queryLimit = ' LIMIT ' . $limitstart . ',' . $limit;

		if (!$queryPagination && $max) {
			// the caller could be from module.
			$queryLimit = ' LIMIT ' . $max;
		}


		$useSelectCount = true;

		$query = 'SELECT ';

		if (! $useSelectCount) {
			$query = 'SELECT SQL_CALC_FOUND_ROWS ';
		}

		if ($includeTags) {
			$query .= 'distinct ';
		}

		$query .= 'a.`id` AS key1, a.*';
		$query .= ', ifnull(f.`id`, 0) as `featured`';

		if ($sort == 'random') {
			$query .= ', floor(1 + rand() * rd.`rid`) as `random_id`';
		}


		$query .= ' FROM `#__easyblog_post` AS a';

		if (!$showBlockedUserPosts) {
			// exclude block users. #1978
			$query .= ' INNER JOIN `#__users` AS uu ON a.`created_by` = uu.`id` and uu.`block` = 0';
		}

		
		$query .= ' LEFT JOIN `#__easyblog_featured` AS f';
		$query .= ' 	ON a.`id` = f.`content_id` AND f.`type` = ' . $db->Quote('post');

		if ((($type == 'blogger' || $type == 'teamblog') && $statType == 'tag') || $includeTags) {

			$query  .= ' LEFT JOIN `#__easyblog_post_tag` AS t ON a.`id` = t.`post_id`';

			if ($type == 'blogger' || $type == 'teamblog') {
				$query  .= ' AND t.`tag_id` = ' . $db->Quote($statId);
			}
		}

		// // CUSTOM FIELDS FILTER DEBUG
		// $fieldsFilter = array(
		// 			'1' => array('3'),
		// 			'2' => array('2', '1')
		// 		);
		// $strictMode = true;
		// Custom Fields filter
		$fieldAclQ = '';

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
				$query .= ' INNER JOIN ' . $fieldQuery . ' ON customFields.`post_id` = a.`id`';
			} else {
				$query .= ' LEFT JOIN ' . $fieldQuery . ' ON customFields.`post_id` = a.`id`';
				$queryWhere .= ' AND customFields.`post_id` IS NULL';
			}


		}

		if ($sort == 'random') {
			$query .= ', (select max(tmp.`id`) - 1 as `rid` from `#__easyblog_post` as tmp) as rd';
		}

		$query .= $queryWhere;
		$query .= $contributeSQL;
		$query .= $queryExclude;
		$query .= $queryInclude;
		$query .= $queryOrder;
		$query .= $queryLimit;

		// Debugging
		// echo str_ireplace('#__', 'jos_', $query);
		// echo '<br><br>';
		// exit;

		$db->setQuery($query);

		if ($db->getErrorNum() > 0) {
			throw EB::exception($db->getErrorMsg() . $db->stderr(), $db->getErrorNum());
		}

		$result = $db->loadObjectList();

		if ($queryPagination) {
			// now execute found_row() to get the number of records found.
			$cntQuery = 'select FOUND_ROWS()';

			if (EB::isFalangActivated() || $useSelectCount) {
				// the way falang perform translation will mess up the found_rows. the only solution is to run select count() #39
				$cntQuery = 'select count(1) from `#__easyblog_post` AS a';

				if ((($type == 'blogger' || $type == 'teamblog') && $statType == 'tag') || $includeTags) {
					$cntQuery  .= ' LEFT JOIN `#__easyblog_post_tag` AS t ON a.id = t.post_id';
				}

				$cntQuery .= $queryWhere;
				$cntQuery .= $contributeSQL;
				$cntQuery .= $queryExclude;
				$cntQuery .= $queryInclude;

				// echo str_ireplace('#__', 'jos_', $cntQuery);
				// echo '<br><br>';
				// exit;
			}

			$db->setQuery($cntQuery);
			$this->_total = $db->loadResult();

			$this->_pagination = EB::pagination($this->_total, $limitstart, $originalLimit);
		}

		return $result;
	}

	/**
	 * Retrieving posts in the trash
	 *
	 * @since   5.1
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getTrashPosts($userId = 0, $sort = 'latest', $max = 0, $search = false, $frontpage = false)
	{
		$db = EB::db();

		$queryPagination = false;
		$queryWhere = '';
		$queryOrder = '';
		$queryLimit = '';
		$queryWhere = '';
		$queryExclude = '';

		$queryWhere .= ' where a.' . $db->qn('state') . ' = ' . $db->Quote(EASYBLOG_POST_TRASHED);

		if ($search) {
			$queryWhere .= ' AND a.`title` LIKE ' . $db->Quote('%' . $search . '%');
		}

		if (!empty($userId)) {
			$queryWhere .= ' AND a.`created_by` = ' . $db->Quote($userId);
		}

		switch ($sort) {
			case 'latest':
				$queryOrder = ' ORDER BY a.`created` DESC';
				break;
			case 'active':
				$queryOrder = ' ORDER BY a.`modified` DESC';
				break;
			case 'alphabet':
				$queryOrder = ' ORDER BY a.`title` ASC';
				break;
			default :
				break;
		}

		if ($max > 0) {
			$queryLimit = ' LIMIT '.$max;
		} else {
			$limit = $this->getState('limit');
			$limitstart = $this->getState('limitstart');
			$queryLimit = ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		if ($queryPagination) {
			$query = 'SELECT COUNT(1) FROM `#__easyblog_post` AS a';

			$query .= $queryWhere;
			$query .= $queryExclude;

			$db->setQuery($query);
			$this->_total = $db->loadResult();

			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);
		}


		$query = 'SELECT a.* FROM `#__easyblog_post` AS a';

		$query .= $queryWhere;
		$query .= $queryExclude;
		$query .= $queryOrder;
		$query .= $queryLimit;

		$db->setQuery($query);

		if ($db->getErrorNum() > 0) {
			throw EB::exception($db->getErrorMsg() . $db->stderr(), $db->getErrorNum());
		}

		return $db->loadObjectList();
	}


	function getPending($typeId = 0, $sort = 'latest', $max = 0, $search = false, $frontpage = false)
	{
		$db = EB::db();

		$queryPagination = false;
		$queryWhere = '';
		$queryOrder = '';
		$queryLimit = '';
		$queryWhere = '';
		$queryExclude = '';

		$queryWhere .= ' where a.' . $db->qn('state') . ' = ' . $db->Quote(EASYBLOG_REVISION_PENDING);


		if ($search) {
			$queryWhere .= ' AND a.`title` LIKE ' . $db->Quote('%' . $search . '%');
		}

		if (! empty($typeId)) {
			$queryWhere .= ' AND a.`created_by` = ' . $db->Quote($typeId);
		}

		switch ($sort) {
			case 'latest':
				$queryOrder = ' ORDER BY a.`created` DESC';
				break;
			case 'active':
				$queryOrder = ' ORDER BY a.`modified` DESC';
				break;
			case 'alphabet':
				$queryOrder = ' ORDER BY a.`title` ASC';
				break;
			default :
				break;
		}

		if ($max > 0) {
			$queryLimit = ' LIMIT '.$max;
		} else {
			$limit = $this->getState('limit');
			$limitstart = $this->getState('limitstart');

			//set frontpage list length if it is detected to be the frontpage
			$view = $this->input->get('view', '', 'cmd');

			if ($view=='latest') {
				$config = EB::config();
				$listlength = $config->get('layout_listlength', '0');

				if ($listlength)
				{
					$limit = $listlength;
					$limitstart = $this->input->get('limitstart', 0, 'int');

					// In case limit has been changed, adjust it
					$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
				}
			}

			$queryLimit = ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		if ($queryPagination) {
			$query = 'SELECT COUNT(1) FROM `#__easyblog_revisions` AS a';

			$query .= $queryWhere;
			$query .= $queryExclude;

			$db->setQuery($query);
			$this->_total = $db->loadResult();

			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);
		}


		$query = 'SELECT a.* FROM `#__easyblog_revisions` AS a';
		$query .= $queryWhere;
		$query .= $queryExclude;
		$query .= $queryOrder;
		$query .= $queryLimit;

		$db->setQuery($query);

		if ($db->getErrorNum() > 0) {
			throw EB::exception($db->getErrorMsg() . $db->stderr(), $db->getErrorNum());
		}

		$result = $db->loadObjectList();
		return $result;
	}


	/**
	 * Method to get a pagination object for the categories
	 *
	 */
	function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 */
	function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get total blogs post currently iregardless the status.
	 *
	 */
	function getTotalBlogs($userId = 0)
	{
		$db = EB::db();
		$where = array();

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_post');

		//blog privacy setting
		$my = JFactory::getUser();
		if ($my->id == 0)
			$where[] = '`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		if (! empty($userId))
			$where[] = '`created_by` = ' . $db->Quote($userId);

		$extra = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$query = $query . $extra;

		$db->setQuery($query);

		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getTotalBlogSubscribers($userId = 0)
	{
		$db = EB::db();
		$where = array();

		$query = 'select count(1) from `#__easyblog_subscriptions` as a';
		$query .= '  inner join `#__easyblog_post` as b';
		$query .= '    on a.`uid` = b.`id`';
		$query .= '    WHERE a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);

		if (! empty($userId))
		$query .= '    and b.created_by = ' . $db->Quote($userId);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Retrieves a list of blog posts associated with a particular tag
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTaggedBlogs($tagId = 0, $limit = false, $includeCatIds = '', $sorting = '', $excludeFeatured = false, $options = array())
	{
		if (!$tagId) {
			return false;
		}

		$my = JFactory::getUser();
		$db = EB::db();
		$config = EB::config();
		$catAccess = array();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$defaultOrder = isset($options['order']) ? $options['order'] : -1;
		$defaultSort = isset($options['sort']) ? $options['sort'] : -1;
		$excludeBlogs = isset($options['excludeBlogs']) ? $options['excludeBlogs'] : array();

		if ($defaultOrder == -1 || $defaultOrder == '-1') {
			$defaultOrder = $config->get('layout_postorder', 'latest');
		}

		if ($defaultSort == -1 || $defaultOrder == '-1') {
			$defaultSort = $config->get('layout_postsort', 'desc');
		}

		$defaultSorting = $config->get('layout_postsort', 'desc');

		if ($limit === false) {
			if ($config->get('layout_listlength') == 0) {
				$limit = $this->getState('limit');
			} else {
				$limit = $config->get('layout_listlength');
			}
		}

		// We need to convert this into an array
		if (!is_array($tagId)) {
			$tagId = array($tagId);
		}

		$limitstart = $this->getState('limitstart');

		$isBloggerMode = EBR::isBloggerMode();
		$queryExclude = '';
		$excludeCats = array();

		$isJSGrpPluginInstalled = false;
		$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled = JPluginHelper::isEnabled('system', 'eventeasyblog');
		$isJSInstalled = false;

		if (JFile::exists(JPATH_ROOT . '/components/com_community/libraries/core.php')) {
			$isJSInstalled = true;
		}

		$includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;
		$jsGrpPostIds = '';
		$jsEventPostIds = '';

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ((b.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($config->get('main_includeteamblogpost')) {
		  $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'b');
		}

		if ($includeJSEvent) {
		  $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'b');
		}

		if ($includeJSGrp) {
		  $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'b');
		}

		if (EB::easysocial()->exists()) {
			if (EB::easysocial()->isBlogAppInstalled('group')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'b');
			}

			if (EB::easysocial()->isBlogAppInstalled('page')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE, 'b');
			}

			if (EB::easysocial()->isBlogAppInstalled('event')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'b');
			}
		}

		$contributeSQL .= ')';

		//get teamblogs id.
		$query = 'SELECT DISTINCT b.*, ifnull(f.`id`, 0) as `featured`';
		$query .= ' FROM ' . $db->qn('#__easyblog_post_tag') . ' AS a ';
		$query .= ' INNER JOIN ' . $db->nameQuote('#__easyblog_post') . ' AS b ';
		$query .= ' 	ON a.post_id=b.id ';
		$query .= ' LEFT JOIN `#__easyblog_featured` AS f';
		$query .= ' 	ON b.`id` = f.`content_id` AND f.`type` = ' . $db->Quote('post');

		if (!$showBlockedUserPosts) {
			// exclude block users. #1978
			$query .= ' INNER JOIN `#__users` AS uu ON b.`created_by` = uu.`id` and uu.`block` = 0';
		}

		$query .= ' WHERE a.' . $db->quoteName('tag_id') . ' IN ';
		$query .= '(';

		$totalTags = count($tagId);

		for ($i = 1; $i <= $totalTags; $i++) {
			$query .= $db->Quote($tagId[$i - 1]);

			if ($i < $totalTags) {
				$query .= ',';
			}
		}

		$query .= ')';
		$query .= ' AND b.' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' AND b.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		if (!empty($excludeBlogs)) {
			$query .= ' AND b.`id` NOT IN (';

			for ($i = 0; $i < count($excludeBlogs); $i++) {

				$query .= $db->Quote($excludeBlogs[$i]);

				if (next($excludeBlogs) !== false) {
					$query .= ',';
				}
			}
			$query .= ')';
		}

		$query .= $contributeSQL;

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();

		if ($filterLanguage) {
			$query .= EBR::getLanguageQuery('AND', 'b.language');
		}


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
			$queryPrivacy = " AND (";
			$queryPrivacy .= " (b.`access`= 0) OR";
			$queryPrivacy .= " ((b.`access` = 20) AND (" . $db->Quote($my->id) . " > 0)) OR";

			if (empty($friends)) {
				$queryPrivacy .= " ((b.`access` = 30) AND (1 = 2)) OR";
			}
			else
			{
				$queryPrivacy .= " ((b.`access` = 30) AND (b." . $db->nameQuote('created_by') . " IN (" . implode(",", $friends) . "))) OR";
			}

			$queryPrivacy .= " ((b.`access` = 40) AND (b." . $db->nameQuote('created_by') ."=" . $my->id . "))";
			$queryPrivacy .= ")";

			$query .= $queryPrivacy;

		} else if ($this->my->id == 0) {

			$queryPrivacy = " AND b.`access` = " . $db->Quote(BLOG_PRIVACY_PUBLIC);
			$query .= $queryPrivacy;
		}


		if ($isBloggerMode !== false)
			$query .= ' AND b.`created_by` = ' . $db->Quote($isBloggerMode);

		$includeCats = array();
		$includeCatIds = trim($includeCatIds);
		if (!empty($includeCatIds)) {
			$includeCats = explode(',', $includeCatIds);

			if (!empty($includeCats)) {
				$catAccess['include'] = $includeCats;
			}
		}

		// exlude featured posts #2999
		if ($excludeFeatured) {
			$query .= ' AND f.`id` is null';
		}

		// category access
		$catLib = EB::category();
		if ($config->get('main_category_privacy')) {
			$catAccessSQL = $catLib->genAccessSQL('b.`id`', $catAccess);
			$query .= ' AND (' . $catAccessSQL . ')';
		} else {
			$catAccessSQL = $catLib->genBasicSQL('b.`id`', $catAccess);
			if ($catAccessSQL) {
				$query .= ' AND ' . $catAccessSQL;
			}
		}

		// Override the sorting
		if ($sorting) {
			$defaultSort = $sorting;
		}

		switch ($defaultOrder) {
			case 'latest':
			$queryOrder = ' ORDER BY b.`created` ' . $defaultSort;
			break;
			case 'published':
			$queryOrder = ' ORDER BY b.`publish_up` ' . $defaultSort;
			break;
			case 'popular':
			$queryOrder = ' ORDER BY b.`hits` ' . $defaultSort;
			break;
			case 'active':
			$queryOrder = ' ORDER BY b.`publish_down` ' . $defaultSort;
			break;
			case 'alphabet':
			$queryOrder = ' ORDER BY b.`title` ' . $defaultSort;
			break;
			case 'modified':
			$queryOrder = ' ORDER BY b.`modified` ' . $defaultSort;
			break;
			case 'random':
			$queryOrder = ' ORDER BY RAND() ';
			break;
			default :
			break;
		}

		$query .= $queryOrder;

		//total tag's post sql
		$totalQuery = 'SELECT COUNT(1) FROM (';
		$totalQuery .= $query;
		$totalQuery .= ') as x';

		$query .= ' LIMIT ' . $limitstart . ',' . $limit;

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$db->setQuery($totalQuery);
		$this->_total = $db->loadResult();

		jimport('joomla.html.pagination');
		$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);

		return $rows;
	}

	function isBlogSubscribedUser($blogId, $userId, $email)
	{
		$db = EB::db();

		$query = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query .= ' WHERE `uid` = ' . $db->Quote($blogId);
		$query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);
		$query .= ' AND (`user_id` = ' . $db->Quote($userId);
		$query .= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function isBlogSubscribedEmail($blogId, $email)
	{
		$db = EB::db();

		$query = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query .= ' WHERE `uid` = ' . $db->Quote($blogId);
		$query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);
		$query .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function addBlogSubscription($blogId, $email, $userId = '0', $fullname = '')
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$date = EB::date();
			$subscriber = EB::table('Subscriptions');
			$subscriber->uid = $blogId;
			$subscriber->utype = EBLOG_SUBSCRIPTION_ENTRY;
			$subscriber->email = $email;

			if ($userId != '0')
				$subscriber->user_id = $userId;

			$subscriber->fullname = $fullname;
			$subscriber->created = $date->toSql();
			$state = $subscriber->store();

			if ($state) {
				$blog = EB::post($blogId);

				// lets send confirmation email to subscriber.
				$helper = EB::subscription();
				$template = $helper->getTemplate();

				$template->uid = $subscriber->id;
				$template->utype = 'subscription';
				$template->user_id = $subscriber->user_id;
				$template->uemail = $email;
				$template->ufullname = $fullname;
				$template->ucreated = $subscriber->created;
				$template->targetname = $blog->title;
				$template->targetlink = $blog->getExternalBlogLink();

				if ($blog->created_by != $subscriber->user_id) {
					$helper->addMailQueue($template);
				}
			}

			return $state;
		}

		return false;
	}

	/**
	 * Converts a string into a valid permalink
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizePermalink($string, $postId = null)
	{
		// Permalink must be free from emoji characters. #479
		$string = EB::string()->removeEmoji($string);
		$permalink = EBR::normalizePermalink($string);

		$i = 1;

		while ($this->permalinkExists($permalink, $postId)) {
			$permalink = $string . '-' . $i;
			$i++;
		}

		$permalink = EBR::normalizePermalink($permalink);

		return $permalink;
	}

	/**
	 * Determines if the post's permalink exists on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function permalinkExists($permalink, $postId = null)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('permalink') . '=' . $db->Quote($permalink);
		$query[] = 'AND ' . $db->qn('published') . '!=' . $db->Quote(EASYBLOG_POST_BLANK);

		if ($postId) {
			$query[] = 'AND ' . $db->qn('id') . '!=' . $db->Quote($postId);
		}

		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Allow callers to update Easysocial stream contribution
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function updateStreamContribution($streamIds, $contribution, $blog, $hasUpdatedAuthor = false, $contextType = 'blog')
	{
		$db = EB::db();

		$originalStreamIds = $streamIds;
		$streamIds = implode(',', $originalStreamIds);

		$query = 'UPDATE ' . $db->nameQuote('#__social_stream');
		$query .= ' SET ' . $db->nameQuote('cluster_id') . '=' . $db->Quote($contribution->id);
		$query .= ' , ' . $db->nameQuote('cluster_type') . '=' . $db->Quote($contribution->type);
		$query .= ' , ' . $db->nameQuote('cluster_access') . '=' . $db->Quote(1);
		$query .= ' WHERE ' . $db->nameQuote('id') . ' IN (' . $streamIds . ')';

		$db->setQuery($query);
		$db->query();

		// Update stream author id if the user changed that blog author to other user
		if ($hasUpdatedAuthor) {
			$this->updateStreamData($originalStreamIds, $blog, $contextType);
		}
	}

	/**
	 * Update existing stream data if changed blog author and other
	 *
	 * @since   5.2.10
	 * @access  public
	 */
	public function updateStreamData($streamIds, $blog, $contextType = 'blog')
	{
		$db = ES::db();

		$streamIds = implode(',', $streamIds);

		$query = 'UPDATE ' . $db->nameQuote('#__social_stream');
		$query .= ' SET ' . $db->nameQuote('actor_id') . ' = ' . $db->Quote($blog->created_by);
		$query .= ' WHERE ' . $db->nameQuote('id') . ' IN (' . $streamIds . ')';

		$db->setQuery($query);
		$db->query();

		$query = 'UPDATE ' . $db->nameQuote('#__social_stream_item');
		$query .= ' SET ' . $db->nameQuote('actor_id') . ' = ' . $db->Quote($blog->created_by);
		$query .= ' WHERE ' . $db->nameQuote('context_id') . ' = ' . $db->Quote($blog->id);
		$query .= ' AND ' . $db->nameQuote('context_type') . ' = ' . $db->Quote($contextType);

		$db->setQuery($query);
		$db->query();
	}

	public function updateBlogSubscriptionEmail($sid, $userid, $email)
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$subscriber = EB::table('Subscriptions');
			$subscriber->load($sid);
			$subscriber->user_id = $userid;
			$subscriber->email = $email;
			$subscriber->store();
		}
	}

	/**
	 * Retrieves the next post in line
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostNavigation(EasyBlogPost $post, $navigationType, $exclusion = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config = EB::config();
		$params = $post->getMenuParams();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$keys = array('prev','next');
		$nav = new stdClass();

		// Get the active menu
		$active = JFactory::getApplication()->getMenu()->getActive();
		$catAccess = array();
		$queryInclude = '';

		$teamId = $post->getTeamAssociation();
		$author = $post->getAuthor();

		$nextId = false;

		// For random navigation, we know the previous exclusion is the next post
		if ($params->get('pagination_style') == 'autoload' && !empty($exclusion) && count($exclusion) > 1) {
			$nextId = $exclusion[count($exclusion) - 2];
		}

		foreach ($keys as $key) {

			if ($nextId && $key == 'next') {
				$nextPost = EB::post($nextId);

				$nav->$key = new stdClass();
				$nav->$key->id = $nextPost->id;
				$nav->$key->title = $nextPost->getTitle();
				continue;
			}

			$query = array();

			$query[] = 'SELECT a.`id`, a.`title`';
			$query[] = ' FROM `#__easyblog_post` AS `a`';

			if (!$showBlockedUserPosts) {
				// exclude block users. #1978
				$query[] = ' INNER JOIN ' . $db->quoteName('#__users') . ' AS uu ON a.`created_by` = uu.`id` and uu.`block` = 0';
			}

			$query[] = ' WHERE a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query[] = ' AND a.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

			// EasySocial integrations
			$query[] = EB::easysocial()->buildPrivacyQuery('a');

			// Jomsocial integrations
			$query[] = EB::jomsocial()->buildPrivacyQuery();

			// Blog privacy settings
			if ($my->guest) {
				$query[] = 'AND a.' . $db->qn('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}

			// Exclude private categories
			$catLib = EB::category();
			if ($config->get('main_category_privacy')) {
				$catAccessSQL = $catLib->genAccessSQL('a.`id`');
				$query[] = ' AND (' . $catAccessSQL . ')';
			}

			// If the current menu is blogger mode, we need to respect this by only loading author related items
			$isBloggerMode = EBR::isBloggerMode();

			if ($isBloggerMode !== false) {
				$query[] = 'AND a.' . $db->qn('created_by') . '=' . $db->Quote($isBloggerMode);
				$query[] = 'AND a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE);
			}

			// Filter the next / previous link by team
			if ($navigationType == 'team' && $teamId) {
				$query[] = 'AND (a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM) . ' AND a.' . $db->qn('source_id') . '=' . $db->Quote($teamId) . ')';
			}

			// Filter the next / previous by author
			if ($navigationType == 'author') {
				$query[] = 'AND a.' . $db->qn('created_by') . '=' . $db->Quote($author->id);
				$query[] = 'AND a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE);
			}

			// Filter the next / previous post items from category
			if ($navigationType == 'category') {
				$query[] = 'AND a.' . $db->qn('category_id') . '=' . $db->Quote($post->category_id);
				$query[] = 'AND a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE);
			}

			// Filter the next / previous post items from site wide
			if ($navigationType == 'site' || $navigationType == 'random') {
				$query[] = 'AND a.' . $db->qn('source_type') . '=' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE);
			}

			if (!empty($exclusion)) {
				$excludeIds = implode(',', $exclusion);
				$query[] = 'AND a.' . $db->qn('id') . ' NOT IN(' . $excludeIds . ')';
			}

			// When language filter is enabled, we need to detect the appropriate contents
			$filterLanguage = JFactory::getApplication()->getLanguageFilter();

			if ($filterLanguage) {
				$query[] = EBR::getLanguageQuery('AND', 'a.language');
			}

			if ($navigationType != 'random') {
				if ($key == 'prev') {
					$query[] = ' AND a.`created` < ' . $db->Quote($post->created);
					$query[] = ' ORDER BY a.`created` DESC';
				}

				if ($key == 'next') {
					$query[] = ' AND a.`created` > ' . $db->Quote($post->created);
					$query[] = ' ORDER BY a.`created` ASC';
				}

				$query[] = 'LIMIT 1';

				$query = implode(' ', $query);

				$db->setQuery($query);
				$result = $db->loadObject();
			} else {

				// Randomize the column used and ordering type instead of sql rand() to optimize the performance
				$columns = array('title', 'permalink', 'created_by', 'category_id', 'created', 'modified', 'hits', 'revision_id', 'publish_up', 'ip');
				$ordering = array('ASC', 'DESC');

				// Mersenne Twister algorithm
				$sortByColumn = $columns[mt_rand(0, count($columns) - 1)];
				$ordering = $ordering[mt_rand(0, count($ordering) - 1)];

				$query[] = ' ORDER BY a.' . $db->nameQuote($sortByColumn) . ' ' . $ordering;
				$query[] = ' LIMIT 10';

				$query = implode(' ', $query);
				$db->setQuery($query);
				$results = $db->loadObjectList();

				if ($results) {
					$result = $results[mt_rand(0, count($results) - 1)];
				}

				// Ensure that there is no duplicate for the next foreach
				if ($result) {
					$exclusion[] = $result->id;
				}
			}

			$nav->$key = $result;
		}

		return $nav;
	}

	function getCategoryName($category_id)
	{
		$db = EB::db();

		if ($category_id == 0)
			return JText::_('COM_EASYBLOG_UNCATEGORIZED');

		$query = 'SELECT `title`, `id` FROM `#__easyblog_category` WHERE `id` = ' . $db->Quote($category_id);
		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}

	/**
	 * Retrieves a list of posts created by the user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getUserPosts($userId, $options = array())
	{
		$db = EB::db();

		$type = EB::normalize($options, 'type', 'public');
		$limitstart = EB::normalize($options, 'limitstart', 0);
		$limit = EB::normalize($options, 'limit', false);
		$sort = EB::normalize($options, 'sort', false);
		$search = EB::normalize($options, 'search', '');

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('created_by') . '=' . $db->Quote($userId);

		// Viewable by public
		if ($type == 'public') {
			$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query[] = 'AND ' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		}

		if ($search) {
			$query[] = 'AND `title` LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
		}

		if (isset($options['exclude']) && $options['exclude']) {
			if (is_array($options['exclude'])) {
				$query[] = 'AND ' . $db->qn('id') . ' NOT IN (' . implode(',', $options['exclude']) . ')';
			} else {
				$query[] = 'AND ' . $db->qn('id') . ' !=' . $db->Quote($options['exclude']);
			}
		}

		if ($sort) {

			$orderby = '';
			switch($sort) {
				case 'title':
					$orderby = 'ORDER BY `title` ASC';
					break;
				case 'latest':
				default:
					$orderby = 'ORDER BY `created` DESC';
					break;
			}

			$query[] = $orderby;
		}

		if ($limit) {
			$query[] = 'LIMIT ' . $limitstart . ',' . $limit;
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$items = $db->loadObjectList();

		return $items;
	}

	function getTrackback($blogId)
	{
		$db = EB::db();

		$query = 'SELECT * FROM `#__easyblog_trackback`';
		$query .= ' WHERE `post_id` = ' . $db->Quote($blogId);
		$query .= ' AND `published`=' . $db->Quote(1);
		$query .= ' ORDER BY `created` DESC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves posts that is related to the given blog post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getRelatedPosts($id, $max = 0, $behavior = 'tags', $categoryId = null, $title = null, $options = [])
	{
		$db = EB::db();
		$config = EB::config();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$ordering = EB::normalize($options, 'ordering', 'created');
		$sort = EB::normalize($options, 'sort', 'desc');

		$result = [];

		if ($behavior == 'tags') {
			$query = [];

			// Get a list of tags
			$query[] = 'SELECT ' . $db->quoteName('tag_id') . ' FROM ' . $db->quoteName('#__easyblog_post_tag') . ' AS pt';
			$query[] = 'inner join ' . $db->quoteName('#__easyblog_tag') . ' AS t ON pt.`tag_id` = t.`id`';
			$query[] = 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($id);

			$query = implode(' ', $query);

			$db->setQuery($query);
			$tags = $db->loadColumn();

			if (!$tags) {
				return $tags;
			}
		}

		$query = [];
		$query[] = 'SELECT DISTINCT c.*,';

		if ($behavior == 'tags') {
			$query[] = 'l.' . $db->quoteName('title')  . ' AS ' . $db->quoteName('category');
			$query[] = ',0 as `featured`';
			$query[] = 'FROM ' . $db->quoteName('#__easyblog_post_tag') . ' AS a';
			$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post_tag') . ' AS a1';
			$query[] = 'ON a.' . $db->quoteName('tag_id') . ' = a1.' . $db->quoteName('tag_id') . ' AND a1.`tag_id` != ' . $db->Quote('0');
			$query[] = 'AND a1.' . $db->quoteName('post_id') . '=' . $db->Quote($id);

			$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post') . ' AS c';
			$query[] = 'ON a.' . $db->quoteName('post_id') . ' = c.' . $db->quoteName('id');

			if (!$showBlockedUserPosts) {
				// exclude blocked users posts/
				$query[] = 'INNER JOIN ' . $db->quoteName('#__users') . ' AS uu';
				$query[] = 'ON c.' . $db->quoteName('created_by') . ' = uu.' . $db->quoteName('id') . ' and uu.`block` = 0';
			}

			$query[] = 'LEFT JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS k';
			$query[] = 'ON k.' . $db->quoteName('post_id') . ' = c.' . $db->quoteName('id');

			$query[] = 'LEFT JOIN ' . $db->quoteName('#__easyblog_category') . ' AS l';
			$query[] = 'ON c.' . $db->quoteName('category_id') . ' = l.' . $db->quoteName('id');

			$query[] = 'WHERE a.' . $db->quoteName('post_id') . '!=' . $db->Quote($id);
		}

		if ($behavior == 'category' && $categoryId) {
			$query[] = '0 as `featured`';
			$query[] = 'FROM ' . $db->quoteName('#__easyblog_category') . ' AS a';
			$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_category') . ' AS a1';
			$query[] = 'ON a.' . $db->quoteName('id') . ' = a1.' . $db->quoteName('id') . ' AND a1.' . $db->quoteName('id') . ' = ' . $db->Quote($categoryId);
			$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post') . ' AS c';
			$query[] = 'ON a1.' . $db->quoteName('id') . ' = c.' . $db->quoteName('category_id');

			if (!$showBlockedUserPosts) {
				// exclude blocked users posts
				$query[] = 'INNER JOIN ' . $db->quoteName('#__users') . ' AS uu';
				$query[] = 'ON c.' . $db->quoteName('created_by') . ' = uu.' . $db->quoteName('id') . ' and uu.`block` = 0';
			}

			$query[] = 'WHERE c.' . $db->quoteName('id') . '!=' . $db->Quote($id);
		}

		if ($behavior == 'title' && $title) {
			// $words = explode(" ", $title);
			// $lastWord = end($words);

			$query[] = '0 as `featured`,';
			$query[] = 'MATCH (c.' . $db->quoteName('title') . ') AGAINST(' . $db->Quote($title) . ' IN BOOLEAN MODE) AS `relevance`';
			// $query[] = '(';

			// foreach ($words as $word) {
			// 	if ($word == $lastWord) {
			// 		$query[] = '(CASE WHEN c.' . $db->quoteName('title') . ' LIKE ' . $db->Quote('%' . $db->getEscaped($word, true) . '%', false) . ' THEN 1 ELSE 0 END)) AS `matches`';
			// 		break;
			// 	}

			// 	$query[] = '(CASE WHEN c.' . $db->quoteName('title') . ' LIKE ' . $db->Quote('%' . $db->getEscaped($word, true) . '%', false) . ' THEN 1 ELSE 0 END) +';
			// }

			$query[] = 'FROM ' . $db->quoteName('#__easyblog_post') . ' AS c';

			if (!$showBlockedUserPosts) {
				// exclude blocked users posts
				$query[] = 'INNER JOIN ' . $db->quoteName('#__users') . ' AS uu';
				$query[] = 'ON c.' . $db->quoteName('created_by') . ' = uu.' . $db->quoteName('id') . ' and uu.`block` = 0';
			}

			// $query[] = 'WHERE c.' . $db->quoteName('id') . '!=' . $db->Quote($id) . ' AND (';
			$query[] = 'WHERE MATCH (c.' . $db->quoteName('title') . ') AGAINST(' . $db->Quote($title) . ' IN BOOLEAN MODE)';
			$query[] = ' AND c.' . $db->quoteName('id') . '!=' . $db->Quote($id);

			// foreach ($words as $word) {

			// 	if ($word == $lastWord) {
			// 		$query[] = 'c.' . $db->quoteName('title') . ' LIKE ' . $db->Quote('%' . $db->getEscaped($word, true) . '%', false) . ')';
			// 		break;
			// 	}

			// 	$query[] = 'c.' . $db->quoteName('title') . ' LIKE ' . $db->Quote('%' . $db->getEscaped($word, true) . '%', false) . ' OR';
			// }
		}

		$query[] = 'AND c.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND c.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		// Determines if there is a new-ness filter. If there is one, we should only pick up posts within the specified interval
		$interval = EB::normalize($options, 'newness', 0);

		if ($interval) {
			$interval = (int) $interval;
			$query[] = 'AND c.`created` >= CURDATE() - INTERVAL ' . $interval . ' DAY';
		}

		// category access here
		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccessSQL = $catLib->genAccessSQL('c.`id`');
			$query[] = ' AND (' . $catAccessSQL . ')';
		}

		// When language filter is enabled, we need to detect the appropriate contents
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query[] = 'AND(';
			$query[] = 'c.' . $db->quoteName('language') . '=' . $db->Quote($language);
			$query[] = 'OR';
			$query[] = 'c.' . $db->quoteName('language') . '=' . $db->Quote('');
			$query[] = 'OR';
			$query[] = 'c.' . $db->quoteName('language') . '=' . $db->Quote('*');
			$query[] = ')';
		}


		// If behavior is title, we will sort by title relevance in descending
		// The greater the relevance, the higher chance to show up in the related posts first
		$orderQuery = 'ORDER BY `relevance` DESC';
		// $orderQuery = 'ORDER BY `matches` DESC';

		if ($behavior != 'title') {
			$orderQuery = 'ORDER BY c.'. $db->quoteName($ordering) . ' ' . $sort;
		}

		$query[] = $orderQuery;
		$query[] = 'LIMIT ' . $max;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		EB::cache()->insert($result);
		$posts = EB::formatter('list', $result, false);

		return $posts;
	}

	/**
	 * Use EasyBlogModelBlogs->approveBlog instead.
	 *
	 * @deprecated	5.0
	 * @access	public
	 */
	public function approveBlog($id)
	{
		$model = EB::model('Blogs');
		return $model->approveBlog($id);
	}

	/**
	 * Retrieves a list of templates created by the user
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTemplates($userId)
	{
		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post_templates');
		$query .= ' WHERE ' . $db->quoteName('user_id') . '=' . $db->Quote($userId);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$templates = array();

		foreach ($result as $row) {
			$template = EB::table('PostTemplate');
			$template->bind($row);

			$templates[] = $template;
		}

		return $templates;
	}

	/**
	 * Retrieves the latest post by author
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getLatestPostByAuthor($authorId = null, $limit = 1)
	{
		$db = EB::db();
		$user = JFactory::getUser($authorId);

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('created_by') . '=' . $db->Quote($user->id);
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND ' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'ORDER BY ' . $db->qn('created') . ' DESC';
		$query[] = 'LIMIT 0,' . (int) $limit;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$post = EB::post($row->id);

			$posts[] = $post;
		}

		return $posts;
	}

	/**
	 * Retrieves the meta id for a blog post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getMetaId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.' . $db->qn('id') . ' FROM ' . $db->qn('#__easyblog_meta') . ' AS a';
		$query[] = 'WHERE a.' . $db->qn('content_id') . '=' . $db->Quote($id);
		$query[] = 'AND a.' . $db->qn('type') . '=' . $db->Quote('post');

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves a list of most commented posts created by the author
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getMostCommentedPostByAuthor($authorId = null, $limit = 5)
	{
		$db = EB::db();
		$user = JFactory::getUser($authorId);

		$query = array();
		$query[] = 'SELECT a.*, COUNT(b.' . $db->qn('id') . ') AS ' . $db->qn('totalcomments') . ' FROM ' . $db->qn('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_comment') . ' AS b';
		$query[] = 'ON b.' . $db->qn('post_id') . ' = a.' . $db->qn('id');
		$query[] = 'WHERE a.' . $db->qn('created_by') . '=' . $db->Quote($user->id);
		$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'AND b.' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'GROUP BY a.' . $db->qn('id');
		$query[] = 'ORDER BY ' . $db->qn('totalcomments') . ' DESC';
		$query[] = 'LIMIT 0,' . (int) $limit;

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {
			$post = EB::post($row->id);
			$post->totalcomments = $row->totalcomments;

			$posts[] = $post;
		}

		return $posts;
	}

	/**
	 * Retrieves a list of posts for post association
	 *
	 * @since	5.4.5
	 * @access	public
	 */
	public function getAssociationPosts($options = array())
	{
		$db = EB::db();

		$query = "select a.* from `#__easyblog_post` as a";
		$query .= " WHERE a." . $db->quoteName('published') . " = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= " AND a." . $db->quoteName('state') . " = " . $db->Quote(EASYBLOG_POST_NORMAL);

		if (isset($options['langcode']) && $options['langcode']) {
			$query .= " AND a." . $db->quoteName('language') . " = " . $db->Quote($options['langcode']);
		}

		if (isset($options['userid']) && $options['userid']) {
			$query .= " AND a." . $db->quoteName('created_by') . " = " . $db->Quote($options['userid']);
		}

		if (isset($options['search']) && $options['search']) {
			$query .= " AND a." . $db->quoteName('title') . " LIKE " . $db->Quote('%' . $options['search'] . '%');
		}

		// Default limit to 50
		$limit = 50;
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		if ($limitstart < 0) {
			$limitstart = 0;
		}

		$queryLimit = " LIMIT " . $limitstart . "," . $limit;

		// total count
		$queryCnt = "SELECT COUNT(1) from (";
		$queryCnt .= $query;
		$queryCnt .= ") as x";

		$db->setQuery($queryCnt);
		$this->_total = $db->loadResult();

		$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);

		$query = $query . $queryLimit;

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Delete posts from particular user
	 *
	 * @since	5.1.0
	 * @access	public
	 */
	public function deleteUserPosts($userId)
	{
		$state = array();

		$items = $this->getUserPosts($userId, array('type' => 'all'));

		foreach ($items as $item) {
			$post = EB::post($item->id);
			$state[] = $post->delete();
		}

		if (in_array(false, $state)) {
			return false;
		}

		return true;
	}
}
