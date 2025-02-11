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

class modTopBlogsHelper extends EasyBlog
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
	 * Retrieves a list of top posts from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		$db = EB::db();
		$order = trim($this->params->get('order', 'postcount_desc'));
		$count = (int) trim($this->params->get('count', 0));
		if ($count < 1) {
			$count = 5;
		}
		$showprivate = $this->params->get('showprivate', true);

		$sorting = $this->params->get('sorting', 'sum');

		$config = EB::config();
		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = 'select sum(b.`value`) as ratings';
		if ($sorting == 'avg') {
			$query = 'select avg(b.`value`) as ratings';
		}
		$query .= ' ,ax.* from';
		$query .= ' (select distinct a.* from `#__easyblog_post` as a';
		if (!$showBlockedUserPosts) {
			$query .= '		inner join `#__users` as uu on a.`created_by` = uu.`id` and uu.`block` = 0';
		}
		$query .= '		inner join `#__easyblog_post_category` as pc on a.`id` = pc.`post_id`';
		$query .= '		inner join `#__easyblog_category` as c on pc.`category_id` = c.`id`';

		$query .= '			WHERE a.' . $db->nameQuote('published') .'=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= '			AND a.' . $db->nameQuote('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		if (!$showprivate) {
			$query .= ' AND a.' . $db->nameQuote('access') . '=' . $db->Quote(0);
		}

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();

		if ($filterLanguage) {
		 	$query .= EBR::getLanguageQuery('AND', 'a.language');
		}

		// Respect inclusion categories
		$categories	= $this->params->get('catid');

		if (!empty($categories)) {
			$categories = explode(',', $categories);

			$query .= ' AND c.`id` IN (';

			if (!is_array($categories)) {
				$categories	= array($categories);
			}

			for ($i = 0; $i < count($categories); $i++) {
				$query	.= $db->Quote($categories[$i]);

				if (next($categories) !== false) {
					$query	.= ',';
				}
			}
			$query	.= ')';
		}

		$query .= ' AND a.' . $db->nameQuote('source_id') . '=' . $db->Quote('0');


		$query .= '		) as ax';
		$query .= '		inner join `#__easyblog_ratings` as b on ax.id = b.uid and b.type = ' . $db->Quote('entry');
		// $query .= ' where b.`value` is not null';
		$query .= ' group by b.`uid`';
		$query .= ' order by `ratings` desc';

		if (!empty($count)) {
			$query .= ' LIMIT ' . $count;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$posts = $this->lib->processItems($result);

		return $posts;
	}

}
