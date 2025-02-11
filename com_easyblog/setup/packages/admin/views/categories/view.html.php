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

class EasyBlogViewCategories extends EasyBlogAdminView
{
	/**
	 * Displays the category listings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.category');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Set the heading
		$this->setHeading('COM_EASYBLOG_TITLE_CATEGORIES');

		JToolbarHelper::addNew('category.create');
		JToolbarHelper::publishList('category.publish');
		JToolbarHelper::unpublishList('category.unpublish');
		JToolbarHelper::custom('category.copy', 'copy', '', JText::_('COM_EASYBLOG_COPY'));
		JToolbarHelper::deleteList('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', 'category.remove');

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_state', 'filter_state', '*', 'word');

		$search = $this->app->getUserStateFromRequest('com_easyblog.categories.search', 'search', '', 'string');
		$search = trim(EBString::strtolower($search));

		$order = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_order', 'filter_order', 'lft', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.categories.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		// Should we display only published categories
		$publishedOnly = $this->input->get('p', 0, 'int');

		$category = EB::table('Category');

		// Get data from the model
		$ordering = array();
		$model = EB::model('Category');

		// Get the list of categories
		$result = $model->getData();
		$categories = array();
		$limit = $model->getState('limit');

		if ($result) {
			foreach ($result as $row) {

				$category = EB::table('Category');
				$category->bind($row);

				$category->depth = $row->depth;
				$category->count = $category->getCount();
				$category->child_count = $model->getChildCount($row->id);

				$ordering[$row->parent_id][] = $category->id;

				$categories[] = $category;
			}
		}

		// Get the pagination
		$pagination = $model->getPagination();

		// Retrieve items from query
		$browse = $this->input->get('browse', 0, 'int');
		$browsefunction = $this->input->get('browsefunction', 'insertCategory', 'cmd');

		// Save ordering
		$saveOrder = $order == 'lft' && $orderDirection == 'asc';

		$this->set('limit', $limit);
		$this->set('browse', $browse);
		$this->set('browsefunction', $browsefunction);
		$this->set('categories', $categories);
		$this->set('pagination', $pagination);
		$this->set('filterState', $filter_state);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('saveOrder', $saveOrder);
		$this->set('ordering', $ordering);
		$this->set('orderDirection', $orderDirection);

		parent::display('categories/default');
	}

	/**
	 * Renders the category form at the back end
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function form()
	{
		// Get the category id.
		$id = $this->input->get('id', 0, 'int');

		$category = EB::table('Category');
		$category->load($id);

		// Set the page heading
		$this->setHeading('COM_EASYBLOG_TITLE_CREATE_CATEGORY', '');

		if ($category->id) {
			$this->setHeading('COM_EASYBLOG_TITLE_EDIT_CATEGORY', '');
		}

		JToolBarHelper::apply('category.apply');
		JToolbarHelper::save('category.save');
		JToolbarHelper::save2new('category.savenew');
		JToolBarHelper::cancel('category.cancel');

		// If this is a new category, initialize the default property for the category
		if (!$category->created) {

			$date = EB::date();

			$category->created = $date->toSql();
			$category->published = true;
			$category->autopost = true;
		}

		// Get assigned acl
		$groups = $category->getGroupAssignedACL();
		$usertags = $category->getUserAssignedACL();

		// Get the current site template. The result always an array
		$templates = $this->getCurrentTemplate();

		$templateDisplay = $templates[0];
		if (count($templates) > 1) {
			$templateDisplay = '[' . implode('|', $templates) . ']';
		}

		// Get a list of custom template folders
		$themes = $this->getCustomThemes($templates);
		$categoryThemes = [];

		if ($themes) {
			$categoryThemes[''] = 'COM_EASYBLOG_CATEGORIES_EDIT_SELECT_CUSTOM_TEMPLATE_DEFAULT';

			foreach ($themes as $theme) {
				$categoryThemes[$theme] = ucfirst($theme);
			}
		}

		// Get a list of custom field groups available
		$fieldModel	= EB::model('Fields');
		$fieldGroups = $fieldModel->getGroups();
		$customFieldGroups = [
			'' => 'COM_EASYBLOG_CATEGORIES_NO_CUSTOM_FIELDS'
		];

		if ($fieldGroups) {
			foreach ($fieldGroups as $fieldGroup) {
				$customFieldGroups[$fieldGroup->id] = $fieldGroup->title;
			}
		}

		// Get a list of parents
		$parentList = EB::populateCategories('', '', 'select', 'parent_id', $category->parent_id , false , false , false , array($category->id) );

		// Get editor
		$editor = EBFactory::getEditor();

		// Get the category params
		$params = $category->getParams();
		$inheritedParams = true;

		// @legacy fixes
		// This option determines if the stored values are being inherited from global settings
		$paramsArray = $params->toArray();

		if ($paramsArray) {
			// New way of testing if parameters are being inherited
			if (isset($paramsArray['inherited'])) {
				$inheritedParams = $paramsArray['inherited'];
			} else {

				// Legacy way of testing if post parameters are being inherited
				// Prior to 5.1, we do not have an explicity settings to inherit or not.
				// To test if the params are being inherited, we need to test each value out
				foreach ($paramsArray as $key => $value) {
					if ($value == '') {
						continue;
					}

					if ($value == '-1') {
						continue;
					}

					$inheritedParams = false;

					// Break out the loop since there is at least 1 settings that was modified
					break;
				}
			}
		}

		// If the params are configured to be inherited, we should map the original value from the view's entry layout settings
		if ($inheritedParams) {

			$menuModel = EB::model('Menu');
			$defaultParams = $menuModel->getDefaultEntryXMLParams();
			$defaultParamsArray = $defaultParams->toArray();

			foreach ($defaultParamsArray as $key => $value) {
				$params->set($key, $this->config->get('layout_' . $key));
			}
		}

		// Set default value for new category param->set('category_acl_type');
		if ($params->get('category_acl_type') === null) {
			$params->set('category_acl_type', CATEGORY_ACL_ACTION_SELECT);
		}

		// Determines if facebook has already been associated
		$associated = EB::oauth()->associated('facebook');

		// Load the oauth table
		$oauth = EB::table('Oauth');
		$oauth->load(array('type' => 'facebook', 'system' => true));

		// Get a list of pages
		$fbPages = array();
		$fbGroups = array();

		if ($associated && $oauth->access_token) {
			$client = EB::oauth()->getClient('Facebook');
			$client->setAccess($oauth->access_token);

			// Get pages that are available
			try {
				$fbPages = $client->getPages();
				$fbGroups = $client->getGroups();
			} catch(Exception $e) {
			}
		}

		// Get a list of stored pages
		$storedFbPages = $params->get('facebook_page_id');
		$storedFbPages = $storedFbPages ? explode(',', $storedFbPages) : array();

		// Get a list of stored groups
		$storedFbGroups = $params->get('facebook_group_id');
		$storedFbGroups = $storedFbGroups ? explode(',', $storedFbGroups) : array();

		// Get the param forms from the view manifest file
		$file = JPATH_ROOT . '/components/com_easyblog/views/entry/tmpl/default.xml';
		$manifest = EB::form()->getManifest($file);

		// Get active tab
		$active = $this->input->get('active', 'general', 'word');

		$this->set('active', $active);
		$this->set('inheritedParams', $inheritedParams);
		$this->set('parentList', $parentList);
		$this->set('params', $params);
		$this->set('manifest', $manifest);
		$this->set('customFieldGroups', $customFieldGroups);
		$this->set('templates', $templates);
		$this->set('templateDisplay', $templateDisplay);
		$this->set('categoryThemes', $categoryThemes);
		$this->set('editor', $editor);
		$this->set('groups', $groups);
		$this->set('usertags', $usertags);
		$this->set('category', $category);
		$this->set('storedFbGroups', $storedFbGroups);
		$this->set('fbGroups', $fbGroups);
		$this->set('storedFbPages', $storedFbPages);
		$this->set('fbPages', $fbPages);
		$this->set('associated', $associated);

		parent::display('categories/form/default');
	}

	/**
	 * Retrieves the current Joomla template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCurrentTemplate()
	{
		$db = EB::db();

		$query = 'SELECT distinct ' . $db->nameQuote('template') . ' FROM ' . $db->nameQuote('#__template_styles');
		// in multilingual setup, the home can be a langauge tag instead of 1. #402
		$query .= ' WHERE ' . $db->nameQuote('home') . '!=' . $db->Quote(0);
		$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$templates = $db->loadColumn();
		return $templates;
	}

	/**
	 * Retrieves a list of custom themes on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCustomThemes($templates)
	{
		$folders = array();

		if ($templates && is_array($templates)) {
			foreach ($templates as $template) {
				$path = JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/themes';

				if (!JFolder::exists($path)) {
					// continue to next template
					continue;
				}

				// $folders[$template] = JFolder::folders($path);

				$overrideFolders = JFolder::folders($path);

				$folders = array_merge($folders, $overrideFolders);
			}
		}

		return $folders;
	}
}
