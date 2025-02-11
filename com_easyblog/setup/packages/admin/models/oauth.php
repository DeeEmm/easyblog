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

class EasyBlogModelOauth extends EasyBlogAdminModel
{
	public $_data = null;
	public $_total = null;
	public $_pagination = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.autopostings.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.autopostings.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Purges the auto posting logs
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function purgeLogs()
	{
		$db = EB::db();
		$query = 'DELETE FROM ' . $db->qn('#__easyblog_oauth_logs');
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @since	5.0
	 * @access 	public
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @since	5.0
	 * @access 	public
	 */
	public function _buildQuery($userId)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $userId );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EB::db();

		$query		= 'SELECT a.* FROM ' . $db->nameQuote( '#__easyblog_oauth' ) . ' AS a '
					. $where . ' '
					. $orderby;

		return $query;
	}

	/**
	 * Builds the where clause
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function _buildQueryWhere($userId)
	{
		$db = EB::db();

		$where[] = 'a.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote($userId);

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	/**
	 * Builds the order by clause
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function _buildQueryOrderBy()
	{
		$orderby = ' ORDER BY a.`id`';
		return $orderby;
	}

	/**
	 * Method to get teamblog item data
	 *
	 * @since	5.0
	 * @access public
	 */
	public function getConsumers( $userId )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)){
			$query = $this->_buildQuery( $userId );
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Determines if a post has been shared before previously.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isShared($postId, $oauthId)
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_oauth_posts');
		$query .= ' WHERE ' . $db->quoteName('oauth_id') . '=' . $db->Quote($oauthId);
		$query .= ' AND ' . $db->quoteName('post_id') . '=' . $db->Quote($postId);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result > 0;
	}

	/**
	 * Determines if a specific client has been associated with the system
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isAssociated($client)
	{
		$db 		= EB::db();
		$query		= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_oauth') . ' '
					. 'WHERE ' . $db->nameQuote('system') . '=' . $db->Quote( 1 ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote($client) . ' '
					. 'AND ' . $db->nameQuote( 'access_token' ) . ' !=""';
		$db->setQuery( $query );

		return $db->loadResult() > 0;
	}

	/**
	 * Retrieves a list of Twitter oauth accesses on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTwitterAccounts()
	{
		// Find all oauth accounts
		$db = EB::db();

		$query 	= 'SELECT * FROM ' . $db->quoteName('#__easyblog_oauth');
		$query	.= ' WHERE ' . $db->quoteName('type') . '=' . $db->Quote('twitter');

		//for now, need to comment this out, so that we can fetch all twitter account on the site
		// $query 	.= ' AND ' . $db->quoteName('system') . '=' . $db->Quote(0);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get the total nr of the team
	 *
	 * @since	5.0
	 * @access 	public
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Retrieves a list of oauth linked accounts for a particular user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getUserClients($userId = null)
	{
		$user = JFactory::getUser($userId);

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_oauth');
		$query[] = 'WHERE ' . $db->qn('user_id') . '=' . $db->Quote($user->id);
		$query[] = 'AND ' . $db->qn('system') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$clients = $db->loadObjectList();

		return $clients;
	}

	/**
	 * Retrieves a list of oauth linked accounts for a particular user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getSystemClients($type = null)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_oauth');
		$query[] = 'WHERE ' . $db->qn('system') . '=' . $db->Quote(1);

		if ($type) {
			$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote($type);
		}

		// order by alphabetical to process Facebook autopost first
		$query[] = ' ORDER BY ' . $db->qn('type') . ' asc';

		$db->setQuery($query);

		$clients = $db->loadObjectList();

		return $clients;
	}

	/**
	 * Determines if the blog post is associated with a twitter previously
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLastTweetImport($oauthId)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT ' . $db->quoteName('id_str') . ' FROM ' . $db->quoteName('#__easyblog_twitter_microblog');
		$query[] = 'WHERE ' . $db->quoteName('oauth_id') . ' = ' . $db->Quote($oauthId);
		$query[] = 'ORDER BY ' . $db->quoteName('created') . ' DESC';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves a list of auto posting logs
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getLogs($status = '')
	{
		$db = EB::db();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_oauth_logs');

		if ($status) {
			$status = $status == 'success' ? 1 : 0;

			$query[] = 'WHERE ' . $db->qn('status') . '=' . $db->Quote($status);
		}

		$db->setQuery($query);

		$this->total = $db->loadResult();


		$query[0] = str_ireplace('COUNT(1)', '*', $query[0]);

		// $limit = EB::call('Pagination', 'getLimit', array('listLength'));
		// $limitstart = $this->input->get('limitstart', 0, 'int');

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		if ($limitstart < 0) {
			$limitstart = 0;
		}

		$query[] = ' LIMIT ' . $limitstart . ',' . $limit;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		$this->pagination = EB::Pagination($this->total, $limitstart, $limit);

		return $result;
	}

	/**
	 * Retrieve oauth tokens thats about to expired.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getSoonTobeExpired($type, $days, $isSystem = true, $userId = 0)
	{
		$db = EB::db();
		$now = EB::date()->toSql();

		// debug code:
		// $now = '2016-11-25 12:21:07';

		$query = 'select a.`id`, a.`user_id`, b.`name`, b.`email`';
		$query .= ' from `#__easyblog_oauth` as a';
		$query .= '		inner join `#__users` as b on a.`user_id` = b.`id`';
		$query .= ' where a.`type` = ' . $db->Quote($type);
		$query .= ' and a.`notify` = ' . $db->Quote('0');
		if ($isSystem) {
			$query .= ' and a.`system` = ' . $db->Quote('1');
		}
		$query .= ' and a.`expires` <= DATE_ADD(' . $db->Quote($now) . ', INTERVAL ' . $days. ' DAY)';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * method to mark the notify column to true
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function markNotify($ids)
	{
		$db = EB::db();

		$query = "update `#__easyblog_oauth` set `notify` = " . $db->Quote('1');
		$query .= " where `id` IN (" . implode($ids) . ")";

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}

	/**
	 * Check if a given oauth has any posts associated or not.
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function hasPostsShared($id)
	{
		$db = EB::db();

		$query = "SELECT count(1) FROM `#__easyblog_oauth_posts`";
		$query .= " WHERE `oauth_id` = " . $db->Quote($id);

		$db->setQuery($query);
		$count = $db->loadResult();

		return $count ? true : false;
	}

	/**
	 * Check if a given oauth has any posts associated or not.
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function migrateOauthPosts($oldId, $newId)
	{
		$db = EB::db();

		$query = "update `#__easyblog_oauth_posts` set `oauth_id` = " . $db->Quote($newId);
		$query .= " where `oauth_id` = " . $db->Quote($oldId);

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}
}
