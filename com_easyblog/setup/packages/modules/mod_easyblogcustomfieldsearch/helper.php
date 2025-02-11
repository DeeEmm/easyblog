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

jimport('joomla.system.file');
jimport('joomla.system.folder');

class modCustomFieldHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	public function getCustomFields($options)
	{
		$db = EB::db();

		$query = 'SELECT a.* FROM ' . $db->quoteName('#__easyblog_fields') . ' AS a';
		$query .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_fields_groups_acl') . ' AS acl';
		$query .= ' ON a.' . $db->quoteName('group_id') . ' = acl.' . $db->quoteName('group_id');

		$query .= ' WHERE a.' . $db->quoteName('group_id') . '=' . $db->Quote($options['groupId']);
		$query .= ' AND a.' . $db->quoteName('state') . '=' . $db->Quote(1);

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

		$query .= ' GROUP BY a.' . $db->quoteName('id');
		$query .= ' ORDER BY a.' . $db->quoteName($options['sorting']) . ' ASC';

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		// Here we need to check if this is already filtered page
		// Check if this is filter by custom field
		$filter = $this->lib->input->get('filter', false);
		$filteredFields = array();

		$savedFilters = EB::model('fields')->getSavedFilter($options['catId']);

		if ($filter == 'field') {
			$filterVars = $this->lib->input->input->getArray();

			foreach ($filterVars as $key => $value) {

				if (strpos($key, 'field') !== false) {
					$fieldId = explode('-', $key);
					$fieldId = $fieldId[1];

					$filteredFields[$fieldId] = $filterVars[$key];
				}
			}
		} else if ($savedFilters) {
			$params = json_decode($savedFilters->params);

			foreach ($params as $filter) {
				if (strpos($filter->name, 'field') !== false) {
					$fieldId = explode('-', $filter->name);
					$fieldId = $fieldId[1];

					$filteredFields[$fieldId][] = $filter->value;
				}
			}
		}

		// Get the fields library
		$lib = EB::fields();

		// Initialize the default value
		$fields = array();
		$allowedFields = array('checkbox', 'radio', 'select');

		foreach ($result as $row) {
			$field = EB::table('Field');
			$field->bind($row);

			$field->options = json_decode($field->options);

			if (in_array($field->type, $allowedFields)) {
				foreach ($field->options as $option) {
					$option->checked = false;
					// Assign back the selected filter
					if (isset($filteredFields[$field->id])) {
						if (in_array($option->value, $filteredFields[$field->id])) {
							$option->checked = true;
						}
					}
				}

				$fields[] = $field;
			}
		}

		return $fields;
	}
}
