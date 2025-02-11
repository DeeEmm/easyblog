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

jimport('joomla.system.file');
jimport('joomla.system.folder');

class modEasyBlogPostMapHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	/**
	 * Retrieves a list of posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		$type = $this->params->get('type', 'recent');
		$type = $this->normalizeFilterType($type);

		// Get the query
		$query = $this->getPostsQuery($type);

		$db = EB::db();
		$db->setQuery($query);

		$posts = $db->loadObjectList();

		// Format the post
		$posts = $this->lib->processItems($posts);

		// Generate the tooltips for the map
		$posts = $this->generateTooltips($posts);

		return $posts;
	}

	/**
	 * Generates the sql query to retrieve posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function getPostsQuery($type)
	{
		$db = EB::db();

		$config = EB::config();
		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$joinQuery = '';

		$headQuery = 'SELECT a.* FROM ' . $db->qn('#__easyblog_post') . ' as a';

		if (!$showBlockedUserPosts) {
			// exclude blocked users pots #1978
			$headQuery .= ' INNER JOIN `#__users` as uu on a.`created_by` = uu.`id` and uu.`block` = 0';
		}

		// select valid latitude/longitude or address
		$query = ' WHERE ((TRIM(a.' . $db->qn('latitude') . ') != ' . $db->quote('') . ' AND TRIM(a.' . $db->qn('longitude') . ') != ' . $db->quote('') . ')';
		$query .= ' OR TRIM(a.' . $db->qn('address') . ') != ' . $db->quote('') . ')';
		$query .= ' AND a.' . $db->qn('published') . ' = ' . $db->quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' AND a.' . $db->qn('state') . ' = ' . $db->quote(EASYBLOG_POST_NORMAL);

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();

		if ($filterLanguage) {
			$query .= EBR::getLanguageQuery('AND', 'a.language');
		}

		if ($type == 'author') {
			$bloggers = $this->lib->join($this->params->get('bloggerid'));

			if (!empty($bloggers)) {
				$query .= ' AND a.' . $db->qn('created_by') . ' IN (' . $bloggers . ')';
			}
		}

		if ($type == 'category') {
			$categories = $this->lib->join($this->params->get('categoryid'));

			if (!empty($categories)) {
				$joinQuery .= ' INNER JOIN ' . $db->qn('#__easyblog_post_category') . ' as pc';
				$joinQuery .= ' ON pc.'. $db->qn('post_id') . ' = a.' . $db->qn('id');
				$query .= ' AND pc.' . $db->qn( 'category_id' ) . ' IN (' . $categories . ')';
			}
		}

		if ($type == 'tags') {
			$tags = $this->lib->join($this->params->get('tagid'));

			if (!empty($post_ids)) {
				$joinQuery .= ' INNER JOIN ' . $db->qn('#__easyblog_post_tag') . ' as pt';
				$joinQuery .= ' ON pt.'. $db->qn('post_id') . ' = a.' . $db->qn('id');
				$query .= ' AND pt' . $db->qn('tag_id') . ' IN (' . $tags . ')';
			}
		}

		if ($type == 'team') {
			$teams = $this->lib->join($this->params->get('teamids'));

			if (!empty($post_ids)) {
				$query .= ' AND a.' . $db->qn('source_type') . ' = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);
				$query .= ' AND a.' . $db->qn('source_id') . ' IN (' . $post_ids . ')';
			}
		}

		if ($type == 'recent') {
			$featured = $this->params->get('usefeatured', false);

			if ($featured) {
				$joinQuery .= ' INNER JOIN ' . $db->qn('#__easyblog_featured') . ' as f';
				$joinQuery .= ' ON f.'. $db->qn('content_id') . ' = a.' . $db->qn('id');
				$joinQuery .= ' AND f.'. $db->qn('type') . ' = ' . $db->Quote(EBLOG_FEATURED_BLOG);
			}
		}

		// always sort by latest
		$query .= ' ORDER BY a.' . $db->qn('created') . ' DESC';

		// set limit
		$count = (int) trim($this->params->get('count', 5));
		if ($count < 1) {
			$count = 5;
		}

		$query .= ' LIMIT ' . $count;

		// joins the strings.
		$query = $headQuery . $joinQuery . $query;

		return $query;
	}

	/**
	 * Generate tooltips for the posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function generateTooltips($posts)
	{
		foreach ($posts as $post) {

			ob_start();
			include(__DIR__ . '/tmpl/tooltip.php');
			$contents = ob_get_contents();
			ob_end_clean();

			$post->html = $contents;
		}

		return $posts;
	}

	/**
	 * Sort the posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function sortLocation($items)
	{
		usort($items, array('modEasyBlogPostMapSorter', 'latitudesort'));
		usort($items, array('modEasyBlogPostMapSorter', 'longitudesort'));

		return $items;
	}

	/**
	 * Given two location, determine if the location are the same
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function sameLocation($a, $b)
	{
		return ($a->latitude == $b->latitude && $a->longitude == $b->longitude);
	}

	/**
	 * Normalize legacy values for post types
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function normalizeFilterType($type)
	{
		if ($type == '0') {
			return 'recent';
		}

		if ($type == '1') {
			return 'author';
		}

		if ($type == '2') {
			return 'category';
		}

		if ($type == '3') {
			return 'tags';
		}

		if ($type == '4') {
			return 'team';
		}

		// Default filter type
		return $type;
	}
}

class modEasyBlogPostMapSorter
{
	// sort by location first
	static function customsort($a, $b, $field)
	{
		if ($a->$field == $b->$field) {
			return 0;
		}

		return ($a->$field > $b->$field)? -1 : 1;
	}

	static function latitudesort($a, $b)
	{
		return self::customsort($a, $b, 'latitude');
	}

	static function longitudesort($a, $b)
	{
		return self::customsort($a, $b, 'longitude');
	}
}
