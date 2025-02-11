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

class EasyBlogModelFields extends EasyBlogAdminModel
{
	public $pagination = null;
	public $_total = null;

	public function __construct()
	{
		$this->app = JFactory::getApplication();

		parent::__construct();

		$limit			= $this->app->getUserStateFromRequest( 'com_easyblog.fields.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart		= $this->input->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getTotal()
	{
		return $this->_total;
	}

	public function getPagination()
	{
		if (is_null($this->pagination)) {
			$this->pagination = EB::Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->pagination;
	}

	public function getItems()
	{
		$mainframe = JFactory::getApplication();
		$db	= EB::db();

		$filter_groups = $mainframe->getUserStateFromRequest('com_easyblog.fields.filter_groups', 'filter_groups', '', 'string');
		$search = $mainframe->getUserStateFromRequest('com_easyblog.fields.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields');

		$where = array();

		if ($filter_groups) {
			$where[] = $db->quoteName('group_id') . '=' . $db->Quote($filter_groups);
		}

		if ($search) {
			$where[] = 'LOWER(' . $db->quoteName('title') . ') LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where)? ' WHERE ' . implode(' AND ', $where) : '');

		$query[] = $where;

		$countQuery = implode(' ', $query);

		//total tag's post sql
		$totalQuery	= 'SELECT COUNT(1) FROM (';
		$totalQuery	.= $countQuery;
		$totalQuery	.= ') as x';

		// now we include the ordering and limits
		$filter_order = $mainframe->getUserStateFromRequest('com_easyblog.fields.filter_order', 'filter_order', 'id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easyblog.fields.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$query[] = 'ORDER BY '.$filter_order.' '.$filter_order_Dir;

		$limitStart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		$query[] = 'LIMIT ' . $limitStart . ',' . $limit;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		$db->setQuery($totalQuery);
		$this->_total = $db->loadResult();

		return $data;
	}

	/**
	 * Removes association between a custom field group and the category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function deleteGroupAssociation($id)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_category_fields_groups');
		$query .= ' WHERE ' . $db->quoteName('group_id') . '=' . $db->Quote($id);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Deletes custom fields values given the field id
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function deleteFieldValue($id)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_fields_values');
		$query .= ' WHERE ' . $db->quoteName('field_id') . '=' . $db->Quote($id);

		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Deletes associated field values for a particular blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function deleteBlogFields($id)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_fields_values');
		$query .= ' WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($id);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Retrieves the custom field value
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getFieldValues($fieldId, $blogId)
	{
		$db = EB::db();

		$values = array();

		if (EB::cache()->exists($blogId, 'posts')) {

			$data = EB::cache()->get($blogId, 'posts');

			if (isset($data['customfields']) && isset($data['customfields'][$fieldId])) {

				foreach($data['customfields'][$fieldId] as $item) {
					$values[] = $item;
				}

				return $values;
			}

			// no customfield values for this post.
			return array();

		} else {

			$query  = 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields_values');
			$query .= ' WHERE ' . $db->quoteName('field_id') . '=' . $db->Quote($fieldId);
			$query .= ' AND ' . $db->quoteName('post_id') . '=' . $db->Quote($blogId);
			$query .= ' ORDER BY ' . $db->quoteName('id');

			$db->setQuery($query);

			$result = $db->loadObjectList();

			if (!$result) {
				return $result;
			}

			foreach ($result as $row) {
				$value = EB::table('FieldValue');
				$value->bind($row);

				$values[] = $value;
			}

		}

		return $values;
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getBlogProperties($categoryId)
	{
		$db 	= EB::db();

		$query	= 'SELECT c.* FROM ' . $db->quoteName('#__easyblog_category_fields_groups') . ' AS a';

		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_fields_groups') . ' AS b';
		$query .= ' on a.' . $db->quoteName('group_id') . ' = b.' . $db->quoteName('id');
		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_fields') . ' AS c';
		$query .= ' on c.' . $db->quoteName('group_id') . ' = b.' . $db->quoteName('id');
		$query .= ' WHERE a.' . $db->quoteName('category_id') . '=' . $db->Quote($categoryId);

		$db->setQuery($query);

		$fields	= $db->loadObjectList();

		// dump($fields);
	}

	/**
	 * Retrieve a list of custom field groups on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getGroups()
	{
		$db 	= EB::db();

		$query	= 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields_groups');

		$db->setQuery($query);

		$groups	= $db->loadObjectList();

		return $groups;
	}


	/**
	 * Preload a list of custom fields for each posts
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function preloadFields($postIds = array())
	{
		$db = EB::db();

		if (!$postIds) {
			return array();
		}

		$query = 'select '.$db->qn('a.id').' as '.$db->qn('cat_fg_id').', '.$db->qn('a.category_id').' as '.$db->qn('cat_fg_category_id').', '.$db->qn('a.group_id').' as '.$db->qn('cat_fg_group_id').', ';
		$query .= $db->qn('fg.id').' as '.$db->qn('fg_id').', '.$db->qn('fg.title').' as '.$db->qn('fg_title').', '.$db->qn('fg.description').' as '.$db->qn('fg_description').', '.$db->qn('fg.created').' as '.$db->qn('fg_created').', '.$db->qn('fg.state').' as '.$db->qn('fg_state').', '.$db->qn('fg.read').' as '.$db->qn('fg_read').', '.$db->qn('fg.write').' as '.$db->qn('fg_write').', '.$db->qn('fg.params').' as '.$db->qn('fg_params').', ';
		$query .= $db->qn('f.id').' as '.$db->qn('f_id').', '.$db->qn('f.group_id').' as '.$db->qn('f_group_id').', '.$db->qn('f.title').' as '.$db->qn('f_title').', '.$db->qn('f.help').' as '.$db->qn('f_help').', '.$db->qn('f.state').' as '.$db->qn('f_state').', '.$db->qn('f.required').' as '.$db->qn('f_required').', '.$db->qn('f.type').' as '.$db->qn('f_type').', '.$db->qn('f.params').' as '.$db->qn('f_params').', '.$db->qn('f.created').' as '.$db->qn('f_created').', '.$db->qn('f.options').' as '.$db->qn('f_options').', ' . $db->qn('f.ordering').' as '.$db->qn('f_ordering') . ',';
		$query .= $db->qn('fv.id').' as '.$db->qn('fv_id').', '.$db->qn('fv.field_id').' as '.$db->qn('fv_field_id').', '.$db->qn('fv.post_id').' as '.$db->qn('fv_post_id').', '.$db->qn('fv.value').' as '.$db->qn('fv_value');
		$query .= ' from '.$db->qn('#__easyblog_category_fields_groups').' as a';
		$query .= ' inner join '.$db->qn('#__easyblog_post_category').' as p on '.$db->qn('a.category_id').' = ' . $db->qn('p.category_id');
		$query .= '	inner join '.$db->qn('#__easyblog_fields_groups').' as fg on '.$db->qn('a.group_id').' = '.$db->qn('fg.id');
		$query .= '	inner join '.$db->qn('#__easyblog_fields').' as f on '.$db->qn('fg.id').' = '.$db->qn('f.group_id');
		$query .= '	left join '.$db->qn('#__easyblog_fields_values').' as fv on '.$db->qn('fv.field_id').' = '.$db->qn('f.id').' and '.$db->qn('fv.post_id').' = '.$db->qn('p.post_id');
		$query .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_fields_groups_acl') . ' AS acl';
		$query .= ' ON fg.' . $db->quoteName('id') . ' = acl.' . $db->quoteName('group_id');
		if (count($postIds) == 1) {
			$query .= ' where '.$db->qn('p.post_id').' = '.$db->Quote($postIds[0]);
		} else {
			$query .= ' where p.post_id IN ('.implode(',',$postIds).')';
		}

		$gid = EB::getUserGids();
		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $id : ',' . $id;
			}
		}

		// We need to check whether the user is belong to one of the group
		$query .= ' AND (';
		$query .= ' acl.' . $db->quoteName('acl_id') . ' IN(' . $gids . ')';
		$query .= ' AND acl.' . $db->quoteName('acl_type') . ' = ' . $db->Quote('read');
		$query .= ' OR acl.' . $db->quotename('id') . ' IS NULL';
		$query .= ' )';

		// echo $query . '<br/><br/>';

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Allow caller to save search filter
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function saveSearchFilter($details = '', $cid = 0)
	{
		$db = EB::db();

		// Get the current logged in user
		$user = EB::user();

		// Check if the record is already exist
		$query	= 'SELECT `id` FROM ' . $db->nameQuote('#__easyblog_fields_filter')
				. ' WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($user->id)
				. ' AND '  . $db->nameQuote('cid') . ' = ' . $db->Quote($cid);

		$db->setQuery($query);
		$id = $db->loadResult();

		if ($id) {
			$sql = 'UPDATE ' . $db->nameQuote('#__easyblog_fields_filter') . ' SET ' . $db->qn('params') . ' = ' . $db->Quote($details);
			$sql .= ' WHERE ' . $db->qn('id') . ' = ' . $db->Quote($id);
		} else {
			$sql = 'INSERT INTO ' . $db->nameQuote('#__easyblog_fields_filter') . ' (`user_id`, `cid`, `params`) VALUES ';
			$sql .= '(' . $db->Quote($user->id) . ', ' . $db->Quote($cid) . ', ' . $db->Quote($details) . ')';
		}

		$db->setQuery($sql);

		return $result = $db->query();
	}


	/**
	 * Retrieves the field custom class value
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getFieldClassValue($fieldId, $postId)
	{
		$db = EB::db();

		$query  = 'SELECT * FROM ' . $db->qn('#__easyblog_fields_values');
		$query .= ' WHERE ' . $db->qn('field_id') . '=' . $db->Quote($fieldId);
		$query .= ' AND ' . $db->qn('post_id') . '=' . $db->Quote($postId);
		$query .= ' ORDER BY ' . $db->qn('id');

		$db->setQuery($query);

		$results = $db->loadObjectList();

		if (!$results) {
			return $results;
		}

		// always get the first array class name
		foreach ($results as $result) {
			$value = $result->class_name;
		}

		return $value;
	}

	/**
	 * Allow caller to clear search filter
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function clearSearchFilter($cid = 0)
	{
		$db = EB::db();

		// Get the current logged in user
		$user = EB::user();

		// Check if the record is already exist
		$query	= 'DELETE FROM ' . $db->nameQuote('#__easyblog_fields_filter')
				. ' WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($user->id)
				. ' AND '  . $db->nameQuote('cid') . ' = ' . $db->Quote($cid);

		$db->setQuery($query);
		return $result = $db->query();
	}

	/**
	 * Determine if the logged in user has filter saved
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getSavedFilter($cid = 0)
	{
		$db = EB::db();

		// Get the current logged in user
		$user = EB::user();

		if (!$user->id) {
			return false;
		}

		$query	= 'SELECT * FROM ' . $db->nameQuote('#__easyblog_fields_filter')
				. ' WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($user->id)
				. ' AND '  . $db->nameQuote('cid') . ' = ' . $db->Quote($cid);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Rebuiling ordering
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function rebuildOrdering($groupId = 0)
	{
		$db = EB::db();
		$groups = array($groupId);

		if (!$groupId) {
			// this mean we need to rebuild all custom fields for each groups. 
			// lets get the groups 1st.

			$query = "select `id` from `#__easyblog_fields_groups` order by `id`;";
			$db->setQuery($query);

			$groups = $db->loadColumn();
		}

		if ($groups) {
			foreach ($groups as $id) {

				$querySet1 = "SET @ordering_interval = 1";
				$querySet2 = "SET @new_ordering = 0";

				$query = "UPDATE `#__easyblog_fields` SET";
				$query .= " `ordering` = (@new_ordering := @new_ordering + @ordering_interval)";
				$query .= " WHERE `group_id` = " . $db->Quote($id);
				$query .= " ORDER BY `ordering` ASC";

				// execute ordering_interval variable initiation.
				$db->setQuery($querySet1);
				$db->query();

				// execute new_ordering variable initiation.
				$db->setQuery($querySet2);
				$db->query();

				// now perform the update
				$db->setQuery($query);
				$db->query();
			}
		}

		return true;
	}


	/**
	 * Rebuiling ordering
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function getMaxOrdering($groupId)
	{
		$db = EB::db();

		$query = "SELECT MAX(`ordering`) FROM `#__easyblog_fields` WHERE `group_id` = " . $db->Quote($groupId);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result ? $result : 0;
	}
}
