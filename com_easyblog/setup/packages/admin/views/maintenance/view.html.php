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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewMaintenance extends EasyBlogAdminView
{
	/**
	 * Displays the theme listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.maintenance');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		if ($this->input->get('success', 0, 'int')) {
			$this->info->set(JText::_('COM_EASYBLOG_MAINTENANCE_SUCCESSFULLY_EXECUTED_SCRIPT'), EASYBLOG_MSG_SUCCESS);
		}

		// Set heading text
		$this->setHeading('COM_EASYBLOG_MAINTENANCE_TITLE_SCRIPTS', '', 'fa-flask');

		// Set the buttons
		JToolbarHelper::custom('maintenance.form', 'refresh', '', JText::_('COM_EASYBLOG_MAINTENANCE_EXECUTE_SCRIPTS'));

		// filters
		$version = $this->app->getUserStateFromRequest('com_easyblog.maintenance.filter_version', 'filter_version', 'all', 'cmd');

		$order = $this->app->getUserStateFromRequest('com_easyblog.maintenance.filter_order', 'filter_order', 'version', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_easyblog.maintenance.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$versions = array();

		$model = EB::model('Maintenance');
		$model->setState('version', $version);
		$model->setState('ordering', $order);
		$model->setState('direction', $orderDirection);

		$scripts = $model->getItems();
		$pagination = $model->getPagination();

		$versions = $model->getVersions();
		$versions = array_combine($versions, array_values($versions));

		$limit = $model->getState('limit');

		$this->set('limit', $limit);
		$this->set('version', $version);
		$this->set('scripts', $scripts);
		$this->set('versions', $versions);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('pagination', $pagination);

		parent::display('maintenance/default');
	}

	public function form($tpl = null)
	{
		$cids = $this->input->get('cid', array(), 'var');

		$scripts = EB::model('Maintenance')->getItemByKeys($cids);

		$this->set('scripts', $scripts);

		parent::display('maintenance/form');
	}

	/**
	 * Displays the theme installer form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function database($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.maintenance');

		// Set heading text
		$this->setHeading('COM_EASYBLOG_MAINTENANCE_TITLE_DATABASE', '', 'fa-flask');

		parent::display('maintenance/database');
	}

	/**
	 * Update the ACL for EasyBlog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function updateACL()
	{
		$db = EB::db();

		// Intelligent fix to delete all records from the #__easyblog_acl_group when it contains ridiculous amount of entries
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_acl_group');
		$db->setQuery($query);

		$total = $db->loadResult();

		if ($total > 20000) {
			$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_acl_group');
			$db->setQuery($query);
			$db->Query();
		}

		// First, remove all records from the acl table.
		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_acl');
		$db->setQuery($query);
		$db->Query();

		// Get the list of acl
		$contents = file_get_contents(EBLOG_ADMIN_ROOT . '/defaults/acl.json');
		$acls = json_decode($contents);

		foreach ($acls as $acl) {

			$query = array();
			$query[] = 'INSERT INTO ' . $db->qn('#__easyblog_acl') . '(' . $db->qn('id') . ',' . $db->qn('action') . ',' . $db->qn('group') . ',' . $db->qn('description') . ',' . $db->qn('published') . ')';
			$query[] = 'VALUES(' . $db->Quote($acl->id) . ',' . $db->Quote($acl->action) . ',' . $db->Quote($acl->group) . ',' . $db->Quote($acl->desc) . ',' . $db->Quote($acl->published) . ')';
			$query = implode(' ', $query);

			$db->setQuery($query);
			$db->Query();
		}

		// Once the acl is initialized, we need to create default values for all the existing groups on the site.
		$this->assignACL();

		return true;
	}

	/**
	 * Assign acl rules to existing Joomla groups
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function assignACL()
	{
		// Get the db
		$db = EB::db();

		// Retrieve all user groups from the site
		$query = array();
		$query[] = 'SELECT a.' . $db->qn('id') . ', a.' . $db->qn('title') . ' AS ' . $db->qn('name') . ', COUNT(DISTINCT b.' . $db->qn('id') . ') AS ' . $db->qn('level');
		$query[] = ', GROUP_CONCAT(b.' . $db->qn('id') . ' SEPARATOR \',\') AS ' . $db->qn('parents');
		$query[] = 'FROM ' . $db->qn('#__usergroups') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__usergroups') . ' AS b';
		$query[] = 'ON a.' . $db->qn('lft') . ' > b.'  . $db->qn('lft');
		$query[] = 'AND a.' . $db->qn('rgt') . ' < b.' . $db->qn('rgt');
		$query[] = 'GROUP BY a.' . $db->qn('id');
		$query[] = 'ORDER BY a.' . $db->qn('lft') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Default values
		$groups = array();
		$result = $db->loadColumn();

		// Get a list of default acls
		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__easyblog_acl');
		$query[] = 'ORDER BY ' . $db->qn('id') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Get those acls
		$installedAcls = $db->loadColumn();

		// Default admin groups
		$adminGroups = array(7, 8);

		if (!empty($result)) {

			foreach ($result as $id) {

				$id = (int) $id;

				// Every other group except admins and super admins should only have restricted access
				if (in_array($id, $adminGroups)) {

					// exclude some of the acl in admins groups.
					$excludeAcls = array(26);
					$adminAcls = array_diff($installedAcls, $excludeAcls);
					$groups[$id] = $adminAcls;

				} else {

					$allowedAcl = array();

					// Default guest / public group
					if ($id == 1 || $id == 9) {
						$allowedAcl = array(18, 19, 37, 39);
					} else {
						// other groups
						$allowedAcl = array(1, 3, 4, 6, 8, 10, 11, 12, 13, 14, 15, 16 ,17, 18, 19, 21, 23, 24, 25, 27, 28, 30, 33, 34, 35, 36 , 37, 39, 40, 41, 42, 46, 48, 49);
					}

					$groups[$id] = $allowedAcl;
				}
			}
		}


		// Insert default filter for all groups.
		$tagFilter = 'script,applet,iframe';
		$attrFilter = 'onclick,onblur,onchange,onfocus,onreset,onselect,onsubmit,onabort,onkeydown,onkeypress,onkeyup,onmouseover,onmouseout,ondblclick,onmousemove,onmousedown,onmouseup,onerror,onload,onunload';

		// Go through each groups now
		foreach ($groups as $groupId => $acls) {

			$query = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_acl_filters');
			$query[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
			$query = implode(' ', $query);

			$db->setQuery($query);
			$filterExists = $db->loadResult() > 0 ? true : false;

			// If the filters doesn't exist, insert them
			if (!$filterExists) {

				$filter = EB::table('ACLFilter');
				$filter->content_id = $groupId;
				$filter->disallow_tags = in_array($groupId, $adminGroups) ? '' : $tagFilter;
				$filter->disallow_attributes = in_array($groupId, $adminGroups) ? '' : $attrFilter;

				$filter->store();
			}

			// Now we need to insert the acl rules
			$query = array();
			$insertQuery = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_acl_group');
			$query[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
			$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('group');

			$query = implode(' ', $query);

			$db->setQuery($query);
			$exists = $db->loadResult() > 0 ? true : false;

			// Reinitialize the query again.
			$query = 'INSERT INTO ' . $db->qn('#__easyblog_acl_group') . ' (' . $db->qn('content_id') . ',' . $db->qn('acl_id') . ',' . $db->qn('status') . ',' . $db->qn('type') . ') VALUES';

			if (!$exists) {

				foreach ($acls as $acl) {
					$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($acl) . ',' . $db->Quote('1') . ',' . $db->Quote('group') . ')';
				}

				//now we need to get the unassigend acl and set it to '0';
				$disabledACLs = array_diff($installedAcls, $acls);

				if ($disabledACLs) {
					foreach ($disabledACLs as $disabledAcl) {
						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($disabledAcl) . ',' . $db->Quote('0') . ',' . $db->Quote('group') . ')';
					}
				}

			} else {

				// Get a list of acl that is already associated with the group
				$sub = array();
				$sub[] = 'SELECT ' . $db->qn('acl_id') . ' FROM ' . $db->qn('#__easyblog_acl_group');
				$sub[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
				$sub[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('group');

				$sub = implode(' ', $sub);
				$db->setQuery($sub);

				$existingGroupAcl = $db->loadColumn();

				// Perform a diff to see which acl rules are missing
				$diff = array_diff($installedAcls, $existingGroupAcl);

				// If there's a difference,
				if ($diff) {
					foreach ($diff as $aclId) {

						$value = 0;

						if (in_array($aclId, $acls)) {
							$value = 1;
						}

						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($aclId) . ',' . $db->Quote($value) . ',' . $db->Quote('group') . ')';
					}
				}
			}

			// Only run this when there is something to insert
			if ($insertQuery) {
				$insertQuery = implode(',', $insertQuery);
				$query .= $insertQuery;

				$db->setQuery($query);
				$db->Query();
			}
		}

		return true;
	}
}
