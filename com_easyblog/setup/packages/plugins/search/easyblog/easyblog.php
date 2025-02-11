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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

jimport('joomla.filesystem.file');

class plgSearchEasyblog extends JPlugin
{
	public function __construct(&$subject, $params)
	{
		$this->my = JFactory::getUser();

		if ($this->exists()) {
			$this->config = EB::config();
		}

		parent::__construct($subject, $params);
	}

	/**
	 * Retrieves the list of search areas
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public function onContentSearchAreas()
	{
		// Check if EasyBlog exists and is installed
		if (!self::exists()) {
			return array();
		}

		// Load site's language
		EB::loadLanguages();

		$areas = array('blogs' => JText::_('PLG_EASYBLOG_SEARCH_BLOGS'));

		return $areas;
	}

	/**
	 * When user performs a search
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$plugin	= JPluginHelper::getPlugin('search', 'easyblog');
		$params = EB::registry($plugin->params);

		// Check if EasyBlog exists and is installed
		if (!self::exists()) {
			return array();
		}

		// Get the list of search areas
		$searchAreas = self::onContentSearchAreas();

		if (is_array($areas) && !array_intersect($areas, array_keys($searchAreas))) {
			return array();
		}

		$text = trim($text);

		if ($text == '') {
			return array();
		}

		// Get search results
		$results = $this->getResult($text, $phrase, $ordering);

		if (!$results) {
			return array();
		}

		$newRows = array();

		foreach ($results as $row) {

			// combine the blog title
			$content = $row->title . ' ' . $row->text;

			// Remove all the HTML content
			$content = preg_replace('/\s+/', ' ', strip_tags($content));

			// Determine if the search term still exist into the stripped HTML content
			$searchTermsExist = $this->searchTermsExist($content, $text);

			// Assign to the new array if search term exist into the blog content (stripped HTML)
			if ($searchTermsExist) {
				$newRows[] = $row;
			}
		}

		if (!$newRows) {
			return array();
		}

		$posts = array();

		foreach ($newRows as &$row) {

			$post = EB::post($row->id);

			$row->section = JText::sprintf('PLG_EASYBLOG_SEARCH_BLOGS_SECTION', $post->getPrimaryCategory()->title);
			$row->href = $post->getPermalink();
			$row->image = $post->getImage('large', false, true);
		}

		return $newRows;
	}

	/**
	 * Determines if EasyBlog exists on the site.
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
		$enabled = JComponentHelper::isEnabled('com_easyblog');

		if (!JFile::exists($file) || !$enabled) {
			return false;
		}

		require_once($file);

		if (!EB::isFoundryEnabled()) {
			return false;
		}

		return true;
	}

	/**
	 * Performs the real searching for blog posts here.
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public function getResult($text, $phrase, $ordering)
	{
		$db = EB::db();
		$where	= array();
		$where2	= array();

		// used for privacy
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$excludeCats = array();

		$languageTag = FH::getCurrentLanguageTag();
		$isSiteMultilingualEnabled = FH::isMultiLingual();

		// Exact matches
		if ($phrase == 'exact') {

			$searchText = $db->Quote('%' . $db->escape($text, true) . '%', false);

			$where[] = 'a.`title` LIKE ' . $searchText;
			$where[] = 'a.`content` LIKE ' . $searchText;
			$where[] = 'a.`intro` LIKE ' . $searchText;
			$where2 = '(t.`title` LIKE ' . $searchText . ')';
			$where = '(' . implode(') OR (', $where) . ')';

		} else {

			$text = $this->normalizeTerms($text);

			$words = explode(' ', $text);
			$wheres = array();
			$where2 = array();
			$wheres2 = array();

			foreach ($words as $word) {

				$word = $db->Quote( '%'. $db->escape($word, true) .'%', false );

				$where[] = 'a.`title` LIKE ' . $word;
				$where[] = 'a.`content` LIKE ' . $word;
				$where[] = 'a.`intro` LIKE ' . $word;

				$where2[] = 't.title LIKE ' . $word;

				$wheres[] = implode(' OR ', $where );
				$wheres2[] = implode(' OR ', $where2);
			}

			$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			$where2	= '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres2 ) . ')';
		}

		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled('system', 'eventeasyblog');

		// Need to check if the site installed jomsocial.
		$isJSInstalled = EB::jomsocial()->exists();

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

		// Get teamblogs id.
		$query = '';

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

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

		// Comment out this sorted by score (title, intro and content), if the search query match with the blog title, it will put the high score. #2486

		// $query = 'SELECT * from (';
		// $query .= 'SELECT a.*, CONCAT(a.`content`, a.`intro`) AS text, "2" as browsernav,';

		// $textquery = $db->Quote('%'.$db->getEscaped($text, true).'%', false);
		// $caseQuery = '((CASE WHEN a.`title` = ' . $db->Quote($text) . ' THEN 4 ELSE 0 END) + (CASE WHEN a.`title` LIKE ' . $textquery . ' THEN 3 ELSE 0 END)';
		// $caseQuery .= ' + (CASE WHEN a.`content` LIKE ' . $textquery . ' THEN 2 ELSE 0 END) + (CASE WHEN a.`intro` LIKE ' . $textquery . ' THEN 2 ELSE 0 END)) as score';

		// $query .= $caseQuery;

		$query	= 'SELECT a.*, CONCAT(a.`content` , a.`intro`) AS text , "2" as browsernav';

		$query	.= ' FROM `#__easyblog_post` as a USE INDEX (`easyblog_post_searchnew`) ';

		$query	.= ' WHERE (' . $where;
		$query	.= ' OR a.`id` IN( ';
		$query	.= '		SELECT tp.`post_id` FROM `#__easyblog_tag` AS t ';
		$query	.= '		INNER JOIN `#__easyblog_post_tag` AS tp ON tp.`tag_id` = t.`id` ';
		$query	.= '		WHERE ' . $where2;
		$query	.= '))';

		// Guests should only see public post.
		if ($this->my->guest) {
			$query .= ' AND a.`access` = ' . $db->Quote('0');
		}

		// Do not render unpublished posts
		$query .= ' AND a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' AND a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		// Filter by language.
		if (!FH::isFromAdmin() && $isSiteMultilingualEnabled) {
			$query .= ' AND a.' . $db->qn('language') . '=' . $db->Quote($languageTag);
		}

		$query .= $queryWhere;

		// Base on score result
		// $query .= ' ) as x';

		if ($ordering == 'oldest') {
			// Base on score result
			// $query .= ' ORDER BY `score` ASC';
			// $query .= ' ORDER BY x.`publish_up` ASC, x.`score` ASC';

			$query .= ' ORDER BY a.`created` ASC';
		}

		if ($ordering == 'newest') {
			// Base on score result
			// $query .= ' ORDER BY `score` DESC';
			// $query .= ' ORDER BY x.`publish_up` DESC, x.`score` DESC';

			$query .= ' ORDER BY a.`created` DESC';
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Checks an object content and see whether still contain those search term (after stripping blog content of HTML).
	 *
	 * @since	5.4.2
	 * @access	public
	 */
	public function searchTermsExist($content, $searchTerms, $phrase = '')
	{
		if ($phrase != 'exact') {
			$searchTerms = explode(' ', $searchTerms);
		}

		$model = EB::model('Search');
		$searchTermsExist = $model->searchTermsExist($content, $searchTerms, $phrase);

		return $searchTermsExist;
	}

	/**
	 * Normalize the search term
	 *
	 * @since   5.4.3
	 * @access  public
	 */
	public function normalizeTerms($terms)
	{
		$db = EB::db();
		$badchars = array('#', '>', '<', '\\', '=', '(', ')', '*', ',', '.', '%', '\'');

		// Replace for those bad characters
		$terms = trim(str_replace($badchars, '', $terms));

		// Ensure the terms convert to lowercase and support UTF-8
		$terms = trim(EBString::strtolower($terms));

		// Escapes a string for usage in an SQL statement
		$terms = $db->getEscaped($terms);

		return $terms;
	}
}
