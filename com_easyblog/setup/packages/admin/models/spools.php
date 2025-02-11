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

use Foundry\Models\Mail;

class EasyBlogModelSpools extends Mail
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct(['fd' => EB::fd()]);
		$app = JFactory::getApplication();

		$limit = $app->getUserStateFromRequest('com_easyblog.categories.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart = $app->input->get('limitstart', 0, '', 'int');

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
		if (empty($this->_total)) {
			$query = $this->_buildQuery(false, true);

			$db = EB::db();
			$db->setQuery($query);

			$this->_total = $db->loadResult();

			// $this->_total = $this->_getListCount($query);
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
			$this->_pagination = EB::Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery($publishedOnly = false, $isCount = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($publishedOnly);
		$orderby = $this->_buildQueryOrderBy();
		$db = EB::db();

		$query = 'SELECT * ';

		if ($isCount) {
			$query = 'SELECT COUNT(1) ';
		}

		$query .= 'FROM ' . $db->nameQuote('#__easyblog_mailq');
		$query .= $where;

		$query .= $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();

		$filter_state = $mainframe->getUserStateFromRequest('com_easyblog.spools.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easyblog.spools.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$where = [];

		if ($filter_state) {
			if ($filter_state === 'P') {
				$where[] = $db->nameQuote('status') . '=' . $db->Quote('1');
			}

			if ($filter_state === 'U') {
				$where[] = $db->nameQuote('status') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER(subject) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		// we need to order by id so that the same date value will not messed up the sorting. #1047
		$orderby = ' ORDER BY `id` DESC';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData($usePagination = true)
	{
		$limit = $this->getState('limit', 0);
		$limitstart = $this->getState('limitstart', 0);

		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			if ($usePagination && $limit) {
				// $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));


				$db = EB::db();

				$query .= ' LIMIT ' . $limitstart . ', ' . $limit;
				$db->setQuery($query);

				$this->_data = $db->loadObjectList();
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}

	/**
	 * Purges all emails from the system
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function purge($type = '')
	{
		$db = EB::db();
		$query = [];
		$query[] = 'DELETE FROM ' . $db->qn('#__easyblog_mailq');

		if ($type == 'sent') {
			$query[] = 'WHERE ' . $db->qn('status') . '=' . $db->Quote(1);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Delete particular user email activities
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function removeUserEmailActivities($recipientEmail = '')
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_mailq');
		$query .= ' WHERE ' . $db->nameQuote('recipient') . '=' . $db->Quote($recipientEmail);

		$db->setQuery($query);
		$state = $db->Query();

		if (!$state) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves a list of excluded email template files for EasyBlog
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getExcludedFiles()
	{
		return ['attachment', 'comment'];
	}
}