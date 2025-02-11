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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelAcls extends EasyBlogAdminModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest('com_easyblog.acls.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

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
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getJoomlaGroupRulesets()
	{
		$sql = 'SELECT * FROM ';

		return $this->_data;
	}

	public function getRules($key='')
	{
		$db = EB::db();
		$sql = 'SELECT * FROM '.$db->nameQuote('#__easyblog_acl').' WHERE `published`=1 ORDER BY `id` ASC';
		$db->setQuery($sql);

		return $db->loadObjectList($key);
	}

	public function deleteRuleset($cid)
	{
		if (empty($cid)) {
			return false;
		}

		$db = EB::db();

		$sql = 'DELETE FROM ' . $db->nameQuote('#__easyblog_acl_group') . ' WHERE '. $db->nameQuote('content_id') . ' = ' . $db->quote($cid) . ' AND `type` = ' . $db->quote('group');
		$db->setQuery($sql);
		$result = $db->query();

		return $result;
	}

	public function insertRuleset($cid, $saveData)
	{
		$db = EB::db();

		$rules = $this->getRules('action');

		$type = 'group';

		$newruleset = array();

		foreach ($rules as $rule) {
			$action = $rule->action;
			$str = "(".$db->quote($cid).", ".$db->quote($rule->id).", ".$db->quote($saveData[$action]).", ".$db->quote($type).")";
			array_push($newruleset, $str);
		}

		if (!empty($newruleset)) {
			$sql = 'INSERT INTO ' . $db->nameQuote('#__easyblog_acl_group') . ' (`content_id`, `acl_id`, `status`, `type`) VALUES ';
			$sql .= implode(',', $newruleset);
			$db->setQuery($sql);

			return $result = $db->query();
		}

		return true;
	}

	public function getRuleSet($cid, $add=false)
	{
		$db = EB::db();

		$rulesets = new stdClass();
		$rulesets->rules = new stdClass();

		//get rules
		$rules = $this->getRules('id');
		foreach ($rules as $rule) {
			$rulesets->rules->{$rule->action} = (INT)$rule->default;
		}

		if (!$add) {
			//get user
			$query = $this->_buildQuery($cid);
			$db->setQuery($query);
			$row = $db->loadObject();
			$rulesets->id 	= $row->id;
			$rulesets->name = $row->name;
			$rulesets->level = '0';

			//get acl group ruleset
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__easyblog_acl_group') . ' WHERE '. $db->nameQuote('content_id') . ' = ' . $db->quote($cid) .' AND '. $db->nameQuote('type') . ' = ' . $db->quote('group');
			$db->setQuery($sql);
			$row = $db->loadAssocList();

			if (count($row) > 0) {
				foreach($row as $data) {
					if (isset($rules[$data['acl_id']])) {
						$action = $rules[$data['acl_id']]->action;
						$rulesets->rules->{$action} = $data['status'];
					}
				}
			}
		}

		return $rulesets;
	}

	/**
	 * Retrieves a list of acl rules on the site
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRuleSets($cid='')
	{
		$db = EB::db();

		$rulesets = new stdClass();
		$ids = array();

		$rules = $this->getRules('id');

		//get user
		$query = $this->_buildQuery($cid);

		$pagination = $this->getPagination();
		$rows = $this->_getList($query, $pagination->limitstart, $pagination->limit);

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$rulesets->{$row->id} = new stdClass();
				$rulesets->{$row->id}->id = $row->id;
				$rulesets->{$row->id}->name = $row->name;
				$rulesets->{$row->id}->level = $row->level;

				foreach ($rules as $rule) {
					$rulesets->{$row->id}->{$rule->action} = (INT)$rule->default;
				}

				array_push($ids, $row->id);
			}

			//get acl group ruleset
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__easyblog_acl_group') . ' WHERE '. $db->nameQuote('type') . ' = ' . $db->quote('group') . ' AND `content_id` IN (' . implode(' , ', $ids) . ')';
			$db->setQuery($sql);
			$acl = $db->loadAssocList();

			foreach ($acl as $data) {
				if (isset($rules[$data['acl_id']])) {
					$action = $rules[$data['acl_id']]->action;
					$rulesets->{$data['content_id']}->{$action} = $data['status'];
				}
			}
		}

		return $rulesets;
	}

	/**
	 * Prepares the SQL query
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function _buildQuery($cid='')
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->qn('a.id') . ', ' . $db->qn('a.title') . ' AS ' . $db->qn('name') . ', COUNT(DISTINCT '. $db->qn('b.id') . ') AS ' . $db->qn('level');
		$query .= ' , GROUP_CONCAT(' . $db->qn('b.id') . ' SEPARATOR \',\') AS parents';
		$query .= ' FROM ' . $db->qn('#__usergroups') . ' AS a';
		$query .= ' LEFT JOIN ' . $db->qn('#__usergroups') . ' AS b';
		$query .= ' ON ' . $db->qn('a.lft') . ' > ' . $db->qn('b.lft');
		$query .= ' AND ' . $db->qn('a.rgt') . ' < ' . $db->qn('b.rgt');

		$where = $this->_buildQueryWhere($cid);
		$orderby = $this->_buildQueryOrderBy();

		$query 	.= $where . ' ' . $orderby;

		return $query;
	}

	public function _buildQueryWhere($cid='')
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();

		$search = $mainframe->getUserStateFromRequest('com_easyblog.acls.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$where = array();

		if (empty($cid) && $search) {
			$where[] = ' LOWER(' . $db->qn('a.title') . ') LIKE \'%' . $search . '%\' ';
		}
		else if ($cid) {
			$where[] = 'a.`id` = ' . $db->quote($cid);
		}

		$where = (count($where) ? ' WHERE ' .implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easyblog.acls.filter_order', 'filter_order', 'a.`id`', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easyblog.acls.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$orderby = ' GROUP BY a.id';
		$orderby .= ' ORDER BY a.lft ASC';

		return $orderby;
	}
}
