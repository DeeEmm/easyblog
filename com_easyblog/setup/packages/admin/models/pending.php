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

class EasyBlogModelPending extends EasyBlogAdminModel
{
	public $data = null;
	public $pagination = null;
	public $total;
	public $searchables = array('id');

	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.pending.limit', 'limit', EB::getLimit() , 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves a list of pending posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getBlogs($withOrder = false)
	{
		if (!$this->data) {
			$query = $this->buildQuery($withOrder);

			$this->data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->data;
	}

	/**
	 * Builds the query for pending posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function buildQuery($withOrder = false, $isCount = false)
	{
		$db = EB::db();

		$tagFilter = $this->input->get('tagid', '', 'int');

		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.pending.filter_category', 'filter_category', '', 'int');

		$query = array();

		// build query header
		$header = 'SELECT a.`id` as `revision_id`, a.`post_id` as `id`, a.`title`, a.`created`, a.`modified`, a.`created_by`,';
		$header .= ' a.`content`, a.`state`, a.`ordering`, b.`source_type`, b.`source_id`';

		if ($isCount) {
			$header = 'SELECT count(a.`id`)';
		}

		$query[] = $header;
		$query[] = 'FROM ' . $db->qn('#__easyblog_revisions') . ' AS a';
		$query[] = ' inner join ' . $db->qn('#__easyblog_post') . ' as b on a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');

		if ($categoryFilter) {
			$query[] = ' LEFT JOIN ' . $db->quoteName('#__easyblog_post_category') . ' AS cat';
			$query[] = ' ON b.' . $db->quoteName('id') . ' = cat.' . $db->quoteName('post_id');
		}

		$query[] = $this->_buildQueryWhere();
		if ($withOrder) {
			$query[] = $this->_buildQueryOrderBy();
		}


		$query = implode(' ', $query);

		return $query;
	}

	public function _buildQueryWhere()
	{
		$db = EB::db();

		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.pending.filter_category', 'filter_category', '', 'int');
		
		$where = array();
		$where[] = ' a.`state` = ' . $db->Quote(EASYBLOG_REVISION_PENDING);

		// Process search
		$search = $this->app->getUserStateFromRequest('com_easyblog.pending.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));


		if ($search) {
			// If there is a : in the search query
			$column = 'b.title';
			$value = $search;

			$customSearch = $this->getSearchableItems($search);

			if ($customSearch) {
				$column = 'b.' . strtolower($customSearch->column);
				$value = $customSearch->query;
			}

			$where[] = $db->qn($column) . ' LIKE (' . $db->Quote('%' . $value . '%') . ')';
		}

		if ($categoryFilter) {
			$where[] = ' cat.`category_id` = ' . $db->Quote($categoryFilter);
		}

		$where = count($where) ? ' WHERE ' . implode(' AND ', $where) : '' ;

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easyblog.pending.filter_order', 'filter_order', 'a.post_id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easyblog.pending.filter_order_Dir','filter_order_Dir', 'DESC', 'word');

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Retrieves total number of pending blog posts by a specific user
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalPending($userId)
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_revisions');
		$query .= ' WHERE ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_REVISION_PENDING);
		$query .= ' AND ' . $db->quoteName('created_by') . '=' . $db->Quote($userId);

		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total number of pending posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotal()
	{
		if (!$this->total) {

			$db = EB::db();

			$query = $this->buildQuery(false, true);

			$db->setQuery($query);
			$result = $db->loadResult();

			$this->total = (int) $result;
		}

		return $this->total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		if (!$this->pagination) {
			$this->pagination = EB::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->pagination;
	}
}
