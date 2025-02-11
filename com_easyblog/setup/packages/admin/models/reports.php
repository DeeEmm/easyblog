<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelReports extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;
	public $data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.reports.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

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
		if (!$this->total) {
			$query = $this->_buildQuery(false, true);
			$this->total = $this->_getListCount($query);
		}

		return $this->total;
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
		if (!$this->pagination) {
			$this->_pagination = EB::Pagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery($publishedOnly = false, $totalOnly = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($publishedOnly);

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_reports') . ' AS a';
		$query[] = $where;

		if (! $totalOnly) {
			$orderby = $this->_buildQueryOrderBy();
			$query[] = $orderby;
		}

		$query = implode(' ', $query);

		return $query;
	}

	/**
	 * Builds the where clause
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function _buildQueryWhere()
	{
		$db = EB::db();
		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.reports.filter_state', 'filter_state', '', 'word');
		$search	= $this->app->getUserStateFromRequest('com_easyblog.reports.search', 'search', '', 'string');
		$search	= $db->getEscaped(trim(EBString::strtolower($search)));

		$where = array();

		if ($search) {
			$where[] = ' LOWER( `reason` ) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode (' AND ', $where) : '');

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
		$order = $this->app->getUserStateFromRequest('com_easyblog.reports.filter_order', 'filter_order', 'a.created', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easyblog.reports.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$orderby = 'ORDER BY ' . $order . ' ' . $direction;

		return $orderby;
	}

	/**
	 * Delete reports for the given id and type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteReports($id, $type)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_reports');
		$query[] = 'WHERE ' . $db->quoteName('obj_id') . '=' . $db->Quote($id);
		$query[] = 'AND ' . $db->nameQuote('obj_type') . '=' . $db->Quote($type);

		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Method to get categories item data
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getData($usePagination = true)
	{
		if (!$this->data) {
			$query = $this->_buildQuery();

			if ($usePagination) {
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}

	/**
	 * Retrieve lists of user's reported posts
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function getReportPosts($options = array())
	{
		$db = EB::db();

		// Normalize options
		$search = isset($options['search']) && $options['search'] ? $options['search'] : '';
		$limit = isset($options['limit']) && $options['limit'] ? $options['limit'] : 0;
		$limit = ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');

		// we need to reset the limit state.
		if ($limit) {
			$this->setState('limit', $limit);
		}

		$limitSQL = 'LIMIT ' . $limitstart . ',' . $limit;

		$mainQuery = array();
		$query = array();

		// Only append this SQL query during search
		if ($search) {

			// reset the main query for retrieve specific data only
			$mainQuery = array();
			$querySelector = 'SELECT a.' . $db->quoteName('id') . ', a.' . $db->quoteName('obj_id') . ', a.' . $db->quoteName('obj_type') . ', a.' . $db->quoteName('reason');
			$querySelector .= ', a.' . $db->quoteName('created_by') . ', p.' . $db->quoteName('title') . ', p.' . $db->quoteName('published');

			$mainQuery[] = 'FROM ' . $db->qn('#__easyblog_reports') . ' AS a';
			$mainQuery[] = 'INNER JOIN ' . $db->qn('#__easyblog_post') . ' AS p';
			$mainQuery[] = 'ON a.' . $db->quoteName('obj_id') . ' = p.' . $db->quoteName('id'); 
			$mainQuery[] = 'WHERE p.' . $db->quoteName('title') . ' LIKE (' . $db->Quote('%' . $search . '%') . ')';
		} else {
			$querySelector = 'SELECT *';
			$mainQuery[] = 'FROM ' . $db->qn('#__easyblog_reports') . ' AS a';			
		}

		$mainQuery[] = 'ORDER BY a.`id` DESC';

		$mainQuery = implode(' ', $mainQuery);

		$query[] = $querySelector;
		$query[] = $mainQuery;
		$query[] = $limitSQL;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$results) {
			return $results;
		}

		// Run query for pagination
		$querySelector = 'SELECT count(1)';
		$query = array();

		$query[] = $querySelector;
		$query[] = $mainQuery;

		$query = implode(' ', $query);
		$db->setQuery($query);

		$this->total = $db->loadResult();

		$reports = array();

		if ($results) {

			foreach ($results as $result) {

				$report = EB::table('Report');
				$report->bind($result);

				$post = EB::table('Post');
				$post->load($report->obj_id);

				$report->blog = $post;
				$reports[] = $report;
			}
		}

		return $reports;
	}	
}
