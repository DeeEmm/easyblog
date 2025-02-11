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

class EasyBlogModelBlogger extends EasyBlogAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$jConfig = EB::jConfig();
		$limit = $jConfig->get('list_limit');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function preload($authorIds = [])
	{
		if (!$authorIds) {
			return $authorIds;
		}

		$db = EB::db();

		$query = array();

		$query[] = 'SELECT b.* FROM ' . $db->qn('#__users') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_users') . ' AS b';
		$query[] = 'ON a.' . $db->qn('id') . '= b.' . $db->qn('id');
		$query[] = ' where a.`id` IN (' . implode(',', $authorIds) . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$authors = array();

		foreach ($result as $item) {
			$author = EB::table('Profile');
			$author->bind($item);

			$authors[$item->id] = $author;
		}

		return $authors;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Retrieves a list of authors used in backend posts listing author filters
	 *
	 * @since	6.0.0
	 * @access	private
	 */
	public function getPostAuthors()
	{
		$db = EB::db();
		$config = EB::config();

		$query  = 'select distinct a.`id`, a.`name`';
		$query .= '	from `#__users` as a';
		$query .= ' 	inner join `#__easyblog_post` as p on a.`id` = p.`created_by`';
		$query .= '	ORDER BY a.`name` ASC';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Method to get blog posts listing
	 *
	 * @access public
	 */
	public function getData($sort = 'latest', $limit = 0, $filter='showallblogger')
	{
		$db = EB::db();
		$config = EB::config();
		$nameDisplayFormat = $config->get('layout_nameformat');
		$limitSQL = '';

		if (!is_null($limit)) {
			$limit = ($limit == 0) ? $this->getState('limit') : $limit;
			$limitstart = $this->getState('limitstart');
			$limitSQL = ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$aclQuery = EB::AclHelper()->genIsbloggerSQL();

		$headQuery  = 'select count( p.id ) as `totalPost`, MAX(p.`created`) as `latestPostDate`, COUNT( DISTINCT(g.content_id) ) as `featured`,';
		$headQuery  .= ' a.`id`, b.`nickname`, b.avatar, b.description, a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`';


		$query = '	from `#__users` as a';
		$query .= ' 	left join `#__easyblog_post` as p on a.`id` = p.`created_by`';
		$query .= ' 		and `p`.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' 		and p.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= ' 	left JOIN `#__easyblog_users` AS `b` ON p.`created_by` = b.`id`';
		$query .= ' 	left join `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		$query .= ' where (' . $aclQuery . ')';
		$query .= ' group by a.`id`';
		if ($filter == 'showbloggerwithpost') {
			$query .= ' having (count(p.id) > 0)';
		}

		// prepare count query
		$cntQuery = 'select count(a.`id`)';
		$cntQuery .= $query;

		// main query
		$query = $headQuery . $query;

		switch($sort)
		{
			case 'latestpost' :
				$query .= '	ORDER BY `latestPostDate` DESC';
				break;
			case 'latest' :
				$query .= '	ORDER BY a.`registerDate` DESC';
				break;
			case 'active' :
				$query	.= ' ORDER BY a.`lastvisitDate` DESC';
				break;
			case 'alphabet' :
				if($nameDisplayFormat == 'name')
					$query .= '	ORDER BY a.`name` ASC';
				else if($nameDisplayFormat == 'username')
					$query .= '	ORDER BY a.`username` ASC';
				else
					$query .= '	ORDER BY b.`nickname` ASC';
				break;
			default	:
				break;
		}

		$query .= $limitSQL;

		$db->setQuery( $query );
		$results = $db->loadObjectList();

		// this getData method only used in backend thus we do not need to check for falang. #39

		// execute count query to get the total number.
		$db->setQuery( $cntQuery );
		$this->_total = $db->loadResult();

		if (empty($this->_pagination)) {
			$limitstart = $this->getState('limitstart');
			$this->_pagination = EB::Pagination( $this->_total , $limitstart , $limit );
		}

		return $results;
	}

	public function isBloggerSubscribedUser($bloggerId, $userId, $email = null)
	{
		$db	= EB::db();

		$query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query  .= ' WHERE `uid` = ' . $db->Quote($bloggerId);
		$query  .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);

		if ($email) {
			$query  .= ' AND (`user_id` = ' . $db->Quote($userId);
			$query  .= ' OR `email` = ' . $db->Quote($email) .')';
		} else {
			$query  .= ' AND `user_id` = ' . $db->Quote($userId);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function isBloggerSubscribedEmail($bloggerId, $email)
	{
		$db	= EB::db();

		// Let's check if this blogger data cached or not.
		if (EB::cache()->exists($bloggerId, 'bloggers')) {
			$data = EB::cache()->get($bloggerId, 'bloggers');

			if (isset($data['subs'])) {
				return $data['subs'];
			} else {
				return false;
			}
		}

		// PHP 8.1 compatibility
		if (is_null($email)) {
			$email = '';
		}

		$query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query  .= ' WHERE `uid` = ' . $db->Quote($bloggerId);
		$query  .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);
		$query  .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function addBloggerSubscription($bloggerId, $email, $userId = '0', $fullname = '')
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$date       = EB::date();
			$subscriber = EB::table('Subscriptions');

			$subscriber->uid = $bloggerId;
			$subscriber->utype = EBLOG_SUBSCRIPTION_BLOGGER;


			$subscriber->email    	= $email;
			if($userId != '0')
				$subscriber->user_id    = $userId;

			$subscriber->fullname	= $fullname;
			$subscriber->created  	= $date->toSql();
			$state = $subscriber->store();

			if ($state) {
				$profile = EB::user($bloggerId);

				// lets send confirmation email to subscriber.
				$helper 	= EB::subscription();
				$template 	= $helper->getTemplate();

				$template->uid 			= $subscriber->id;
				$template->utype 		= 'bloggersubscription';
				$template->user_id 		= $subscriber->user_id;
				$template->uemail 		= $email;
				$template->ufullname 	= $fullname;
				$template->ucreated 	= $subscriber->created;
				$template->targetname 	= $profile->getName();
				$template->targetlink	= EBR::getRoutedURL('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $bloggerId, false, true );

				$helper->addMailQueue( $template );
			}
		}
	}

	function updateBloggerSubscriptionEmail($sid, $userid, $email)
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$subscriber = EB::table('Subscriptions');
			$subscriber->load($sid);
			$subscriber->email = $email;
			$subscriber->user_id = $userid;
			$subscriber->store();
		}
	}

	function getBlogggerSubscribers($bloggerId)
	{
		$db = EB::db();

		$query  = "SELECT *. 'bloggersubscription' as `type` FROM `#__easyblog_subscriptions`";
		$query .= " WHERE `uid` = " . $db->Quote($bloggerId);
		$query .= " AND `utype` = " . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);

		//echo $query . '<br/><br/>';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves a list of recent posts from an author
	 *
	 * @since	6.0.0
	 * @access	private
	 */
	public function getRecentPosts($authorId, $limit, $excludePostId = null)
	{
		$recent = [];

		$model = EB::model('Blog');
		$result = $model->getBlogsBy('blogger', $authorId, 'latest', $limit, EBLOG_FILTER_PUBLISHED, false, false, $excludePostId ? [$excludePostId] : null);

		if (!$result) {
			return $recent;
		}

		$posts = [];

		foreach ($result as $row) {
			$item = EB::post();
			$item->bind($row, ['force' => true]);

			$posts[] = $item;
		}

		return $posts;
	}

	/**
	 * Retrieves a list of bloggers from the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getSiteAuthors($limit = 0, $limitstart = 0)
	{
		$db = EB::db();
		$limit = ($limit == 0) ? $this->getState('limit') : $limit;

		if (!$limitstart) {
			$limitstart = $this->getState('limitstart');
		}

		// Generate the acl query
		$aclQuery = EB::AclHelper()->genIsbloggerSQL();

		// Build the pagination query
		$query = array();
		$query[] = 'SELECT COUNT(1)';
		$query[] = 'FROM `#__users` as a';
		$query[] = 'inner JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';
		$query[] = 'LEFT JOIN `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		$query[] = 'WHERE (' . $aclQuery . ')';
		$query = implode(' ', $query);

		$db->setQuery($query);
		$total = (int) $db->loadResult();


		// Retrieve the total count
		$query = array();
		$query[] = 'SELECT a.`id`, b.`nickname`, a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`, b.`permalink`';
		$query[] = 'FROM `#__users` as a';
		$query[] = 'inner JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';
		$query[] = 'LEFT JOIN `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		$query[] = 'WHERE (' . $aclQuery . ')';
		$query[] = 'ORDER BY a.`name` ASC';
		$query[] = 'LIMIT ' . $limitstart . ',' . $limit;
		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		$this->_pagination = EB::pagination($total, $limitstart, $limit);

		return $result;
	}

	/**
	 * Retrieves a list of bloggers from the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getBloggers($sort = 'latest', $limit = 0, $filter='showallblogger', $search = '', $inclusion = array(), $exclusion = array(), $featuredOnly = '', $ignorePagination = false)
	{
		$db = EB::db();
		$config	= EB::config();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);
		$nameDisplayFormat = $config->get('layout_nameformat');

		$limit = ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');
		$limitstart = $limitstart < 0 ? 0 : $limitstart;

		if ($ignorePagination) {
			$limitSQL = ' LIMIT ' . $limit;
		} else {
			$limitSQL = ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$excludedQuery = '';
		$excluded = $config->get('layout_exclude_bloggers');

		// check if there is exclusion from the backend settings OR from the parameter
		if (!empty($excluded) || !empty($exclusion)) {

			$tmp = array();
			$excluded = EBString::trim($excluded);

			if ($excluded) {
				$tmp = explode(',', $excluded);
			}

			// If caller passed in an exclusion list, we need to merge them both
			if (!empty($exclusion)) {
				$tmp = array_merge($tmp, $exclusion);
			}

			$values	= array();

			foreach ($tmp as $id) {
				$values[] = $db->Quote($id);
			}

			$excludedQuery = ' AND a.`id` NOT IN (' . implode( ',' , $values ) . ')';
		}

		//inclusion blogger
		$includedQuery = '';
		if (!empty($inclusion)) {

			$values	= array();

			foreach ($inclusion as $id) {
				$values[] = $db->Quote($id);
			}

			$includedQuery = ' AND a.id IN (' . implode(',', $values) . ')';

		}

		$searchQuery = '';
		if (!empty($search)) {
			$searchQuery .= ' AND ';

			switch ($nameDisplayFormat) {
				case 'name':
					$searchQuery .= '`name` LIKE ' . $db->Quote('%' . $search . '%' );
				break;
				case 'username':
					$searchQuery .= '`username` LIKE ' . $db->Quote('%' . $search . '%');
				break;
				default:
					$searchQuery .= '`nickname` LIKE ' . $db->Quote('%' . $search . '%');
				break;
			}
		}

		$aclQuery = EB::AclHelper()->genIsbloggerSQL();

		$queryHead = 'select count( p.id ) as `totalPost`, MAX(p.`created`) as `latestPostDate`, COUNT(g.content_id) as `featured`,';
		$queryHead .= ' a.`id`, IFNULL(b.`nickname`, a.name) as `nickname`, a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`,';
		$queryHead .= ' IFNULL(b.`permalink`, REPLACE(a.`username`, \' \', \'-\')) as `permalink`, IFNULL(oa.`id`, 0) as `hasTwitter`';

		$mainCntQuery = 'select a.`id`';

		$query = '	from `#__users` as a';
		$query .= ' 	left JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';

		// if ($filter == 'showallblogger') {
		// 	$query .= ' 	left join `#__easyblog_post` as p on a.`id` = p.`created_by`';
		// 	$query .= ' 		and `p`.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		// 	$query .= ' 		and p.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		// } else {
		// 	$query .= ' 	inner join `#__easyblog_post` as p on a.`id` = p.`created_by`';
		// }

		$query .= ' 	left join `#__easyblog_post` as p on a.`id` = p.`created_by`';
		$query .= ' 		and `p`.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' 		and p.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query .= ' 	left join `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		$query .= ' 	left join `#__easyblog_oauth` AS `oa` ON a.`id`= oa.`user_id` AND oa.`type`= ' . $db->Quote('twitter');

		$query .= ' where (' . $aclQuery . ')';

		// if ($filter == 'showbloggerwithpost') {
		// 	$query .= ' and `p`.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		// 	$query .= ' and p.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		// }

		$query .= $excludedQuery;
		$query .= $includedQuery;
		$query .= $searchQuery;

		if (!$showBlockedUserPosts) {
			// exclude blocked users #1978
			$query .= ' and a.`block` = 0';
		}

		if ($config->get('main_category_privacy')) {
			// if a post has multiple categories and one of the category is not accessible, we should exclude this post. #790
			$gids = EB::getUserGids('', true);

			$query .= " and not exists (";
			$query .= "	select acp2.post_id from `#__easyblog_post_category` as acp2";
			$query .= "		inner join  `#__easyblog_category` as cat2 on acp2.`category_id` = cat2.`id`";
			$query .= "	where acp2.`post_id` = p.`id`";
			$query .= "	and ( ";
			$query .= "	  ( cat2.`private` = 1 and (" . $this->my->id . " = 0) ) OR ";
			$query .= "	  ( cat2.`private` = 2 and (select count(1) from `#__easyblog_category_acl` as cacl2 where cacl2.`category_id` = cat2.`id` and cacl2.`acl_id` = " . CATEGORY_ACL_ACTION_VIEW . " and cacl2.`content_id` IN ($gids)) = 0 )";
			$query .= "   )";
			$query .= ")";
		}

		$query .= ' group by a.`id`';

		if ($filter == 'showbloggerwithpost' && $featuredOnly) {
			$query .= ' having (count(p.id) > 0 and count(g.content_id) > 0)';
		} else if ($filter == 'showbloggerwithpost' && !$featuredOnly) {
			$query .= ' having (count(p.id) > 0)';
		} else if ($filter != 'showbloggerwithpost' && $featuredOnly) {
			$query .= ' having (count(g.content_id) > 0)';
		}

		// prepare select count query. #39
		$mainCntQuery = $mainCntQuery . $query;

		// now append the select heading. #39
		$query = $queryHead . $query;

		switch ($sort) {
			case 'featured':
				$query .= ' ORDER BY `featured` DESC';
				break;
			case 'latestpost' :
				$query .= '	ORDER BY `latestPostDate` DESC';
				break;
			case 'latest' :
				$query .= '	ORDER BY a.`id` DESC';
				break;
			case 'postcount' :
				$query .= '	ORDER BY `totalPost` DESC';
				break;
			case 'active' :
				$query .= ' ORDER BY a.`lastvisitDate` DESC';
				break;
			case 'ordering' :
				$query .= ' ORDER BY b.`ordering` ASC';
				break;
			case 'alphabet' :
				if ($nameDisplayFormat == 'name') {
					$query .= '	ORDER BY a.`name` ASC';
				} else if($nameDisplayFormat == 'username') {
					$query .= '	ORDER BY a.`username` ASC';
				} else {
					$query .= '	ORDER BY b.`nickname` ASC';
				}
				break;
			default:
				break;
		}

		$query .= $limitSQL;

		// echo str_ireplace('#__', 'jos_', $query);exit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		// now execute found_row() to get the number of records found.
		// $cntQuery = 'select FOUND_ROWS()';

		// the way falang perform translation will mess up the found_rows. the only solution is to run select count() #39
		// if (EB::isFalangActivated()) {
			$cntQuery = 'select count(1) from (' . $mainCntQuery . ') as x';
		// }

		$db->setQuery($cntQuery);
		$this->_total = $db->loadResult();

		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);
		}

		return $results;
	}

	public function getTagUsed($bloggerId, $limit = null)
	{
		$db = EB::db();

		// Cache check
		if (EB::cache()->exists($bloggerId, 'bloggers')) {
			$data = EB::cache()->get($bloggerId, 'bloggers');

			if (isset($data['tag'])) {
				return $data['tag'];
			} else {
				return array();
			}
		}

		$query  = 'select distinct a.* from `#__easyblog_tag` as a';
		$query  .= ' inner join `#__easyblog_post_tag` as b on a.`id` = b.`tag_id`';
		$query  .= ' inner join `#__easyblog_post` as c on b.`post_id` = c.`id`';
		$query	.= ' where c.`created_by` = ' . $db->Quote($bloggerId);
		$query  .= ' and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query  .= ' and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		if ($limit) {
			$query .= ' LIMIT ' . $limit;
		}

		$db->setQuery($query);

		$result	= $db->loadObjectList();
		return $result;
	}

	public function getCategoryUsed($bloggerId, $limit = null)
	{
		$db = EB::db();

		// Cache check
		if (EB::cache()->exists($bloggerId, 'bloggers')) {
			$data = EB::cache()->get($bloggerId, 'bloggers');

			if (isset($data['category'])) {
				return $data['category'];
			}
		}

		$query  = 'select distinct a.*, count(b.`id`) as `post_count` from `#__easyblog_category` as a';
		$query  .= ' inner join `#__easyblog_post_category` as b ON a.`id` = b.`category_id`';
		$query  .= ' inner join `#__easyblog_post` as c ON b.`post_id` = c.`id`';
		$query  .= ' where c.`created_by` = ' . $db->Quote($bloggerId);
		$query  .= ' and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query  .= ' and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query  .= ' group by a.id';
		$query  .= ' order by null';

		if ($limit) {
			$query .= ' LIMIT ' . $limit;
		}

		$db->setQuery($query);

		$result	= $db->loadObjectList();
		return $result;
	}

	/**
	 * Retrieves the total number of posts created by an author.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getTotalBlogCreated($id)
	{
		$db = EB::db();
		$config = EB::config();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_post') . ' AS a';
		$query[] = 'WHERE a.' . $db->qn('created_by') . '=' . $db->Quote($id);
		$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		// category access here
		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccess = array();
			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $catAccess);
			$query[] = ' AND (' . $catAccessSQL . ')';
		}

		// Check against author_alias column
		if ($this->my->id != $id) {
			$query[] = ' AND (a.' . $db->qn('author_alias') . ' IS NULL OR a.' . $db->qn('author_alias') . ' = ' . $db->Quote('') . ') ';
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result	= $db->loadResult();
		return $result;
	}


	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function preloadPosts($bloggerIds, $limit = null)
	{
		$db = EB::db();
		$config = EB::config();

		if (is_null($limit)) {
			$limit = EB::call('Pagination', 'getLimit');
		}

		$gids = EB::getUserGids('', true);

		// Determines if this is currently on blogger mode
		$isBloggerMode = EBR::isBloggerMode();

		$sort = $config->get('layout_postsort', 'DESC');
		$ordering = $config->get('layout_postorder', 'created');

		// Ordering column should be publish_up if the ordering is configured to be publishing date
		if ($ordering == 'published') {
			$ordering = 'publish_up';
		}

		$query = array();

		$i = 1;
		foreach ($bloggerIds as $bid) {

			$p = 'p'.$i;
			$f = 'f'.$i;

			$isJSGrpPluginInstalled = false;
			$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
			$isEventPluginInstalled = JPluginHelper::isEnabled('system' , 'eventeasyblog');
			$isJSInstalled  = false; // need to check if the site installed jomsocial.

			if (EB::jomsocial()->exists()) {
				$isJSInstalled = true;
			}

			$includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
			$includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;

			// contribution type sql
			$contributor = EB::contributor();
			$contributeSQL = " AND (($p.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ") ";
			if ($config->get('main_includeteamblogpost')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, $p);
			}
			if ($includeJSEvent) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, $p);
			}
			if ($includeJSGrp) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, $p);
			}

			// Test if easysocial exists on the site
			if (EB::easysocial()->exists()) {
				if (EB::easysocial()->isBlogAppInstalled('group')) {
					$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, $p);
				}

				if (EB::easysocial()->isBlogAppInstalled('page')) {
					$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE, $p);
				}

				if (EB::easysocial()->isBlogAppInstalled('event')) {
					$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, $p);
				}
			}

			$contributeSQL .= ")";

			$tmp = "(select distinct $p.*, " . $db->Quote($bid) . " as `author_id`, IFNULL($f.`id`, 0) as `featured`";
			$tmp .= "   from `#__easyblog_post` as $p";
			$tmp .= " LEFT JOIN `#__easyblog_featured` AS $f";
			$tmp .= "   ON $p.`id` = $f.`content_id` AND $f.`type` = " . $db->Quote('post');
			$tmp .= " where $p.created_by = " . $db->Quote($bid);

			$tmp .= " and $p.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$tmp .= " and $p.`state` = " . $db->Quote(EASYBLOG_POST_NORMAL);

			// If user is a guest, ensure that they can really view the blog post
			// if ($this->my->guest) {
			// 	$tmp .= " AND $p." . $db->qn('access') . " = " . $db->Quote(BLOG_PRIVACY_PUBLIC);
			// }


			// Blog privacy setting
			// @integrations: jomsocial privacy
			$file = JPATH_ROOT . '/components/com_community/libraries/core.php';

			$easysocial = EB::easysocial();
			$jomsocial = EB::jomsocial();

			if ($config->get('integrations_es_privacy') && $easysocial->exists() && !FH::isSiteAdmin()) {
				$esPrivacyQuery = $easysocial->buildPrivacyQuery($p);
				$queryPrivacy = $esPrivacyQuery;

				$tmp .= $queryPrivacy;

			} else if ($config->get('main_jomsocial_privacy') && $jomsocial->exists() && !FH::isSiteAdmin()) {
				require_once($file);

				$my = JFactory::getUser();
				$jsFriends = CFactory::getModel('Friends');
				$friends = $jsFriends->getFriendIds($my->id);
				array_push($friends, $my->id);

				// Insert query here.
				$queryPrivacy = " AND (";
				$queryPrivacy .= " ($p.`access`= 0) OR";
				$queryPrivacy .= " (($p.`access` = 20) AND (" . $db->Quote($my->id) . " > 0)) OR";

				if (empty($friends)) {
					$queryPrivacy .= " (($p.`access` = 30) AND (1 = 2)) OR";
				}
				else
				{
					$queryPrivacy .= " (($p.`access` = 30) AND ($p." . $db->nameQuote('created_by') . " IN (" . implode(",", $friends) . "))) OR";
				}

				$queryPrivacy .= " (($p.`access` = 40) AND ($p." . $db->nameQuote('created_by') ."=" . $my->id . "))";
				$queryPrivacy .= ")";

				$tmp .= $queryPrivacy;

			} else if ($this->my->id == 0) {

				$queryPrivacy = " AND $p.`access` = " . $db->Quote(BLOG_PRIVACY_PUBLIC);
				$tmp .= $queryPrivacy;
			}


			// Ensure that the blog posts is available site wide
			$tmp .= $contributeSQL;

			// Filter by language
			$language = EB::getCurrentLanguage();

			if ($language) {
				$tmp .= " AND ($p." . $db->qn('language') . "=" . $db->Quote($language) . " OR $p." . $db->qn('language') . "=" . $db->Quote('*') . " OR $p." . $db->qn('language') . "=" . $db->Quote('') . ")";
			}

			if ($config->get('main_category_privacy')) {
				// if a post has multiple categories and one of the category is not accessible, we should exclude this post. #790
				$acp = 'acp' . $i;
				$cat = 'cat' . $i;

				$tmp .= " and not exists (";
				$tmp .= "   select $acp.post_id from `#__easyblog_post_category` as $acp";
				$tmp .= "       inner join  `#__easyblog_category` as $cat on $acp.`category_id` = $cat.`id`";
				$tmp .= "   where $acp.`post_id` = $p.`id`";
				$tmp .= "   and (";
				$tmp .= "       ($cat.`private` = 1 and (" . $this->my->id . " = 0)) OR ";
				$tmp .= "       ($cat.`private` = 2 and (select count(1) from `#__easyblog_category_acl` as cacl2 where cacl2.`category_id` = $cat.`id` and cacl2.`acl_id` = " . CATEGORY_ACL_ACTION_VIEW . " and cacl2.`content_id` IN ($gids)) = 0)";
				$tmp .= "  )";
				$tmp .= ")";
			}

			$queryOrder = ' ORDER BY';
			switch ($ordering) {
				case 'published':
					$queryOrder .= " $p.`publish_up` " . $sort;
					break;
				case 'popular':
					$queryOrder .= " $p.`hits` " . $sort;
					break;
				case 'active':
					$queryOrder .= " $p.`publish_down` " . $sort;
					break;
				case 'alphabet':
					$queryOrder .= " $p.`title` " . $sort;
					break;
				case 'modified':
					$queryOrder .= " $p.`modified` " . $sort;
					break;
				case 'latest':
				default :
					$queryOrder .= " $p.`created` " . $sort;
					break;
			}

			$tmp .= $queryOrder;

			$tmp .= " limit " . $limit . ")";

			$query[] = $tmp;

			$i++;
		}

		$query = implode(' UNION ALL ', $query);

		// echo $query;exit;

		// here we sort the results. #1050
		// $query = "select * from (" . $query . ") as xx";
		// $query .= " ORDER BY xx." . $db->quoteName($ordering) . " " . $sort;

		// $queryOrder = ' ORDER BY';
		// switch ($ordering) {
		// 	case 'published':
		// 		$queryOrder .= ' xx.`publish_up` ' . $sort;
		// 		break;
		// 	case 'popular':
		// 		$queryOrder .= ' xx.`hits` ' . $sort;
		// 		break;
		// 	case 'active':
		// 		$queryOrder .= ' xx.`publish_down` ' . $sort;
		// 		break;
		// 	case 'alphabet':
		// 		$queryOrder .= ' xx.`title` ' . $sort;
		// 		break;
		// 	case 'modified':
		// 		$queryOrder .= ' xx.`modified` ' . $sort;
		// 		break;
		// 	case 'latest':
		// 	default :
		// 		$queryOrder .= ' xx.`created` ' . $sort;
		// 		break;
		// }

		// $query .= $queryOrder;

		// echo $query;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$posts = array();

		if ($results) {
			foreach($results as $row) {
				$posts[$row->author_id][] = $row;
			}
		}

		return $posts;
	}




	/**
	 * preload tag used by bloggers.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function preloadTagUsed($bloggerIds, $isCount = false) {

		$db = EB::db();

		$query  = 'select distinct a.*, c.`created_by` as `author_id`';

		if ($isCount) {
			$query = 'select count(distinct a.id) as `cnt`, c.`created_by` as `author_id`';
		}

		$query 	.= ' from `#__easyblog_tag` as a';
		$query  .= ' 	inner join `#__easyblog_post_tag` as b on a.`id` = b.`tag_id`';
		$query  .= ' 	inner join `#__easyblog_post` as c on b.`post_id` = c.`id`';
		$query	.= ' where c.`created_by` IN (' . implode(',', $bloggerIds) . ')';
		$query  .= ' and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query  .= ' and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		if ($isCount) {
			$query .= ' group by c.`created_by`';
			$query .= ' order by null';
		}

		$db->setQuery($query);

		$results	= $db->loadObjectList();

		$tags = array();

		if ($results) {
			foreach($results as $result) {
				if ($isCount) {
					$tags[$result->author_id] = $result->cnt;
				} else {
					$tags[$result->author_id][$result->id] = $result;
				}

			}
		}

		return $tags;
	}

	/**
	 * preload categories used by bloggers.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function preloadCategoryUsed($bloggerIds, $isCount = false)
	{
		$db = EB::db();

		$query  = 'select distinct a.*, count(b.`id`) as `post_count`, c.`created_by` as `author_id`';

		if ($isCount) {
			$query = 'select count(distinct a.id) as `cnt`, c.`created_by` as `author_id`';
		}

		$query 	.= ' from `#__easyblog_category` as a';
		$query  .= ' 	inner join `#__easyblog_post_category` as b ON a.`id` = b.`category_id`';
		$query  .= ' 	inner join `#__easyblog_post` as c ON b.`post_id` = c.`id`';
		$query	.= ' where c.`created_by` IN (' . implode(',', $bloggerIds) . ')';
		$query  .= ' and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query  .= ' and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		if ($isCount) {
			$query  .= ' group by c.`created_by`';
		} else {
			$query  .= ' group by a.`id`, c.`created_by`';
		}
		$query 	.= ' order by null';

		$db->setQuery($query);

		$results = $db->loadObjectList();

		$categories = array();

		if ($results) {
			foreach ($results as $result) {
				if ($isCount) {
					$categories[$result->author_id] = $result->cnt;
				} else {
					$categories[$result->author_id][$result->id] = $result;
				}
			}
		}

		return $categories;
	}

	public function preloadBlogggerSubscribers($bloggerIds)
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$email = $my->id ? $my->email : '';

		$query = 'SELECT `id`, `uid` FROM `#__easyblog_subscriptions`';
		$query .= ' WHERE `uid` IN (' . implode(',', $bloggerIds) . ')';
		$query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);
		$query .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$subs = [];

		if ($results) {
			foreach($results as $result) {
				$subs[$result->uid] = $result->id;
			}
		}

		return $subs;
	}

	/**
	 * Retrieve all bloggers on the site
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAllBloggers()
	{
		$db = EB::db();

		$aclQuery = EB::AclHelper()->genIsbloggerSQL();
		$query = 'SELECT a.`id`, b.`nickname`, a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`, b.`permalink`';
		$query .= '	from `#__users` as a inner JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';
		$query .= ' where (' . $aclQuery . ')';
		$query .= ' group by a.`id`';
		$query .= '	ORDER BY a.`id` DESC';

		// echo $query;exit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}


}
