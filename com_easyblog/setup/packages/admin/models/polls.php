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

class EasyBlogModelPolls extends EasyBlogAdminModel
{
	public $total = null;
	private $_nextlimit = 0;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.polls.limit', 'limit', $this->app->getCfg('list_limit') , 'int');

		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieve the items of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getItems($pollId)
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$query = [];

		$query[] = 'SELECT a.*, u.`id` as `voted` FROM ' . $db->quoteName('#__easyblog_polls_items') . 'AS a';
		$query[] = 'LEFT JOIN `#__easyblog_polls_users` as u ON u.`item_id` = a.`id`'; 
		$query[] = 'AND u.`user_id` = ' . $my->id;
		$query[] = 'WHERE a.`poll_id` = ' . $pollId;
		$query[] = 'ORDER BY a.`id` ASC';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (!$items) {
			return [];
		}

		return $items;
	}

	/**
	 * Determine if the poll has expired
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function hasExpired($expiryDate)
	{
		if (!$this->hasExpirationDate($expiryDate)) {
			return false;
		}

		$current = EB::date()->toSql();

		return $current >= $expiryDate;
	}

	/**
	 * Determine if the poll has expiration date
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function hasExpirationDate($date)
	{
		return $date !== '0000-00-00 00:00:00';
	}

	/**
	 * Determine if the user has voted the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function hasVoted($pollId, $userId)
	{
		$db = EB::db();
		$query = [];

		$query[] = 'SELECT COUNT(1) FROM `#__easyblog_polls_users`';
		$query[] = 'WHERE `poll_id` = ' . $pollId;
		$query[] = 'AND `user_id` = ' . $userId;

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve the item that the user voted
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getUserVoted($pollId, $userId)
	{
		$db = EB::db();
		$query = [];

		$query[] = 'SELECT * FROM `#__easyblog_polls_users`';
		$query[] = 'WHERE `poll_id` = ' . $pollId;
		$query[] = 'AND `user_id` = ' . $userId;

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieve the voter ids of the poll/ poll item
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getVoterIds($pollId, $itemId = null, $options = [])
	{
		$db = EB::db();
		$query = [];

		$query[] = 'SELECT `user_id` FROM `#__easyblog_polls_users`';
		$query[] = 'WHERE `poll_id` = ' . $pollId;

		if ($itemId) {
			$query[] = 'AND `item_id` = ' . $itemId;
		}

		$query[] = 'ORDER BY `id` DESC';

		$hasLimit = false;
		$start = false;

		if (isset($options['limit']) && isset($options['start'])) {
			$hasLimit = $options['limit'];
			$start = $options['start'];

			$query[] = 'LIMIT ' . $options['start'] . ', ' . ($options['limit'] + 1);
		}

		$db->setQuery($query);

		$ids = $db->loadColumn();

		if (!$ids) {
			$this->_nextlimit = 0;
			return [];
		}

		if ($hasLimit && count($ids) > $hasLimit) {
			// remove the last item
			array_pop($ids);

			$this->_nextlimit = $start + $hasLimit;
		}

		return $ids;
	}

	/**
	 * Retrieves the next limit
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getNextLimit()
	{
		return $this->_nextlimit;
	}

	/**
	 * Retrieves the total votes of the poll/ poll item
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getTotalVotes($pollId, $itemId = null)
	{
		$db = EB::db();
		$query = [];

		$query[] = 'SELECT COUNT(1) FROM `#__easyblog_polls_users`';
		$query[] = 'WHERE `poll_id` = ' . $pollId;

		if ($itemId) {
			$query[] = 'AND `item_id` = ' . $itemId;
		}

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Retrieves the polls of the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getPolls($options = [])
	{
		$db = EB::db();
		$query = [];

		$state = EB::normalize($options, 'state', EB_PUBLISHED);
		$search = EB::normalize($options, 'search', '');
		$userId = EB::normalize($options, 'userId', null);
		$limit = EB::normalize($options, 'limit', null);
		$includeAll = EB::normalize($options, 'includeAll', false);

		$whereQuery = [];

		if ($state !== 'all') {
			if ($state == 'published') {
				$state = EB_PUBLISHED;
			}

			if ($state == 'published') {
				$state = EB_UNPUBLISHED;
			}

			$whereQuery[] = '`state` = ' . $db->Quote($state);
		}

		// Site admin can access to all
		if (!$includeAll && $userId) {
			$whereQuery[] = '`user_id` = ' . $db->Quote($userId);
		}

		if ($search) {
			$whereQuery[] = '`title` LIKE ' . $db->Quote('%' . $search . '%');
		}

		$whereQuery = count($whereQuery) ? 'WHERE ' . implode(' AND ', $whereQuery) : '';

		$orderQuery = [];
		$orderQuery[] = 'ORDER BY `id` DESC';
		$orderQuery = implode(' ', $orderQuery);

		$query[] = 'SELECT * FROM `#__easyblog_polls`';
		$query[] = $whereQuery;
		$query[] = $orderQuery;

		if ($limit) {
			$this->setState('limit', $limit);
			$limitQuery = 'LIMIT ' . $limit;

			$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');

			if ($limitstart) {
				$limitQuery = 'LIMIT ' . $limitstart . ',' . $limit;
			}

			$query[] = $limitQuery;
		}

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$results) {
			return [];
		}

		$polls = [];

		foreach ($results as $row) {
			$poll = EB::polls($row->id);

			$polls[] = $poll;
		}

		$query = [];
		$query[] = 'SELECT COUNT(1) FROM `#__easyblog_polls`';
		$query[] = $whereQuery;
		$query[] = $orderQuery;

		$db->setQuery($query);
		$this->total = $db->loadResult();

		return $polls;
	}

	/**
	 * Retrieve pagination for comments.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getPagination()
	{
		$pagination = EB::pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));

		return $pagination;
	}

	/**
	 * Perform the deletion of the polls given
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function deletePolls($pollIds)
	{
		$db = EB::db();

		if (is_array($pollIds)) {
			$pollIds = implode(',', $pollIds);
		}

		$query = [];
		$query[] = 'DELETE FROM `#__easyblog_polls`';
		$query[] = 'WHERE `id` IN (' . $pollIds . ')';

		$db->setQuery($query);
		$db->Query();

		$query = [];
		$query[] = 'DELETE FROM `#__easyblog_polls_items`';
		$query[] = 'WHERE `poll_id` IN (' . $pollIds . ')';

		$db->setQuery($query);
		$db->Query();

		$query = [];
		$query[] = 'DELETE FROM `#__easyblog_polls_users`';
		$query[] = 'WHERE `poll_id` IN (' . $pollIds . ')';

		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Perform the deletion of the items of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function deleteItems($pollId, $exclusion = [])
	{
		$db = EB::db();

		$query = [];
		$query[] = 'DELETE FROM `#__easyblog_polls_items`';
		$query[] = 'WHERE `poll_id` = ' . $pollId;

		if ($exclusion) {
			$query[] = 'AND `id` NOT IN (' . implode(',', $exclusion) . ')';
		}

		$db->setQuery($query);
		$db->Query();

		// We need to delete the voted of the deleted items as well
		$this->deleteVotedItems($pollId, $exclusion);
	}

	/**
	 * Perform the deletion of the voted items of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function deleteVotedItems($pollId, $exclusion = [])
	{
		$db = EB::db();

		$query = [];
		$query[] = 'DELETE FROM `#__easyblog_polls_users`';
		$query[] = 'WHERE `poll_id` = ' . $pollId;

		if ($exclusion) {
			$query[] = 'AND `item_id` NOT IN (' . implode(',', $exclusion) . ')';
		}

		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Retrieve the total number of the items of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getTotalItems($pollId)
	{
		$db = EB::db();
		$query = [];

		$query[] = 'SELECT COUNT(1) FROM `#__easyblog_polls_items`';
		$query[] = 'WHERE `poll_id` = ' . $pollId;

		$db->setQuery($query);

		return $db->loadResult();
	}
}