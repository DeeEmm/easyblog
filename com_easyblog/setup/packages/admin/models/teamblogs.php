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

class EasyBlogModelTeamBlogs extends EasyBlogAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = EB::getLimit();
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = EB::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();

		$db			= EB::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_team' ) . ' AS a'
				. $where . ' '
				. $orderby;

		return $query;
	}

	/**
	 * Retrieves a list of Joomla User Groups associated with a team
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserGroups($teamId)
	{
		$db 	= EB::db();

		$query 	= 'SELECT ' . $db->quoteName('group_id') . ' FROM ' . $db->quoteName('#__easyblog_team_groups');
		$query 	.= ' WHERE ' . $db->quoteName('team_id') . '=' . $db->Quote($teamId);

		$db->setQuery($query);

		$userGroups = $db->loadColumn();

		return $userGroups;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();

		$filter_state = $mainframe->getUserStateFromRequest('com_easyblog.teamblogs.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easyblog.teamblogs.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'a.' . $db->nameQuote('published') . '=' . $db->Quote('1');
			} else if ($filter_state == 'U') {
				$where[] = 'a.' . $db->nameQuote('published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER(a.`title`) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.teamblogs.filter_order', 		'filter_order', 	'a.created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.teamblogs.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Retrieves a list of team members from a team
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMembers($id)
	{
		if (!$id) {
			return;
		}

		if (EB::cache()->exists($id, 'teamblogs')) {

			$data = Eb::cache()->get($id, 'teamblogs');

			if (isset($data['member'])) {
				return $data['member'];
			} else {
				// since we arleady cache this teamid, this mean there is no members for this teamblog
				return array();
			}
		}

		$db = EB::db();

		$query 	= 'SELECT * FROM ' . $db->quoteName('#__easyblog_team_users');
		$query 	.= ' WHERE ' . $db->quoteName('team_id') . '=' . $db->Quote($id);
		$db->setQuery($query);

		$members = $db->loadObjectList();

		return $members;
	}

	/**
	 * Retrieve the members count
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int
	 * @return
	 */
	public function getMembersCount($id)
	{
		$config = EB::config();
		$db = EB::db();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = 'SELECT COUNT(' . $db->quoteName('user_id') . ') FROM ' . $db->quoteName('#__easyblog_team_users') . ' as a';

		if (!$showBlockedUserPosts) {
			$query .= ' INNER JOIN `#__users` as uu on a.`user_id` = uu.`id` and uu.`block` = 0';
		}

		$query .= ' WHERE ' . $db->quoteName('team_id') . '=' . $db->Quote($id);

		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getTeams()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$pagination = $this->getPagination();

			$this->_data	= $this->_getList($query, $pagination->limitstart, $pagination->limit);
		}

		return $this->_data;
	}

	/**
	 * Clear off any relations
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteGroupRelations($id)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_team_groups');
		$query .= ' WHERE ' . $db->quoteName('team_id') . '=' . $db->Quote($id);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Retrieves a list of teams the user joined
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTeamJoined($id = null)
	{
		$db = EB::db();
		$userId = JFactory::getUser($id)->id;
		$groupIds = EB::getUserGids($userId);

		$query = array();

		$query[] = 'SELECT DISTINCT a.* FROM ' . $db->qn('#__easyblog_team') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_team_users') . ' AS b';
		$query[] = 'ON b.' . $db->qn('team_id') . ' = a.' . $db->qn('id');
		$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_team_groups') . ' AS c';
		$query[] = 'ON a.' . $db->qn('id') . ' = c.' . $db->qn('team_id');
		$query[] = 'WHERE (';
		$query[] = 'b.' . $db->qn('user_id') . '=' . $db->Quote($userId) . ' OR c.' . $db->qn('group_id') . ' IN(' . implode(',', $groupIds) . ')';
		$query[] = ')';
		$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote(1);

		$query = implode(' ', $query);

		// echo str_ireplace('#__', 'jos_', $query);
		// exit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$teams = array();

		foreach ($result as $row) {

			$team = EB::table('TeamBlog');
			$team->bind($row);

			$teams[] = $team;
		}

		return $teams;
	}

	/**
	 * Retrieves a list of categories for this team
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getCategories($id)
	{
		$db = EB::db();

		$query = 'SELECT DISTINCT a.*, COUNT( b.' . $db->nameQuote( 'id' ) . ' ) AS ' . $db->nameQuote( 'post_count' );
		$query .= ' FROM ' . $db->qn('#__easyblog_category') . ' AS a';
		$query .= ' INNER JOIN ' . $db->qn('#__easyblog_post_category') . ' AS b';
		$query .= ' 	ON a.`id` = b.`category_id`';
		$query .= ' INNER JOIN ' . $db->qn('#__easyblog_post') . ' AS c';
		$query .= ' 	ON b.`post_id` = c.`id`';
		$query .= ' where (c.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM) . ' and c.`source_id` = ' . $db->Quote($id) .')';
		$query .= ' group by a.`id`';

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getBlogContributed($postId)
	{
		$db = EB::db();

		$query  = 'SELECT a.`source_id` as `team_id`, b.`title`, \'1\' AS `selected`';
		$query .= ' FROM `#__easyblog_post`  AS `a`';
		$query .= ' 	INNER JOIN `#__easyblog_team` AS `b` ON a.`source_id` = b.`id`';
		$query .= ' WHERE a.`id` = ' . $db->Quote($postId);
		$query .= ' AND a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		$db->setQuery( $query );
		$result	= $db->loadObject();
		//$result	= $db->loadAssocList();

		return $result;
	}

	public function getTeamSubscribers($teamId)
	{
		$db = EB::db();

		$query  = "SELECT *, 'teamsubscription' as `type` FROM `#__easyblog_subscriptions`";
		$query  .= " WHERE `uid` = " . $db->Quote($teamId);
		$query  .= " AND `utype` = " . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG);


		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Determines if the given user id is a member of a team
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isMember($teamId, $userId)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_team_users');
		$query[] = 'WHERE ' . $db->quoteName('team_id') . '=' . $db->Quote($teamId);
		$query[] = 'AND ' . $db->quoteName('user_id') . '=' . $db->Quote($userId);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$isMember = $db->loadResult() > 0;

		return $isMember;
	}

	/**
	 * Retrieves the team the blog post is posted into
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostTeam($postId)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'select a.' . $db->qn('source_id') . ' as team_id from ' . $db->qn('#__easyblog_post') . ' as a';
		$query[] = ' where a.' . $db->qn('id') . ' = ' . $db->Quote($postId);
		$query[] = ' and a.' . $db->qn('source_type') . ' = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Determines if the user is a team blog admin
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isTeamAdmin($teamId, $userId = null)
	{
		$db = EB::db();
		$user = JFactory::getUser($userId);

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_team_users') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_team') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('team_id') . ' = b.' . $db->quoteName('id');
		$query[] = 'AND a.' . $db->quoteName('isadmin') . '=' . $db->Quote(1);
		$query[] = 'AND a.' . $db->quoteName('team_id') . '=' . $db->Quote($teamId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$isAdmin = $db->loadResult() > 0;

		return $isAdmin;
	}

	public function checkIsTeamAdmin($userId , $teamId	= '')
	{
		$db = EB::db();

		$query  = 'select count(1) from `#__easyblog_team_users` as a';
		$query  .= ' inner join `#__easyblog_team` as b on a.`team_id` = b.`id`';
		$query  .= ' where a.`user_id` = ' . $db->Quote($userId);
		$query  .= ' and a.`isadmin` = ' . $db->Quote('1');
		if(!empty($teamId))
			$query  .= ' and a.`team_id` = ' . $db->Quote($teamId);

		$db->setQuery($query);
		$result	= $db->loadResult();

		return ($result > 0) ? true : false;
	}

	/**
	 * Retrieves a list of team members from a team blog
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTeamMembers( $teamId )
	{
		$db = EB::db();

		$query	= 'SELECT ' . $db->nameQuote( 'user_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__easyblog_team_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'team_id' ) . ' = ' . $db->Quote( $teamId );
		$db->setQuery($query);

		$result = $db->loadObjectList();

		// @rule: Process users from Joomla user groups
		$exclusion	= '';
		$total		= count( $result );

		if( $result )
		{
			for( $i = 0; $i < $total; $i++ )
			{
				$exclusion .= $db->Quote( $result[ $i ]->user_id );

				if( next( $result ) !== false )
				{
					$exclusion .= ',';
				}
			}
		}

		$query	= 'SELECT b.`user_id` '
				. 'FROM ' . $db->nameQuote( '#__easyblog_team_groups' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__user_usergroup_map' ) . ' AS b '
				. 'ON a.`group_id` = b.`group_id` '
				. 'WHERE a.' . $db->nameQuote( 'team_id' ) . ' = ' . $db->Quote( $teamId );

		if( !empty( $exclusion ) )
		{
			$query	.= ' AND b.`user_id` NOT IN(' . $exclusion . ')';
		}

		$db->setQuery($query);

		$groupUsers	= $db->loadObjectList();
		$result = array_merge($result, $groupUsers);

		if (!$result) {
			return $result;
		}

		//preload users
		$tempIds = array();
		foreach ($result as $row) {
			$tempIds[] = $row->user_id;
		}

		// make sure no dupliate values.
		$tempIds = array_unique($tempIds);

		EB::user($tempIds);


		$members = array();

		$ids = array();
		foreach ($result as $row) {

			if (!isset($ids[$row->user_id])) {
				$profile = EB::user($row->user_id);
				$profile->displayName = $profile->getName();

				$members[] = $profile;
			}

			$ids[$row->user_id] = true;
		}

		return $members;
	}

	/**
	 * Method to get teamblog item data
	 *
	 * @access public
	 * @return array
	 */
	public function getTeamBlogs($options = array())
	{
		$my = JFactory::getUser();
		$db = EB::db();

		// getting user gids
		$gid = array();

		if ($my->guest) {
			$gid = JAccess::getGroupsByUser(0, false);
		} else {
			$gid = JAccess::getGroupsByUser($my->id, false);
		}

		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= !$gids ? $id : ',' . $id;
			}
		}

		$query = 'SELECT a.*';

		if (isset($options['featured']) && $options['featured']) {
			$query .= 'IFNULL(b.id, "0") AS ' . $db->qn('isfeatured');
		}

		$query .= ' FROM ' . $db->qn('#__easyblog_team') . ' AS a';


		if (isset($options['featured']) && $options['featured']) {
			$query .= ' LEFT JOIN ' . $db->qn('#__easyblog_featured') . ' AS b';
			$query .= ' ON a.' . $db->qn('id') . '= b.' . $db->qn('content_id');
			$query .= ' AND b.' . $db->qn('type') . '=' . $db->Quote(EBLOG_FEATURED_TEAMBLOG);
		}

		$query .= ' WHERE a.' . $db->qn('published') . '=' . $db->Quote(1);
		$query .= ' AND (';
		$query .= '  (a.' . $db->qn('access') . '=' . $db->Quote(EBLOG_TEAMBLOG_ACCESS_EVERYONE) . ')';

		if ($gids) {
			$query .= '  OR';
			$query .= ' (a.' . $db->qn('access') . '=' . $db->Quote(EBLOG_TEAMBLOG_ACCESS_MEMBER);
			$query .= ' 	AND (';
			$query .= '  		SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_team_groups') . ' AS agrp WHERE agrp.' . $db->qn('team_id') . ' = a.' . $db->qn('id');
			$query .= '  		AND agrp.' . $db->qn('group_id') . ' IN (' . $gids . ')';
			$query .= ' 	) > 0';
			$query .= ' )';
		}

		if (!$my->guest) {
			$query .= '  OR';
			$query .= ' (a.' . $db->qn('access') . '=' . $db->Quote(EBLOG_TEAMBLOG_ACCESS_MEMBER);
			$query .= ' 	AND (';
			$query .= '  		SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_team_users') . ' AS tu WHERE tu.' . $db->qn('team_id') . ' = a.' . $db->qn('id');
			$query .= '  		AND tu.' . $db->qn('user_id') . ' IN (' . $my->id . ')';
			$query .= ' 	) > 0';
			$query .= ' )';

			// Determine if access is open for registered user only.
			$query .= ' OR';
			$query .= ' (a.' . $db->qn('access') . '=' . $db->Quote(EBLOG_TEAMBLOG_ACCESS_REGISTERED);
			$query .= ' )';
		}

		$query .= ' )';

		// If the user is a guest we should not display teams that are restricted to registered only.
		if ($my->guest) {
			$query .= ' AND a.' . $db->qn('access') . '!=' . $db->Quote(EBLOG_TEAMBLOG_ACCESS_REGISTERED);
		}

		// Default ordering by creation date
		$query .= ' ORDER BY a.`created` DESC';

		$result = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		if (!$result) {
			return $result;
		}

		$teams = array();

		foreach ($result as $row) {
			$team = EB::table('TeamBlog');
			$team->bind($row);

			$teams[] = $team;
		}

		return $teams;
	}

	public function getTotalTeamJoined( $userId )
	{
		$db = EB::db();

		$query  = 'select count(1) from `#__easyblog_team_users` where `user_id` = ' . $db->Quote($userId);
		$db->setQuery( $query );

		$result	= $db->loadResult();
		return (empty($result)) ? 0 : $result;
	}

	public function getTotalRequest()
	{
		$my     = JFactory::getUser();

		$userId	= (FH::isSiteAdmin()) ? '' : $my->id;
		return count($this->getRequests($userId, false));
	}

	/**
	 * Retrieves a list of team blog requests
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getRequests($userId = '', $useLimit = true, $search = null)
	{
		$db = EB::db();

		if ($useLimit) {
			$limit	= ($this->getState('limit') > 0) ? $this->getState('limit') : EB::getLimit();
			$this->setState('limit', $limit);
		}

		$limit = $this->getState('limit');
		$limitstart = $this->getState('limitstart');

		// common query
		$cquery = '';

		// If user id is provided, we assume that the user is a team admin and not a site admin
		if (!empty($userId)) {
			$cquery  .= ' inner join `#__easyblog_team_users` as b';
			$cquery  .= '    on a.`team_id` = b.`team_id`';
			$cquery  .= '    and b.`user_id` = ' . $db->Quote($userId);
			$cquery  .= '    and b.`isadmin` = ' . $db->Quote('1');
		}

		$cquery .= '  inner join `#__easyblog_team` as c on a.`team_id` = c.`id`';

		if ($search) {
			$cquery .= ' INNER JOIN ' . $db->qn('#__users') . ' AS d';
			$cquery .= ' ON a.' . $db->qn('user_id') . ' = d.' . $db->qn('id');
		}

		$cquery  .= ' where a.`ispending` = ' . $db->Quote('1');

		if ($search) {
			$cquery .= 'AND (';
			$cquery .= 'c.' . $db->qn('title') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$cquery .= ' OR ';
			$cquery .= 'd.' . $db->qn('name') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
			$cquery .= ')';
		}

		$query  = 'select count(1) from `#__easyblog_team_request` as a';
		$query  .= $cquery;


		$db->setQuery($query);

		$this->_total = $db->loadResult();

		jimport('joomla.html.pagination');
		$this->_pagination = EB::pagination( $this->_total , $limitstart , $limit );

		// Actual query
		$query  = 'select a.*, c.`title` from `#__easyblog_team_request` as a';
		$query  .= $cquery;
		$query  .= ' order by a.`created`';

		if($useLimit) {
			$query	.= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$db->setQuery($query);
		$result	= $db->loadObjectList();

		return $result;
	}


	public function isTeamSubscribedUser($teamId, $userId, $email)
	{
		$db	= EB::db();

		$query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query  .= ' WHERE `uid` = ' . $db->Quote($teamId);
		$query  .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG);


		$query  .= ' AND (`user_id` = ' . $db->Quote($userId);
		$query  .= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function isTeamSubscribedEmail($teamId, $email)
	{
		$db	= EB::db();

		$query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query  .= ' WHERE `uid` = ' . $db->Quote($teamId);
		$query  .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG);

		$query  .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function addTeamSubscription($teamId, $email, $userId = '0', $fullname = '')
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		$teamTbl	= EB::table('Teamblog');
		$teamTbl->load($teamId);
		$gid = EB::getUserGids($userId);
		$isMember = $teamTbl->isMember($userId, $gid);

		if ($teamTbl->allowSubscription($teamTbl->access, $userId, $isMember, $acl->get('allow_subscription'))) {
			$date       = EB::date();
			$subscriber = EB::table('Subscriptions');

			$subscriber->uid = $teamId;
			$subscriber->utype = EBLOG_SUBSCRIPTION_TEAMBLOG;


			$subscriber->email = $email;

			if($userId != '0') {
				$subscriber->user_id = $userId;
			}

			$subscriber->fullname = $fullname;
			$subscriber->created = $date->toSql();
			$state =  $subscriber->store();

			// lets send confirmation email to subscriber.
			if ($state) {
				$helper = EB::subscription();
				$template = $helper->getTemplate();

				$template->uid = $subscriber->id;
				$template->utype = 'teamsubscription';
				$template->user_id = $subscriber->user_id;
				$template->uemail = $email;
				$template->ufullname = $fullname;
				$template->ucreated = $subscriber->created;
				$template->targetname = $teamTbl->title;
				$template->targetlink = EBR::getRoutedURL('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $teamId, false, true );

				$helper->addMailQueue($template);
			}

		}
	}

	function updateTeamSubscriptionEmail($sid, $userId, $email)
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		$subscriber = EB::table('Subscriptions');
		$subscriber->load($sid);

		$teamTbl = EB::table('Teamblog');
		$teamTbl->load($subscriber->uid);

		$gid = EB::getUserGids($userId);
		$isMember = $teamTbl->isMember($userId, $gid);

		if ($teamTbl->allowSubscription($teamTbl->access, $userId, $isMember, $acl->get('allow_subscription'))) {
			$subscriber->user_id  = $userId;
			$subscriber->email    = $email;
			$subscriber->store();
		}
	}

	public function getPostTeamId($id)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'select a.' . $db->qn('source_id') . ' as team_id from ' . $db->qn('#__easyblog_post') . ' as a';
		$query[] = ' where a.' . $db->qn('id') . ' = ' . $db->Quote($postId);
		$query[] = ' and a.' . $db->qn('source_type') . ' = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Retrieves a list of posts from a specific team
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPosts($id, $limit = null)
	{
		//lets check if posts already cached or not.
		if (EB::cache()->exists($id, 'teamblogs')) {
			$data = EB::cache()->get($id, 'teamblogs');

			if (isset($data['post'])) {
				return $data['post'];
			} else {
				return array();
			}
		}

		$db = EB::db();
		$config = EB::config();
		$query = array();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$options = array('teamId'=>$id, 'concateOperator' => 'AND');

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = '';
		$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a', $options);


		$query[] = 'SELECT a.* FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';

		if (!$showBlockedUserPosts) {
			$query[] = ' INNER JOIN `#__users` as uu on a.`created_by` = uu.`id` and uu.`block` = 0';
		}

		$query[] = 'where a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'and a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query[] = $contributeSQL;

		// category access here
		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccessSQL = $catLib->genAccessSQL('a.`id`');
			$query[] = 'AND (' . $catAccessSQL . ')';
		}

		// Ordering options
		$ordering = 'DESC';
		$query[] = ' ORDER BY a.' . $db->quoteName('created') . ' ' . $ordering;

		if (!is_null($limit) && $limit) {

			// Glue back the sql queries into a single string.
			$queryCount = implode(' ', $query);
			$queryCount = str_ireplace('SELECT a.*', 'SELECT COUNT(1)', $queryCount);

			$db->setQuery($queryCount);
			$count = $db->loadResult();

			$limit = ($limit == 0) ? $this->getState('limit') : $limit;
			$limitstart = $this->input->get('limitstart', 0, 'int');

			// Set the limit
			$query[] = 'LIMIT ' . $limitstart . ',' . $limit;

			$this->_pagination = EB::pagination($count, $limitstart, $limit);

		} else {
			// TODO: prepare the pagination object
		}

		$query = implode(' ', $query);

		// Debug
		// echo str_ireplace('#__', 'jos_', $query);exit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves the total number of posts available in a team
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostCount($id)
	{
		$db = EB::db();
		$query = array();

		$options = array('teamId'=>$id, 'concateOperator' => 'AND');

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = '';
		$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a', $options);

		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'where a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'and a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = $contributeSQL;

		// category access here
		$config = EB::config();
		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccessSQL = $catLib->genAccessSQL('a.`id`');
			$query[] = 'AND (' . $catAccessSQL . ')';
		}


		$query = implode(' ', $query);

		$db->setQuery($query);
		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves a list of teams that are associated with the specific user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getUserTeams($userId = null, $search = '', $limit = null)
	{
		$user = JFactory::getuser($userId);
		$userId = $user->id;

		$db = EB::db();

		$query = array();

		$query[] = 'SELECT SQL_CALC_FOUND_ROWS a.* FROM ' . $db->qn('#__easyblog_team') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_team_users') . ' AS b';
		$query[] = 'ON a.' . $db->qn('id') . ' = b.' . $db->qn('team_id');
		$query[] = 'AND b.' . $db->qn('user_id') . '=' . $db->Quote($userId);

		$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_team_groups') . ' AS c';
		$query[] = 'ON a.' . $db->qn('id') . ' = c.'  . $db->qn('team_id');
		$query[] = 'WHERE a.' . $db->qn('published') . '=' . $db->Quote(1);

		if ($search) {
			$query[] = 'AND a.' . $db->quoteName('title') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
		}

		$query[] = 'GROUP BY a.' . $db->qn('id') . ' HAVING (COUNT(b.' . $db->qn('team_id') . ') > 0 || COUNT(c.' . $db->qn('team_id') . ') > 0)';

		if ($limit) {
			$this->setState('limit', $limit);

			$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');
			$limitSQL = 'LIMIT ' . $limitstart . ',' . $limit;
			$query[] = $limitSQL;
		}

		$query = implode(' ', $query);
		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if (!$rows) {
			return $rows;
		}

		$teams = array();

		foreach ($rows as $row) {
			$team = EB::table('TeamBlog');
			$team->bind($row);

			$teams[] = $team;
		}

		$cntQuery = 'select FOUND_ROWS()';
		$db->setQuery($cntQuery);

		$this->_total = $db->loadResult();

		return $teams;
	}

	/**
	 * Retrieves a list of teams that are associated with the specific user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getAllTeams($search = '', $limit = null)
	{
		$db = EB::db();

		$query = array();

		$limit	= ($limit == 0) ? $this->getState('limit') : $limit;

		if ($limit) {
			$this->setState('limit', $limit);
		}

		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');

		$limitSQL = 'LIMIT ' . $limitstart . ',' . $limit;

		$query[] = 'SELECT SQL_CALC_FOUND_ROWS a.* FROM ' . $db->qn('#__easyblog_team') . ' AS a';
		$query[] = 'WHERE 1';

		if ($search) {
			$query[] = 'AND a.' . $db->quoteName('title') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
		}

		$query[] = $limitSQL;

		$query = implode(' ', $query);
		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if (!$rows) {
			return $rows;
		}

		$teams = array();

		foreach ($rows as $row) {
			$team = EB::table('TeamBlog');
			$team->bind($row);

			$teams[] = $team;
		}

		$cntQuery = 'select FOUND_ROWS()';
		$db->setQuery($cntQuery);

		$this->_total = $db->loadResult();

		return $teams;
	}

	public function getAllMembersCount($teamId)
	{
		$db = EB::db();

		$config = EB::config();
		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = "select count(b.`team_id`) as cnt";
		$query .= "	from `#__easyblog_team_groups` as b";
		$query .= "		inner join `#__user_usergroup_map` as a on b.`group_id` = a.`group_id`";
		$query .= "		left join `#__easyblog_team_users` as c on a.`user_id` = c.`user_id` and c.`team_id` = b.`team_id`";
		if (!$showBlockedUserPosts) {
			$query .= "		left join `#__users` as uu on c.`user_id` = uu.`id`";
		}
		$query .= " where b.`team_id` = " . $db->Quote($teamId);
		$query .= " and c.`user_id` is null";
		if (!$showBlockedUserPosts) {
			$query .= " and (uu.`block` = 0 OR uu.`id` is null)";
		}

		$query .= " UNION ALL ";

		$query .= " select count(a.`team_id`) as cnt";
		$query .= "	from `#__easyblog_team_users` as a";
		if (!$showBlockedUserPosts) {
			$query .= "		left join `#__users` as uu on a.`user_id` = uu.`id`";
		}
		$query .= " where a.`team_id` = " . $db->Quote($teamId);
		$aclQuery = EB::AclHelper()->genIsbloggerSQL('a.user_id');
		$query .= " and (" . $aclQuery . ")";
		if (!$showBlockedUserPosts) {
			$query .= " and (uu.`block` = 0 OR uu.`id` is null)";
		}

		$db->setQuery($query);

		$results = $db->loadColumn();

		if ($results) {
			return array_sum($results);
		}

		return '0';
	}

	public function getAllMembers($teamId, $limit = 0)
	{
		$db = EB::db();

		$config = EB::config();
		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = '';

		$query .= "select x.user_id, count(p.id) as `postCnt`, x.`isadmin` from (";
		$query .= "(select a.user_id, a.isadmin";
		$query .= " from `#__easyblog_team_users` as a";
		if (!$showBlockedUserPosts) {
			$query .= "		inner join `#__users` as uu on a.`user_id` = uu.`id` and uu.`block` = 0";
		}

		$query .= " where a.`team_id` = " . $db->Quote($teamId);
		if ($limit) {
			$query .= " limit " . $limit;
		}
		$query .= ")";

		$query .= " UNION ALL ";

		$query .= " (select a.`user_id`, c.`isadmin`";
		$query .= " from `#__easyblog_team_groups` as b";
		$query .= "     inner join `#__user_usergroup_map` as a on b.`group_id` = a.`group_id`";
		$query .= "     left join `#__easyblog_team_users` as c on a.`user_id` = c.`user_id` and c.`team_id` = b.`team_id`";
		if (!$showBlockedUserPosts) {
			$query .= "		inner join `#__users` as uu on c.`user_id` = uu.`id` and uu.`block` = 0";
		}

		$query .= " where b.`team_id` = " . $db->Quote($teamId);

		$query .= " and c.`user_id` is null";
		if ($limit) {
			$query .= " limit " . $limit;
		}
		$query .= ")";

		// to limit the overall results.
		if ($limit) {
			$query .= " limit " . $limit;
		}

		$query .= ") as x";
		$query .= " left join `#__easyblog_post` as p on x.`user_id` = p.`created_by`";
		$query .= " and p.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= " and p.`source_id` = " . $db->Quote($teamId);
		$query .= " and p.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		$aclQuery = EB::AclHelper()->genIsbloggerSQL('x.user_id');
		$query .= " where (" . $aclQuery . ")";

		$query .= " group by x.`user_id`";
		$query .= " order by null";

		// echo $query;exit;

		$db->setQuery($query);

		$results = $db->loadObjectList();

		$members = array();

		if ($results) {

			//preload users
			$ids = array();
			foreach($results as $item) {
				$ids[] = $item->user_id;
			}

			EB::user($ids);

			foreach($results as $item) {

				$profile = EB::user($item->user_id);
				$profile->displayName = $profile->getName();

				$profile->postCount = $item->postCnt;
				$profile->isAdmin = $item->isadmin;

				$members[] = $profile;
			}
		}

		return $members;
	}

	/**
	 * preload team members
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadTeamMembers($teamIds)
	{
		$db = EB::db();

		$config = EB::config();
		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = "select a.`team_id`, a.`user_id`, a.`isadmin` from `#__easyblog_team_users` as a";
		if (!$showBlockedUserPosts) {
			$query .= " INNER JOIN `#__users` as uu on a.`user_id` = uu.`id` and uu.`block` = 0";
		}
		if (count($teamIds) == 1) {
			$query .= " where a.`team_id` = " . $db->Quote($teamIds[0]);
		} else {
			$query .= " where a.`team_id` IN (" . implode(',', $teamIds) . ")";
		}

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}

	public function preloadTotalMemberCount($teamIds)
	{
		$db = EB::db();

		$config = EB::config();
		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = "(select b.`team_id`, count(b.`team_id`) as cnt";
		$query .= "	from `#__easyblog_team_groups` as b";
		$query .= "		inner join `#__user_usergroup_map` as a on b.`group_id` = a.`group_id`";
		$query .= "		left join `#__easyblog_team_users` as c on a.`user_id` = c.`user_id` and c.`team_id` = b.`team_id`";
		if (!$showBlockedUserPosts) {
			$query .= "		left join `#__users` as uu on c.`user_id` = uu.`id`";
		}
		$query .= " where b.`team_id` IN (" . implode(',', $teamIds) . ")";
		$query .= " and c.`user_id` is null";
		if (!$showBlockedUserPosts) {
			$query .= " and (uu.`block` = 0 OR uu.`id` is null)";
		}
		$query .= " group by b.`team_id`";
		$query .= " order by null)";

		$query .= " UNION ALL ";

		$query .= " (select a.`team_id`, count(a.`team_id`) as cnt";
		$query .= "	from `#__easyblog_team_users` as a";
		if (!$showBlockedUserPosts) {
			$query .= "		left join `#__users` as uu on a.`user_id` = uu.`id`";
		}
		$query .= " where a.`team_id` IN (".implode(',', $teamIds).")";

		$aclQuery = EB::AclHelper()->genIsbloggerSQL('a.user_id');
		$query .= " and (" . $aclQuery . ")";
		if (!$showBlockedUserPosts) {
			$query .= " and (uu.`block` = 0 OR uu.`id` is null)";
		}

		$query .= " group by a.`team_id`";
		$query .= " order by null)";

		$db->setQuery($query);

		$results = $db->loadObjectList();

		$members = array();

		if ($results) {
			foreach($results as $item) {
				if (isset($members[$item->team_id])) {
					$tmp = $members[$item->team_id];
					$tmp = $tmp + $item->cnt;
					$members[$item->team_id] = (int) $tmp;
				} else {
					$members[$item->team_id] = (int) $item->cnt;
				}
			} // foreach
		}

		return $members;
	}

	public function preloadPosts($teamIds, $limit = EASYBLOG_TEAMBLOG_LISTING_NO_POST)
	{
		$db = EB::db();
		$config = EB::config();
		$query = array();

		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		foreach($teamIds as $cid) {

			$tmp = '(SELECT a.*, f.`id` as `featured` FROM ' . $db->qn('#__easyblog_post') . ' AS a';
			$tmp .= ' LEFT JOIN `#__easyblog_featured` AS f';
			$tmp .= ' 	ON a.`id` = f.`content_id` AND f.`type` = ' . $db->Quote('post');

			if (!$showBlockedUserPosts) {
				$tmp .= ' INNER JOIN `#__users` as uu on a.`created_by` = uu.`id` and uu.`block` = 0';
			}

			$tmp .= ' where a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$tmp .= ' and a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
			$tmp .= ' and a.' . $db->qn('source_type') . ' = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);
			$tmp .= ' and a.' . $db->qn('source_id') . ' = ' . $db->Quote($cid);

			// category access here
			if ($config->get('main_category_privacy')) {
				$catLib = EB::category();
				$catAccessSQL = $catLib->genAccessSQL('a.`id`');
				$tmp .= ' AND (' . $catAccessSQL . ')';
			}

			// Filter by language
			$language = EB::getCurrentLanguage();
			if ($language) {
				$tmp .= ' AND (a.' . $db->qn('language') . '=' . $db->Quote($language) . ' OR a.' . $db->qn('language') . '=' . $db->Quote('*') . ' OR a.' . $db->qn('language') . '=' . $db->Quote('') . ')';
			}

			$tmp .= ' order by a.`created` desc';
			$tmp .= ' limit ' . $limit . ')';

			$query[] = $tmp;

		}

		$query = implode(' UNION ALL ', $query);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$posts = array();
		if ($results) {
			foreach($results as $row) {
				$posts[$row->source_id][] = $row;
			}
		}

		return $posts;
	}

	public function preloadPostCount($teamIds)
	{
		$db = EB::db();

		$config = EB::config();
		$showBlockedUserPosts = $config->get('main_show_blockeduserposts', 0);

		$query = 'SELECT a.`source_id`, COUNT(a.`source_id`) as `cnt` FROM ' . $db->qn('#__easyblog_post') . ' AS a';
		if (!$showBlockedUserPosts) {
			$query .= ' INNER JOIN `#__users` as uu on a.`created_by` = uu.`id` and uu.`block` = 0';
		}
		$query .= ' where a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' and a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= ' and a.' . $db->qn('source_type') . ' = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);
		$query .= ' and a.' . $db->qn('source_id') . ' IN (' . implode(',', $teamIds) . ')';

		// category access here
		$config = EB::config();
		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccessSQL = $catLib->genAccessSQL('a.`id`');
			$query .= ' AND (' . $catAccessSQL . ')';
		}

		$query .= ' group by a.`source_id`';
		$query .= ' order by null';


		$db->setQuery($query);
		$results = $db->loadObjectList();

		$counts = array();

		if ($results) {
			foreach($results as $item) {
				$counts[$item->source_id] = $item->cnt;
			}
		}

		return $counts;
	}

	/**
	 * Set member as admin
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function setAdmin($userId, $teamId)
	{
		$db = EB::db();

		$query = 'UPDATE `#__easyblog_team_users` SET ';
		$query .= ' `isadmin` = ' . $db->Quote('1');
		$query .= ' WHERE `team_id` = ' . $db->Quote($teamId);
		$query .= ' AND `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}

	/**
	 * Remove admin right from team member
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeAdmin($userId, $teamId)
	{
		$db = EB::db();

		$query = 'UPDATE `#__easyblog_team_users` SET ';
		$query .= ' `isadmin` = ' . $db->Quote('0');
		$query .= ' WHERE `team_id` = ' . $db->Quote($teamId);
		$query .= ' AND `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}

	/**
	 * Remove memnber from the team
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeMember($userId, $teamId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_team_users`';
		$query .= ' WHERE ' . $db->quoteName('team_id') . '=' . $db->Quote($teamId);
		$query .= ' AND ' . $db->quoteName('user_id') . '=' . $db->Quote($userId);

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}
}
