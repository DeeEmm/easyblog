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

class EasyBlogModelUsers extends EasyBlogAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = ($this->jConfig->get('list_limit') == 0) ? 5 : $this->jConfig->get('list_limit');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
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
	public function getTotal($bloggerOnly = false)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)){
			$query = $this->_buildQuery($bloggerOnly);
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
	public function getPagination($bloggerOnly = false)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination( $this->getTotal( $bloggerOnly ), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery($bloggerOnly = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();

		$db = EB::db();

		if ($bloggerOnly) {
			$aclQuery = EB::AclHelper()->genIsbloggerSQL();

			$query	= 'select a.* FROM `#__users` AS `a`';
			$query .= $where;
			$query .= ($where) ? ' and (' : ' where (';
			$query .= $aclQuery. ')';

		} else {
			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__users' );
			$query .= $where;
		}

		$query .= $orderby;

		return $query;
	}

	public function _buildQueryWhere($tblNS = '')
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();

		$filter_state = $this->input->get('filter_state', 'P', 'string');

		$search = $this->input->get('search', '', 'string');
		$search = $db->getEscaped( trim(EBString::strtolower( $search ) ) );

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('block') . '=' . $db->Quote('0');
			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote('block') . '=' . $db->Quote('1');
			}
		}

		if ($search) {
			$where[] = ' LOWER( name ) LIKE \'%' . $search . '%\' ';
		}

		$where = $where ? ' WHERE ' . implode(' AND ', $where) : '';

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $this->input->get('filter_order', 'id', 'string');
		$filter_order_Dir	= $this->input->get('filter_order_Dir', 'asc', 'string');

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	public function _buildQueryLimit()
	{
		$limit = $this->getState('limit');
		$limitstart = $this->getState('limitstart');
		$limitSQL = ' LIMIT ' . $limitstart . ',' . $limit;

		return $limitSQL;
	}

	/**
	 * Retrieves
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserIdByEmail($email)
	{
		$db = EB::db();
		$query	= 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->nameQuote('#__users') . ' '
				. 'WHERE ' . $db->quoteName('email') . '=' . $db->Quote($email);

		$db->setQuery($query);
		$id = $db->loadResult();

		// Check if they have permissions
		return $id;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getUsers($bloggerOnly = false, $paginate = true)
	{
		$db	= EB::db();

		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query	= $this->_buildQuery($bloggerOnly);

			if ($paginate) {
				$query .= $this->_buildQueryLimit();	
			}

			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}

		return $rows;
	}

	/**
	 * Determines if the permalink exists.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	Permalink
	 * @param	int 	The current user id.
	 * @return	bool	True if exists , false otherwise
	 */
	public function permalinkExists($permalink , $id)
	{
		$db = EB::db();
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_users');
		$query .= ' WHERE ' . $db->nameQuote('permalink') . '=' . $db->Quote($permalink);
		$query .= ' AND ' . $db->nameQuote('id') . '!=' . $db->Quote($id);

		$db->setQuery( $query );
		$count = $db->loadResult();

		return $count ? true : false;
	}

	/**
	 * Creates a new user account given the username and email
	 *
	 * @since 4.0
	 *
	 */
	public function createUser($username, $email, $name, $password = false)
	{
		$registration = EB::registration();
		$options = array('username' => $username, 'email' => $email, 'name' => $name, 'password' => $password);

		$state = $registration->validate($options);

		if ($state !== true) {
			return $state;
		}

		// Create the new user account
		$id = $registration->addUser($options , 'comment');

		if (!is_numeric($id)) {
			return $id;
		}

		return (int) $id;
	}


	/**
	 * Retrieves a list of user data based on the given ids.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	Array 	$ids	An array of ids.
	 * @return
	 */
	public function getUsersMeta( $ids = array() )
	{
		$db = EB::db();

		static $const = array();

		$loaded = array();
		$new    = array();

		if (!empty($ids)) {
			foreach ($ids as $id) {
				if (is_numeric($id)) {
					if (isset($const[$id])) {
						$loaded[]	= $const[$id];
					} else {
						$new[]	= $id;
					}
				}
			}
		}

		// New ids detected. lets load the users data
		if ($new) {

			foreach ($new as $id) {
				$const[$id] = false;
			}

			$query = "select u.*,";
			$query .= " e.`id` as `eb_id`, e.`nickname`, e.`avatar`, ";
			$query .= " e.`description`, e.`url`, e.`params` as `eb_params`, e.`published` as `eb_published`, e.`title` as `eb_title`,";
			$query .= " e.`biography`, e.`permalink`, e.`custom_css`, e.`ordering`";
			$query .= " from `#__users` as u";
			$query .= " left join `#__easyblog_users` as e ON u.`id` = e.`id`";

			if (count($new) > 1) {
				$query .= " where u.`id` IN (" . implode(',', $new) . ")";
			} else {
				$query .= " where u.`id` = " . $new[0];
			}

			$db->setQuery($query);
			$users = $db->loadObjectList();

			if ($users) {
				foreach ($users as $user) {
					$loaded[] = $user;
					$const[$user->id] = $user;
				}
			}
		}

		$return = array();

		if ($loaded) {
			foreach ($loaded as $user) {
				if (isset($user->id)) {
					$return[] = $user;
				}
			}
		}

		return $return;
	}

	/**
	 * Retrieves last ordering value from the user table
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function authorLastOrdering()
	{
		$db = EB::db();

		$query = 'SELECT MAX(' . $db->nameQuote('ordering') . ')';
		$query .= ' FROM ' . $db->nameQuote('#__easyblog_users');

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;		
	}

	/**
	 * Remove all the author data from easyblog table
	 *
	 * @since	5.1.0
	 * @access	public
	 */
	public function removeEasyBlogUser($userId)
	{
		// Remove all related things to this user
		$this->removeAdsenseSetting($userId);
		$this->removeFeedburnerSetting($userId);
		$this->removeOAuthSetting($userId);
		$this->removeFeaturedBlogger($userId);
		$this->removeTeamBlogUser($userId);
		$this->removeAvatar($userId);
		
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_users`';
		$query .= ' WHERE `id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			return false;
		}

		return true;
	}

	/**
	 * Allow callers to remove avatar
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function removeAvatar($userId)
	{
		$profile = EB::user($userId);

		$avatar_path = $this->config->get('main_avatarpath');
		$avatar_path = rtrim($avatar_path, '/');
		$path = JPATH_ROOT . '/' . $avatar_path . '/' . $profile->avatar;

		JFile::delete($path);

		$profile->avatar = '';
		$profile->store();
	}

	/**
	 * Removed adsense configuration
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function removeAdsenseSetting($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_adsense`';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove any feedburner configuration of the user
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function removeFeedburnerSetting($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_feedburner`';
		$query .= ' WHERE `userid` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove oauth settings that related to autoposting
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function removeOAuthSetting($userId)
	{
		$db = EB::db();

		// removing oauth posts
		$query = 'DELETE FROM `#__easyblog_oauth_posts`';
		$query .= ' WHERE `oauth_id` IN (';
		$query .= ' select `id` from `#__easyblog_oauth` where `user_id` = ' . $db->Quote($userId);
		$query .= ')';

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}

		// removing oauth
		$query = 'DELETE FROM `#__easyblog_oauth`';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);
		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove this blogger from the featured listing
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function removeFeaturedBlogger($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_featured`';
		$query .= ' WHERE `content_id` = ' . $db->Quote($userId);
		$query .= ' AND `type` = ' . $db->Quote('blogger');

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove this author from all teams
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function removeTeamBlogUser($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_team_users`';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}
}
