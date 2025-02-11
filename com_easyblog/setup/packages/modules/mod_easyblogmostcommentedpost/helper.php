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

class modEasyBlogMostCommentedPostHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	public function getMostCommentedPost()
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$config = $this->lib->config;
		$count = (int) trim($this->params->get('count', 0));
		$categories = $this->params->get('catid');
		$interval = (int) $this->params->get('interval', 0);

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$catAccess = array();

		// Respect inclusion categories
		if (!empty($categories)) {

			if (!is_array($categories)) {
				$categories	= array($categories);
			}

			$catAccess['include'] = $categories;
		}

		$showprivate = $this->params->get('showprivate', true);

		$query = 'SELECT a.*, count(b.' . $db->quoteName('id') . ') as ' . $db->quoteName('comment_count');
		$query .= ' FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';

		if (!$showBlockedUserPosts) {
			//exlude blocked users posts
			$query .= ' INNER JOIN `#__users` as uu on a.`created_by` = uu.`id` and uu.`block` = 0';
		}

		$query .= '  LEFT JOIN ' . $db->quoteName('#__easyblog_comment') . ' AS b ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');
		$query .= ' WHERE a.' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' AND a.' . $db->quoteName('state') . ' = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		// Add interval checks
		if ($interval) {

			$now = EB::date()->toSql();
			$query .= ' AND b.' . $db->quoteName('created') . ' >= DATE_SUB(' . $db->Quote($now) . ', INTERVAL ' . $interval . ' DAY)';
		}

		if(!$showprivate)
			$query .= ' AND a.' . $db->quoteName('access') . ' = ' . $db->Quote('0');

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ((a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($config->get('main_includeteamblogpost')) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
		}

		$contributeSQL .= ')';

		$query .= $contributeSQL;

		// category access here
		$catLib = EB::category();
		if ($config->get('main_category_privacy')) {
			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $catAccess);
			$query .= ' AND (' . $catAccessSQL . ')';
		} else {
			$catAccessSQL = $catLib->genBasicSQL('a.`id`', $catAccess);
			if ($catAccessSQL) {
				$query .= ' AND ' . $catAccessSQL;
			}
		}

		$query .= ' GROUP BY a.' . $db->quoteName('id');
		$query .= ' HAVING (' . $db->quoteName('comment_count') . ' > 0)';
		$query .= ' ORDER BY ' . $db->quoteName('comment_count') . ' DESC';

		if ($count > 0) {
			$query .= ' LIMIT ' . $count;
		}

		$db->setQuery($query);

		$posts = $db->loadObjectList();

		return $posts;
	}
}
