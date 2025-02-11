<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class modEasyBlogLatestCommentHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	public function getLatestComment()
	{
		$db = EB::db();
		$config = EB::config();

		$count = (int) trim($this->params->get('count', 5));
		if ($count < 0) {
			$count = 5;
		}

		$showprivate = $this->params->get('showprivate', true);

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = 'SELECT ' . $db->qn('b.title') . ' as `blog_title`, ' . $db->qn('b.created_by') . ' as `author_id`, ' . $db->qn('b.category_id') . ' as `category_id`, a.*';
		$query .= ' from `#__easyblog_comment` as a';
		$query .= '   left join `#__easyblog_post` as b';
		$query .= '   on a.`post_id` = b.`id`';

		if (!$showBlockedUserPosts) {
			$query .= ' left join `#__users` as uu on a.`created_by` = uu.`id`';
		}

		$query .= ' where b.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' and b.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= ' and a.`published`=' . $db->Quote( '1' );

		// Respect privacy
		if (!$showprivate) {
			$query .= ' and b.`access` = ' . $db->Quote('0');

			// category access here
			$config = EB::config();

			if ($config->get('main_category_privacy')) {
				$catAccess = array();
				$catLib = EB::category();
				$catAccessSQL = $catLib->genAccessSQL('b.`id`', $catAccess);
				$query .= ' AND (' . $catAccessSQL . ')';
			}

			$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
			$isEventPluginInstalled = JPluginHelper::isEnabled('system', 'eventeasyblog');
			$isJSInstalled = false; // need to check if the site installed jomsocial.

			$file = JPATH_ROOT . '/components/com_community/libraries/core.php';
			$isJSExists = JFile::exists($file);

			if ($isJSExists) {
				$isJSInstalled = true;
			}

			$includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
			$includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;

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

			// Only process the contribution sql for EasySocial if EasySocial really exists.
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
			$query .= $contributeSQL;
		}

		if (!$showBlockedUserPosts) {
			$query .= ' and (uu.block = 0 OR uu.id IS NULL)';
		}

		$query .= ' order by a.`created` desc';

		if ($count) {
			$query .= ' limit ' . $count;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (count($result) > 0) {
			for ($i = 0; $i < count($result); $i++) {
				$row =& $result[ $i ];
				$row->author = EB::user($row->created_by);
				$row->dateString = EB::date($row->created)->format(JText::_('DATE_FORMAT_LC3'));
			}
		}
		return $result;
	}

	public function getJComment()
	{
		$db = EB::db();

		$count = (int) trim($this->params->get('count', 5));
		if ($count < 0) {
			$count = 5;
		}

		$q = [];
		$q[] = "SELECT * FROM `#__jcomments`";
		$q[] = "WHERE `published` = " . $db->Quote(1);
		$q[] = "AND `object_group` = " . $db->Quote('com_easyblog');
		$q[] = "ORDER BY `date` DESC";
		if ($count) {
			$q[] = "LIMIT 0, " . $count;
		}

		$query = implode(" ", $q);

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$comments = array();

		if ($rows) {
			foreach ($rows as $row) {
				$row->author = EB::user($row->userid);
				$row->created_by = $row->userid;
				$row->post_id = $row->object_id;

				$blog = EB::table('Blog');
				$blog->load($row->object_id);

				$row->blog_title = $blog->title;
				$row->dateString = $row->date;
				$comments[] = $row;
			}
		}

		return $comments;
	}
}
