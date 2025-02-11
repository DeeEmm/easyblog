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

class EasyBlogViewFields extends EasyBlogAdminView
{
	/**
	 * Displays available custom fields.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.fields');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Set page details
		$this->setHeading('COM_EASYBLOG_FIELDS_TITLE', '', 'fa-list-alt');

		$filter_groups = $this->app->getUserStateFromRequest( 'com_easyblog.fields.filter_groups', 'filter_groups', '*', 'string' );
		$search = $this->app->getUserStateFromRequest( 'com_easyblog.fields.search', 'search', '', 'string' );
		$search = EBString::trim(EBString::strtolower($search));

		$order = $this->app->getUserStateFromRequest('com_easyblog.fields.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.fields.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		JToolbarHelper::addNew('fields.add');
		JToolbarHelper::divider();
		JToolbarHelper::publishList('fields.publish');
		JToolbarHelper::unpublishList('fields.unpublish');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_FIELDS_DELETE_CONFIRMATION'), 'fields.delete');

		$showOrdering = (!$filter_groups || $filter_groups == '*') ? false : true;

		// Get the list of custom fields on the site
		$model = EB::model('Fields');
		$rows = $model->getItems();
		$limit = $model->getState('limit');

		$fields = array();
		$ordering = array();

		if ($rows) {
			foreach ($rows as $row) {
				$field = EB::table('Field');
				$field->bind($row);

				$fields[] = $field;

				$ordering[] = $row->id;
			}
		}

		$pagination = $model->getPagination();

		$fieldGroupsModel = EB::model('FieldGroups');
		$groupFilters = [
			'' => 'COM_EASYBLOG_FIELDS_FILTER_BY_GROUP'
		];

		$items = $fieldGroupsModel->getItems();

		foreach ($items as $fieldGroup) {
			$groupFilters[$fieldGroup->id] = $fieldGroup->title;
		}

		// Save ordering
		$saveOrder = $order == 'ordering' && $orderDirection == 'asc';

		$this->set('currentFilter', $filter_groups);
		$this->set('groupFilters', $groupFilters);
		$this->set('limit', $limit);
		$this->set('search', $search);
		$this->set('pagination', $pagination);
		$this->set('fields', $fields);
		$this->set('showOrdering', $showOrdering);
		$this->set('saveOrder', $saveOrder);
		$this->set('ordering', $ordering);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('fields/default');
	}

	/**
	 * Displays available custom field groups
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function groups($tpl = null)
	{
		$this->checkAccess('easyblog.manage.fields');

		// Set page details
		$this->setHeading('COM_EASYBLOG_FIELDS_GROUPS_TITLE', '', 'fa-list-alt');

		JToolbarHelper::addNew('fields.addGroup');
		JToolbarHelper::divider();
		JToolbarHelper::publishList('fields.publishGroup');
		JToolbarHelper::unpublishList('fields.unpublishGroup');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_CONFIRM_DELETE_GROUPS'), 'fields.removeGroup');

		$search = $this->app->getUserStateFromRequest('com_easyblog.fields.search', 'search', '', 'string');
		$search = trim(EBString::strtolower($search));
		$filter_state = $this->app->getUserStateFromRequest( 'com_easyblog.fields.filter_state', 'filter_state', '*', 'string' );

		$browse = $this->input->get('browse', 0, 'int');
		$browsefunction = $this->input->get('browsefunction', 'insertGroup', 'string');

		// Get custom field groups
		$model 	= EB::model('FieldGroups');
		$result	= $model->getItems(true);
		$pagination = $model->getPagination();
		$limit = $model->getState('limit');

		// Bind the result with the table
		$groups = array();

		if ($result) {
			foreach ($result as $row) {

				$group 	= EB::table('FieldGroup');
				$group->bind($row);

				$groups[] = $group;
			}
		}

		$this->set('limit', $limit);
		$this->set('pagination', $pagination);
		$this->set('search', $search);
		$this->set('groups', $groups);
		$this->set('filterState', $filter_state);
		$this->set('browse', $browse);
		$this->set('browsefunction', $browsefunction);

		parent::display('fields/groups/default');
	}

	/**
	 * Displays the new custom field form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('easyblog.manage.fields');

		$id = $this->input->get('id', 0, 'int');

		// Get the field table
		$field = EB::table('Field');
		$field->load($id);

		$title = 'COM_EASYBLOG_FIELDS_CREATE_TITLE';

		if ($id) {
			$title 	= 'COM_EASYBLOG_FIELDS_EDIT_TITLE';
		}

		// Set page details
		$this->setHeading($title, '', 'fa-list-alt');

		JToolbarHelper::apply('fields.apply');
		JToolbarHelper::save('fields.save');
		JToolbarHelper::save2new('fields.savenew');
		JToolBarHelper::cancel('fields.cancel');

		// Get the fieldsgroup model
		$model = EB::model('Fields');
		$groups = $model->getGroups();

		if (!$groups) {
			$this->info->set('COM_EASYBLOG_PLEASE_CREATE_CUSTOM_FIELDS_GROUP_FIRST', 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=fields&layout=groups');
		}

		$fieldGroups = [
			'' => 'COM_EASYBLOG_FIELDS_SELECT_FIELD_GROUP'
		];

		foreach ($groups as $group) {
			$fieldGroups[$group->id] = $group->title;
		}

		// Get the custom fields library
		$fields = EB::fields()->getItems();
		$fieldTypes = [
			'' => 'COM_EASYBLOG_FIELDS_SELECT_FIELD_TYPE'
		];

		foreach ($fields as $type => $customField) {
			$fieldTypes[$customField->getElement()] = $customField->getTitle();
		}

		// Get the current active field form
		$form = '';

		if ($field->id) {
			$form = EB::fields()->get($field->type)->admin($field);
		}

		$this->set('fieldTypes', $fieldTypes);
		$this->set('fieldGroups', $fieldGroups);
		$this->set('fields', $fields);
		$this->set('form', $form);
		$this->set('field', $field);
		$this->set('groups', $groups);

		parent::display('fields/form/default');
	}

	/**
	 * Displays the field group form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function groupForm($tpl = null)
	{
		$this->checkAccess('easyblog.manage.fields');

		// Set page details
		$this->setHeading('COM_EASYBLOG_FIELDS_GROUPS_TITLE', '', 'fa-list-alt');

		JToolbarHelper::apply('fields.applyGroup');
		JToolbarHelper::save('fields.saveGroup');
		JToolbarHelper::save2new('fields.saveNewGroup');
		JToolBarHelper::cancel('fields.cancelGroup');

		// Get the group id
		$id = $this->input->get('id', 0, 'int');

		$group = EB::table('FieldGroup');
		$group->load($id);

		$this->set('group', $group);

		parent::display('fields/groups/form/default');
	}
}
