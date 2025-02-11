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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelDrafts extends EasyBlogAdminModel
{
	private $data = null;
	private $pagination = null;
	private $total = null;

	public function __construct()
	{
		parent::__construct();

		// //get the number of events from database
		// $limit  = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		// $limitstart = $this->input->get('limitstart', 0, 'int');
		//
		$useLimit 		= $this->config->get('layout_listlength') == 0 ? $this->app->getCfg('list_limit') : $this->config->get( 'layout_listlength' );
		$limit			= $this->app->getUserStateFromRequest( 'com_easyblog.revisions.limit', 'limit', $useLimit , 'int');
		$limitstart		= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the list of draft items
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItems()
	{
		if ($this->data) {
			return $this->data;
		}

		$query = $this->buildItemsQuery();

		$this->data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		return $this->data;
	}

	private function buildItemsQuery()
	{
		$db	 = EB::db();

		$query = 'select a.* from ' . $db->qn('#__easyblog_revisions') . ' as a';
		$query .= ' inner join ' . $db->qn('#__easyblog_post') . ' as b on a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');
		$query .= ' where a.' . $db->qn('state') . ' = ' . $db->Quote(EASYBLOG_REVISION_DRAFT);

		return $query;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getItemsTotal()
	{
		// Load total number of rows
		if (!$this->total) {

			$db = EB::db();

			$query = 'select count(1) from ' . $db->qn('#__easyblog_revisions') . ' as a';
			$query .= ' inner join ' . $db->qn('#__easyblog_post') . ' as b on a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');
			$query .= ' where a.' . $db->qn('state') . ' = ' . $db->Quote(EASYBLOG_REVISION_DRAFT);

			$db->setQuery($query);

			$this->total 	= $db->loadResult();
		}

		return $this->total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function getItemsPagination()
	{
		if ($this->pagination) {
			return $this->pagination;
		}

		$this->pagination = EB::Pagination( $this->getItemsTotal(), $this->getState('limitstart'), $this->getState('limit') );
		return $this->pagination;
	}

	/**
	 * methods used in frontend
	 */

	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if ($this->total) {
			return $this->total;
		}

		$query = $this->_buildQuery();
		$this->total = @$this->_getListCount($query);

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
		if ($this->pagination) {
			return $this->pagination;
		}

		jimport('joomla.html.pagination');
		$this->pagination	= EB::pagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );

		return $this->pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery()
	{
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.* FROM ' . $db->qn('#__easyblog_revisions') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_post') . ' AS b';
		$query[] = 'ON a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');
		$query[] = $where;
		$query[] = $orderby;

		$query = implode(' ', $query);

		return $query;
	}

	/**
	 * Builds the where clause for drafts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function _buildQueryWhere()
	{
		$db = EB::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.revisions.filter_state', 'filter_state', '', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.revisions.search', 'search', '', 'string');
		$search = $db->getEscaped(EBString::strtolower($search));

		$where = array();

		$where[] = 'a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_REVISION_DRAFT);

		// Get draft created by the current user
		$my = JFactory::getUser();

		$where[] = 'a.' . $db->qn('created_by') . '=' . $db->Quote($my->id);

		if ($search) {
			$where[] = ' LOWER( a.' . $db->qn('title') . ' ) LIKE \'%' . $search . '%\' ';
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.revisions.filter_order', 		'filter_order', 	'created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.revisions.filter_order_Dir',	'filter_order_Dir',	'desc', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData( $usePagination = true )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			if ($usePagination) {
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}

	/*
	 * Discards all drafts for the specific user.
	 *
	 * @param	int		$userID		The subject's id.
	 * @return	boolean	true on success false otherwise.
	 */
	public function discard( $userId )
	{
		$db		= EB::db();
		// $query	= 'DELETE FROM ' . $db->qn( '#__easyblog_drafts' ) . ' '
		// 		. 'WHERE ' . $db->qn( 'created_by' ) . '=' . $db->Quote( $userId ) . ''
		// 		. 'AND `pending_approval` = ' . $db->Quote( '0' );

		// $db->setQuery( $query );
		// return $db->Query();
	}
}
