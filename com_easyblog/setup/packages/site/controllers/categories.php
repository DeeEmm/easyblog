<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerCategories extends EasyBlogController
{
	/**
	 * Saves a category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Default return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories', false);

		// Ensure that the user has access to create category
		if (!$this->acl->get('create_category') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_CATEGORY', 'danger');
			return $this->app->redirect($return);
		}

		// Possibility is that this category is being edited.
		$id = $this->input->get('id', 0, 'int');

		// Get the title of the category
		$title = $this->input->get('title', '', 'default');

		if (!$title) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY_CATEGORY_TITLE_ERROR', 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categoryForm&id=' . $id, false));
		}

		$category = EB::table('Category');
		$category->load($id);

		// Default success message
		$message = 'COM_EASYBLOG_DASHBOARD_CATEGORIES_ADDED_SUCCESSFULLY';

		if ($category->id && $id) {
			$message = 'COM_EASYBLOG_DASHBOARD_CATEGORY_UPDATED_SUCCESSFULLY';
		}

		// Check whether the same category already exists on the site.
		$model = EB::model('Category');
		$exists = $model->isExist($title, $category->id);

		if ($exists) {
			$this->info->set(JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_ALREADY_EXISTS_ERROR', $title), 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categoryForm&id=' . $id, false));
		}

		$post = $this->input->getArray('post');
		$post['title'] = $title;


		// if this is a edit category, we do not update the author
		if (! $category->id) {
			$post['created_by'] = $this->my->id;
		}

		$post['parent_id'] = $this->input->get('parent_id', 0, 'int');
		$post['private'] = $this->input->get('private', 0, 'int');
		$post['description'] = $this->input->get('description', '', 'raw');

		$category->bind($post);

		if ($category->private == '2' && $category->default) {
			$this->info->set('COM_EB_DASHBOARD_CATEGORIES_UNABLE_ASSIGN_PERMISSION_DEFAULT_CATEGORY', 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categoryForm&id=' . $id, false));
		}

		// Set the category as published by default.
		$category->published = true;

		// Assign default ACL for the category
		if (!$category->id) {

			// Category ACL type.
			$postParams = $this->input->get('params', '', 'default');
			$params = [
				'category_acl_type' => FH::normalize($postParams, 'category_acl_type', 2),
				'inherited' => true
			];

			$category->generateDefaultParams($params);
		}

		// Save the cat 1st so that the id get updated
		$state = $category->store();

		if (!$state) {
			$this->info->set($category->getError(), 'danger');
			return $this->app->redirect($return);
		}

		// Category ACL will not be applied on default category.
		if (!$category->default) {
			// Delete all acl related to this category
			$category->deleteACL();

			if ($category->private == CATEGORY_PRIVACY_ACL) {
				$category->saveACL($post);
			}
		}

		// Set a category avatar if required
		$file = $this->input->files->get('Filedata', '', 'array');

		if (isset($file['name']) && !empty($file['name'])) {
			$state = $category->uploadAvatar();

			if ($state === false) {
				EB::info()->set($category->getError(), 'error');

				return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories', false));
			}
		}

		$category->afterSave();

		$this->info->set(JText::sprintf($message, $category->getTitle()), 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Deletes category from the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		// Default redirection url
		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories', false);

		// Get the ids
		$ids = $this->input->get('ids', array(), 'array');

		if (empty($ids)) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_ID_IS_EMPTY_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		foreach ($ids as $id) {

			// Load up the category
			$category = EB::table('Category');
			$category->load($id);

			// Ensure that this category is delete-able
			if (!$category->canDelete()) {
				$this->info->set($category->getError(), 'danger');
				return $this->app->redirect($redirect);
			}

			// Try to delete the category now
			$state = $category->delete();

			if (!$state) {
				$this->info->set($category->getError(), 'danger');
				return $this->app->redirect($redirect);
			}
		}

		$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_DELETED_SUCCESSFULLY', 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Publish the categories
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function publish()
	{
		FH::checkToken();
		EB::requireLogin();

		// Check if the user is really allowed to perform these actions
		if (!$this->acl->get('create_category') && !FH::isSiteAdmin()) {
			die();
		}

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories', false);

		$ids = $this->input->get('ids', [], 'array');

		if (empty($ids)) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_ID_IS_EMPTY_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		$model = EB::model('Categories');

		// Publish the categories now
		$state = $model->publish($ids, 1);

		$this->info->set('COM_EASYBLOG_CATEGORIES_PUBLISHED_SUCCESS', 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Unpublish the categories
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function unpublish()
	{
		FH::checkToken();
		EB::requireLogin();

		// Check if the user is really allowed to perform these actions
		if (!$this->acl->get('create_category') && !FH::isSiteAdmin()) {
			die();
		}

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories', false);

		$ids = $this->input->get('ids', [], 'array');

		if (empty($ids)) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_ID_IS_EMPTY_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		foreach ($ids as $id) {
			$category = EB::table('Category');
			$category->load($id);

			// Tell the user that it is not possible to unpublish default category
			if ($category->isDefault()) {
				$this->info->set(JText::sprintf('COM_EASYBLOG_DASHBOARD_UNPUBLISHED_ERROR_DEFAULT_CATEGORY', $category->title), 'error');
				return $this->app->redirect($redirect);
			}
		}

		$model = EB::model('Categories');

		// Unpublish the categories
		$state = $model->publish($ids, 0);

		$this->info->set('COM_EASYBLOG_CATEGORIES_UNPUBLISHED_SUCCESS', 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Set category as default
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function setDefault()
	{
		FH::checkToken();
		EB::requireLogin();

		$redirect = 'index.php?option=com_easyblog&view=dashboard&layout=categories';

		// Only site admin can make the category as default
		if (!FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'danger');
			return $this->app->redirect($redirect);
		}

		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_CATEGORIES_ID_IS_EMPTY_ERROR', 'danger');
			return $this->app->redirect($redirect);
		}

		// Load the category
		$category = EB::table('Category');
		$category->load($id);

		// If the category is not public, don't set it as default
		if (!$category->isNotAssigned()) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_MARK_DEFAULT_NOT_PUBLIC', 'danger');
			return $this->app->redirect($redirect);
		}

		// If the category is not published, don't set it as default.
		if (!$category->published) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_MARK_DEFAULT_NOT_PUBLISHED', 'danger');
			return $this->app->redirect($redirect);
		}

		// Set category as default now
		$category->setDefault();

		$this->info->set('COM_EASYBLOG_CATEGORIES_MARKED_AS_DEFAULT', 'success');
		return $this->app->redirect($redirect);
	}
}
