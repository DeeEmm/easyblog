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

class EasyBlogModelFieldGroups extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;

	function __construct()
	{
		parent::__construct();

		$this->app = JFactory::getApplication();
		$limit = $this->app->getUserStateFromRequest( 'com_easyblog.fieldgroups.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (is_null($this->total)) {
			$db = EB::db();

			$query = array();
			$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields_groups');

			$query = implode(' ', $query);

			$db->setQuery($query);

			$total = $db->loadResult();

			$this->total = $total;
		}

		return $this->total;
	}

	public function getPagination()
	{
		if (is_null($this->pagination)) {
			$this->pagination = EB::Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->pagination;
	}

	public function getItems($groups = false)
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();

		$where = array();

		if ($groups) {
			$search = $mainframe->getUserStateFromRequest('com_easyblog.fields.groupSearch', 'search', '', 'string');
			$search = trim(EBString::strtolower($search));

			if ($search) {
				$where[] = 'LOWER(' . $db->quoteName('title') . ') LIKE \'%' . $search . '%\' ';
			}

			$filter_state = $mainframe->getUserStateFromRequest('com_easyblog.fields.filter_state', 'filter_state', '*', 'string');

			if ($filter_state) {
				if ($filter_state == 'P') {
					$where[] = '`state` = ' . $db->Quote('1');
				}
				if ($filter_state == 'U') {
					$where[] = '`state` = ' . $db->Quote('0');
				}
			}
		}
		
		$query = array();

		$pagination = $this->getPagination();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields_groups');

		$where = (count($where)? ' WHERE ' . implode(' AND ', $where) : '');

		$query[] = $where;
		$query[] = 'LIMIT ' . $pagination->limitstart . ',' . $pagination->limit;

		$query 	= implode(' ', $query);

		$db->setQuery($query);
		$data	= $db->loadObjectList();

		return $data;
	}

	/**
	 * Determines if there are any values in the fields within this group.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasValues($postId, $groupId)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_fields_values') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_fields') . ' AS b';
		$query[] = 'ON a.' . $db->qn('field_id') . ' = b.' . $db->qn('id');
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_fields_groups') . ' AS c';
		$query[] = 'ON b.' . $db->qn('group_id') . '= c.' . $db->qn('id');
		$query[] = 'WHERE c.' . $db->qn('id') . '=' . $db->Quote($groupId);
		$query[] = 'AND a.' . $db->qn('post_id') . '=' . $db->Quote($postId);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$hasValues = $db->loadResult() > 0 ? true : false;

		return $hasValues;
	}
}
