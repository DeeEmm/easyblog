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

require_once(__DIR__ . '/model.php');

class EasyBlogModelTemplates extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;

	public function __construct()
	{
		parent::__construct();

		// Get the limit
		$limit = $this->app->getCfg('list_limit') == 0 ? 5 : $this->app->getCfg('list_limit');

		// Get the current limit start
		$limitstart = $this->input->get('limitstart', 0, 'int');

		if ($limit != 0) {
			$limitstart = (int) floor( ( $limitstart / $limit ) * $limit );
		} else {
			$limitstart = 0;
		}

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the total items
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotal()
	{
		if (!$this->total) {
			$query = $this->buildQuery();

			$db = EB::db();
			$db->setQuery($query);
			$this->total = $db->loadResult();
		}

		return $this->total;
	}

	/**
	 * Retrieve the pagination for the templates
	 *
	 * @sicne 4.0
	 * @access public
	 * @return
	 */
	public function getPagination()
	{
		return $this->pagination;
	}

	/**
	 * Builds the main query to retrieve post templates
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function buildQuery($options = array())
	{
		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post_templates');

		if (isset($options['user_id'])) {
			$query .= ' WHERE ' . $db->quoteName('user_id') . '=' . $db->Quote($options['user_id']);
		}

		$db->setQuery($query);
	}

	/**
	 * Retrieves a list of templates
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItems($options = array())
	{
		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post_templates');

		$where = array();

		if (isset($options['user_id'])) {
			$where[] = $db->quoteName('user_id') . '=' . $db->Quote($options['user_id']);
		}

		$search = $this->app->getUserStateFromRequest('com_easyblog.templates.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		// override search input if exists
		$search = $this->input->get('search', $search, 'default');

		if ($search) {
			$where[] = ' LOWER(' . $db->quoteName('title') . ') LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$query .= $where;

		$ordering = $this->app->getUserStateFromRequest('com_easyblog.templates.filter_order', 'filter_order', 'ordering', 'cmd');
		$orderingDirection = $this->app->getUserStateFromRequest('com_easyblog.templates.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$query .= ' ORDER BY ' . $ordering . ' ' . $orderingDirection . ', ' . $db->qn('ordering');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Get the total for pagination
		$query = str_ireplace('*', 'COUNT(1)', $query);
		$db->setQuery($query);
		$total = $db->loadResult();

		$this->pagination = EB::pagination($total, $this->getState('limitstart'), $this->getState('limit'));


		return $result;
	}

	/**
	 * Retrieves a list of system templates
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSystemTemplates()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_post_templates');
		$query[] = 'WHERE ' . $db->qn('system') . '=' . $db->Quote(1);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$templates = $db->loadObjectList();

		return $templates;
	}

	/**
	 * Retrieves a list of post templates
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostTemplates($userId = null, $includeUnpublish = false, $includeGlobal = true, $docType = 'all', $limit = null)
	{
		$user = JFactory::getUser($userId);
		$id = $user->id;

		$db = EB::db();

		$query = array();

		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');
		$limitSQL = 'LIMIT ' . $limitstart . ',' . $limit;

		$query[] = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $db->qn('#__easyblog_post_templates');
		$query[] = 'WHERE (' . $db->qn('user_id') . '=' . $db->Quote($id);

		if ($includeGlobal || FH::isSiteAdmin()) {
			$query[] = 'OR ' . $db->qn('system') . '=' . $db->Quote(1);
			$query[] = 'OR ' . $db->qn('system') . '=' . $db->Quote(2);
		}

		$query[] = ')';

		if (!$includeUnpublish) {
			$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);
		}

		$search = $this->input->get('search', '', 'default');

		if ($search) {
			$query[] = ' AND LOWER(' . $db->qn('title') . ') LIKE \'%' . $search . '%\' ';
		}

		// Post template type
		if ($docType != 'all') {
			$query[] = ' AND ' . $db->qn('doctype') . '=' . $db->Quote($docType);
		}

		$query[] = ' ORDER BY ' . $db->qn('ordering');

		// Apply pagination limit
		if ($limit) {
			$limit	= ($limit == 0) ? $this->getState('limit') : $limit;
			$this->setState('limit', $limit);

			$query[] = $limitSQL;
		}

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$cntQuery = 'select FOUND_ROWS()';
		$db->setQuery($cntQuery);

		$total = $db->loadResult();

		// Set pagination
		$this->pagination = EB::pagination($total, $this->getState('limitstart'), $this->getState('limit'));

		$templates = array();

		foreach ($result as $row) {
			$template = EB::table('PostTemplate');
			$template->bind($row);

			$templates[] = $template;
		}

		return $templates;
	}
}






