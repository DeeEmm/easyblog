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

class EasyBlogModelSearch extends EasyBlogAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total = null;

	public $_includeArchive = null;
	public $_tags = null;
	public $_sort = null;
	public $_ordering = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->_includeArchive = $this->config->get('main_archiving_search', 0);
		$this->_tags = array();
		$this->_sort = '';
		$this->_ordering = '';

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		// Load total number of rows
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_buildQuery());
		}

		return $this->_total;
	}


	/**
	 * Method to update the includeArchive flag
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function setArchiveSearch($allow = true)
	{
		$this->_includeArchive = $allow;
	}

	/**
	 * Method to update the includeArchive flag
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function setTags($tagIds)
	{
		if (!$tagIds) {
			$this->_tags = '';
			return;
		}

		$tags = EB::makeArray($tagIds);

		if ($tags) {

			// prevent the tags value caused the sql error e.g. single quote
			foreach ($tags as $key => $tag) {

				// unset the key if the tag value is not integer
				if (!is_int($tag)) {
					unset($tags[$key]);
					continue;
				}

				$tags[$key] = $tag;
			}
		}

		if (!$tags) {
			$this->_tags = '';
			return;
		}

		$this->_tags = $tags;
	}

	/**
	 * Method to set the query sorting
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function setSorting($sort)
	{
		$this->_sort = $sort;
	}

	/**
	 * Method to set the query's sort ordering
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function setOrdering($ordering)
	{
		$this->_ordering = $ordering;
	}

	public function _buildQuery()
	{
		$db = EB::db();

		// used for privacy
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$excludeCats = [];
		$isBloggerMode = EBR::isBloggerMode();

		$where = [];
		$catId = $this->input->get('category_id', 0, 'int');

		$sort = $this->_sort ? $this->_sort : 'score';
		$ordering = $this->_ordering ? $this->_ordering : 'desc';

		// normalize search terms
		$text = $this->normalizeTerms();

		$words = explode(' ', $text);
		$wheres = [];

		foreach ($words as $word) {
			$word = $db->Quote('%' . $db->getEscaped($word, true) . '%', false);

			$where[] = 'a.`title` LIKE ' . $word;
			$where[] = 'a.`content` LIKE ' . $word;
			$where[] = 'a.`intro` LIKE ' . $word;

			$wheres[] = implode(' OR ', $where);
		}

		$where = '((' . implode(') OR (', $wheres) . '))';

		$isJSGrpPluginInstalled = false;
		$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled = JPluginHelper::isEnabled('system', 'eventeasyblog');
		$isJSInstalled = false; // need to check if the site installed jomsocial.

		if (JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'core.php')) {
			$isJSInstalled = true;
		}

		$includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;

		$query = '';

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ((a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($this->config->get('main_includeteamblogpost')) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
		}

		if ($includeJSEvent) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'a');
		}

		if ($includeJSGrp) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'a');
		}

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

		$queryWhere .= $contributeSQL;

		// category access here
		$config = EB::config();
		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccessSQL = $catLib->genAccessSQL('a.`id`');
			$queryWhere .= ' AND (' . $catAccessSQL . ')';
		}

		if ($isBloggerMode) {
			$queryWhere .= ' AND a.`created_by`=' . $db->Quote($isBloggerMode);
		}

		if ($catId) {
			// There is a possibility that this is parent category
			$model = EB::model('Categories');
			$categories = $model->getChildCategories($catId);

			$catIds = [$catId];

			foreach ($categories as $category) {
				$catIds[] = (int) $category->id;
			}

			$queryWhere .= ' AND pc.`category_id` IN(' . implode(',', $catIds) . ')';
		}

		$query = 'SELECT DISTINCT a.*, CONCAT(a.`content`, a.`intro`) AS text';

		if ($sort === 'score') {
			$query = 'SELECT * from (';
			$query .= 'SELECT DISTINCT a.*, CONCAT(a.`content`, a.`intro`) AS text, ';
		}

		// %this is text%
		$textquery = $db->Quote('%'.$db->getEscaped($text, true).'%', false);

		if ($sort === 'score') {
			$caseQuery = '((CASE WHEN a.`title` = ' . $db->Quote($text) . ' THEN 4 ELSE 0 END) + (CASE WHEN a.`title` LIKE ' . $textquery . ' THEN 3 ELSE 0 END)';
			$caseQuery .= ' + (CASE WHEN a.`content` LIKE ' . $textquery . ' THEN 2 ELSE 0 END) + (CASE WHEN a.`intro` LIKE ' . $textquery . ' THEN 2 ELSE 0 END)) as score';

			$query .= $caseQuery;
		}

		$query .= ' FROM `#__easyblog_post` as a USE INDEX (`easyblog_post_searchnew`)';

		// Always inner join with jos_users and a.created_by so that only valid blogs are loaded
		$query .= ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS c ON a.`created_by`=c.`id`';

		// Always inner join with the category table so that only published are loaded
		$query .= ' INNER JOIN ' . $db->nameQuote('#__easyblog_category') . ' AS cat ON cat.`id` = a.`category_id`';

		if ($catId) {
			$query .= ' INNER JOIN ' . $db->nameQuote('#__easyblog_post_category') . ' AS pc ON a.`id` = pc.`post_id`';
		}

		$query .= ' WHERE ' . $where;

		$tagIds = $this->_tags;

		if ($tagIds) {
			// if tag ids supplied in the search, lets filter the post based on these tag ids.
			// #2464

			$query .= ' AND a.`id` IN (';
			$query .= '    SELECT tp.`post_id` FROM `#__easyblog_post_tag` as tp';
			$query .= '        INNER JOIN `#__easyblog_tag` AS t ON tp.`tag_id` = t.`id`';
			$query .= '    WHERE t.`id` IN (' . implode(',', $tagIds) . ')';
			$query .= '  GROUP BY tp.`post_id` HAVING COUNT(tp.`id`) >= ' . count($tagIds);
			$query .= ')';
		}

		//blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		$easysocial = EB::easysocial();
		$jomsocial = EB::jomsocial();

		if ($this->config->get('integrations_es_privacy') && $easysocial->exists() && !FH::isSiteAdmin()) {
			$esPrivacyQuery = $easysocial->buildPrivacyQuery('a');
			$privateBlog .= $esPrivacyQuery;
		} else if ($this->config->get('main_jomsocial_privacy') && $jomsocial->exists() && !FH::isSiteAdmin()) {
			$jsFriends = CFactory::getModel('Friends');
			$friends = $jsFriends->getFriendIds($this->my->id);

			// Insert query here.
			$privateBlog .= ' AND (';
			$privateBlog .= ' (a.`access`= 0) OR';
			$privateBlog .= ' ((a.`access` = 20) AND (' . $db->Quote($this->my->id) . ' > 0)) OR';

			if (empty($friends)) {
				$privateBlog .= ' ((a.`access` = 30) AND (1 = 2)) OR';
			} else {
				$privateBlog .= ' ((a.`access` = 30) AND (a.' . $db->nameQuote('created_by') . ' IN (' . implode(',', $friends) . '))) OR';
			}

			$privateBlog .= ' ((a.`access` = 40) AND (a.' . $db->nameQuote('created_by') .'=' . $this->my->id . '))';
			$privateBlog .= ')';
		} else {
			if ($this->my->id == 0) {
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		if ($privateBlog) {
			$query .= $privateBlog;
		}

		// do not show unpublished post
		$query .= ' AND a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);

		$state = array(EASYBLOG_POST_NORMAL);
		if ($this->_includeArchive) {
			$state[] = EASYBLOG_POST_ARCHIVED;
		}

		$query .= count($state) == 1 ? ' AND a.`state` = ' . $state[0] : ' AND a.`state` IN (' . implode(',', $state) . ')';

		// Ensure that post category is published
		$query .= ' AND cat.`published` = ' . $db->Quote(EASYBLOG_CATEGORY_PUBLISHED);

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		if (!EB::isFromAdmin()) {
			$filterLanguage = $this->app->getLanguageFilter();

			if ($filterLanguage) {
				$query  .= EBR::getLanguageQuery('AND', 'a.language');
			}
		}

		$query .= $queryWhere;

		if ($sort === 'score') {
			$query .= ' ) as x';
			$query .= ' ORDER BY `score` DESC';
		}

		if ($sort !== 'score') {
			$query .= ' ORDER BY '. $db->nameQuote('a.' . $sort) . ' ' . $ordering;
		}

		return $query;
	}

	public function getData()
	{
		if (empty($this->_data)) {

			$query = $this->_buildQuery();

			// $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

			// normalize search terms
			// set the flag to false is because we do not want to escape if the search term contain double quote
			// in this section we need find whether the blog title and content have the search term
			$text = $this->normalizeTerms(false);

			$words = explode(' ', $text);
			$newRows = array();

			$limit = $this->getState('limit', 0);
			$limitstart = $this->getState('limitstart', 0);

			$db = EB::db();
			$db->setQuery($query);

			$results = $db->loadObjectList();

			// skip this if do not return any result
			if (!$results) {
				return $results;
			}

			foreach ($results as $row) {

				// combine the blog title
				$content = $row->title . ' ' . $row->text;

				// Remove all the HTML content
				$content = preg_replace('/\s+/', ' ', strip_tags($content));

				// Determine if the search term still exist into the stripped HTML content
				$searchTermsExist = $this->searchTermsExist($content, $words);

				// Assign to the new array if search term exist into the blog content (stripped HTML)
				if ($searchTermsExist) {
					$newRows[] = $row;
				}
			}

			// set the total of the results
			$this->_total = count($newRows);

			// Determine if the result whether more than the current list limit we set
			// Then we need to render the paginations
			if ($limit > 0) {

				// returns selected parts of an array base on the current limitstart
				$this->_data = array_splice($newRows, $limitstart, $limit);

			} else {
				$this->_data = $newRows;
			}
		}

		return $this->_data;
	}

	public function searchText($text, $options = array())
	{
		if (empty($text)) {
			return false;
		}

		$limit = EB::normalize($options, 'limit', 0);
		if ($limit) {
			$this->input->set('limit', $limit);
		}

		// Set input query
		$this->input->set('query', $text);

		return $this->getData();
	}

	/**
	 * Normalize the search term
	 *
	 * @since   5.4.2
	 * @access  public
	 */
	public function normalizeTerms($escapeTerm = true)
	{
		$badchars = array('#', '>', '<', '\\', '=', '(', ')', '*', ',', '.', '%', '\'');

		// Retrieve the search keyword from the URL query string
		$terms = $this->input->get('query', '', 'string');

		// Replace for those bad characters
		$terms = trim(str_replace($badchars, '', $terms));

		// Ensure the terms convert to lowercase and support UTF-8
		$terms = trim(EBString::strtolower($terms));

		// Escapes a string for usage in an SQL statement
		if ($escapeTerm) {

			$db = EB::db();
			$terms = $db->getEscaped($terms);
		}

		return $terms;
	}

	/**
	 * Checks an object content and see whether still contain those search term (after stripping blog content of HTML).
	 *
	 * @since   5.4.2
	 * @access  public
	 */
	public function searchTermsExist($content, $searchTerms, $phrase = '')
	{
		if (!$content || !$searchTerms) {
			return false;
		}

		// Proceed this if somewhere pass in this specific phase then need to search exact the word
		// so far only search easyblog plugin might be pass in this phrase
		if ($phrase == 'exact' && is_string($searchTerms)) {

			// Determine if the content still contain those search terms
			$termExist = EBString::stristr($content, $searchTerms) !== false;

			if ($termExist) {
				return true;
			}

			return false;
		}

		// Ensure the search terms is array before proceed this
		if (is_array($searchTerms)) {

			foreach ($searchTerms as $term) {

				// Determine if the content still contain those search terms
				$termExist = EBString::stristr($content, $term) !== false;

				if ($termExist) {
					return true;
				}
			}
		}

		return false;
	}
}
