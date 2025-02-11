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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerCategory extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		// Check for acl rules.
		$this->checkAccess('category');

		$this->registerTask('apply', 'save');
		$this->registerTask('save', 'save');
		$this->registerTask('savenew', 'save');
		$this->registerTask('publish', 'publish');

		// In Joomla 3.0, it seems like we need to explicitly set unpublish
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('orderup', 'orderup');
		$this->registerTask('orderdown', 'orderdown');
	}

	public function orderdown()
	{
		// Check for request forgeries
		FH::checkToken();

		$this->orderCategory(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		FH::checkToken();

		$this->orderCategory(-1);
	}

	public function orderCategory($direction)
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('category');

		// Initialize variables
		$db	= EB::db();
		$cid = $this->input->get('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row = EB::table('Category');
			$row->load((int) $cid[0]);

			$row->move($direction);

			//now we need to update the ordering.
			$row->updateOrdering();
		}

		$this->app->redirect('index.php?option=com_easyblog&view=categories');
	}

	public function saveOrder()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('category');

		$row = EB::table('Category');
		$row->rebuildOrdering();

		//now we need to update the ordering.
		$row->updateOrdering();

		$message = JText::_('COM_EASYBLOG_CATEGORIES_ORDERING_SAVED');
		EB::info()->set($message, 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=categories');
		exit;
	}

	/**
	 * Allows caller to duplicate categories
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function copy()
	{
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('category');

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			die('');
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$category = EB::table('Category');
			$category->load($id);

			$duplicate = $category->duplicate();
		}

		$this->info->set('COM_EASYBLOG_SELECTED_CATEGORY_DUPLICATED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=categories');
	}

	/**
	 * Saves a category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get posted data
		$post = $this->input->getArray('post');
		$task = $this->getTask();

		$oriParentId = $this->input->get('oriParentId', 0);

		//unset oriParentId since we no longer needed
		unset($post['oriParentId']);

		// Get the category id
		$id = $this->input->get('id', '', 'int');
		$category = EB::table('Category');
		$category->load($id);

		// Construct the redirection url
		$url = 'index.php?option=com_easyblog&view=categories&layout=form';

		if ($category->id) {
			$url .= '&id=' . $id;
		}

		if (!isset($post['title']) || !$post['title']) {
			EB::info()->set(JText::_('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY'), 'error');
			return $this->app->redirect($url);
		}

		// Determines if this is a new category
		$isNew = $category->id ? false : true;

		// Bind the posted data
		$category->bind($post);

		if (!$category->isNotAssigned() && $category->isDefault()) {
			EB::info()->set(JText::_('COM_EASYBLOG_CATEGORIES_SAVE_NOT_PUBLIC'), 'error');
			return $this->app->redirect($url);
		}

		if (!$category->created_by && empty($category->created_by)) {
			$category->created_by = $this->my->id;
		}

		// Get the description for the category
		$category->description = $this->input->get('description', '', 'raw');

		// Process the params
		$raw = $this->input->get('params', '', 'array');

		if (isset($raw['facebook_page_id'])) {

			$pages = $raw['facebook_page_id'];

			// We need to merge them to be comma separated values
			$raw['facebook_page_id'] = implode(',', $pages);
		}

		if (isset($raw['facebook_group_id'])) {

			$groups = $raw['facebook_group_id'];

			// We need to merge them to be comma separated values
			$raw['facebook_group_id'] = implode(',', $groups);
		}

		// Determines if the post params are being inherited
		if (!isset($raw['inherited'])) {
			$raw['inherited'] = false;
		}

		// Process category's default post cover
		$fileCatPostCover = $this->input->files->get('post_cover', '');

		// Process this if the post cover field doesn't have upload any file
		// Need to pass back the post cover value
		if (isset($fileCatPostCover['tmp_name']) && empty($fileCatPostCover['name'])) {
			$catParamsObj = EB::makeArray($category->params);

			if (isset($catParamsObj['post_cover']) && $catParamsObj['post_cover']) {
				$raw['post_cover'] = $catParamsObj['post_cover'];
			}
		}

		$category->params = json_encode($raw);

		// Try to save the category now
		$state = $category->store();

		if (!$state) {
			EB::info()->set($category->getError(), 'error');

			return $this->app->redirect($url);
		}

		// we need to check if the parent_id has changed or not. if yes,
		// we need to re-calcuate the lft and rgt boundary #128
		if ($oriParentId != $category->parent_id) {
			$category->moveLftToLastOf($category->parent_id);
		}

		// Bind the category with the custom fields
		$fieldGroup = $this->input->get('field_group', '', 'int');

		if ($fieldGroup) {
			$category->bindCustomFieldGroup($fieldGroup);
		} else {
			$category->removeFieldGroup();
		}

		// Category ACL will not be applied on default category.
		if (!$category->default) {
			// Once the category is saved, delete existing acls
			$category->deleteACL();

			if ($category->private == CATEGORY_PRIVACY_ACL) {
				$category->saveACL($post);
			}
		}

		// Set the meta for the category
		$category->addMeta();

		// Process category avatars
		$file = $this->input->files->get('Filedata', '');

		if (isset($file['tmp_name']) && !empty($file['name'])) {
			$state = $category->uploadAvatar();

			if ($state === false) {
				return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=category', false), $category->getError(), 'error');
			}
		}

		// Process category's default post cover
		if (isset($fileCatPostCover['tmp_name']) && !empty($fileCatPostCover['name'])) {
			$category->saveDefaultPostCover($fileCatPostCover);
		}

		$category->afterSave();

		$this->info->set('COM_EASYBLOG_CATEGORIES_SAVED_SUCCESS', 'success');

		$redirect = 'index.php?option=com_easyblog&view=categories';

		$actionString = $isNew ? 'COM_EB_ACTIONLOGS_CATEGORY_CREATE' : 'COM_EB_ACTIONLOGS_CATEGORY_UPDATE';
		$categoryLink = $redirect . '&layout=form&id=' . $category->id;

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log($actionString, 'category', array(
			'link' => $categoryLink,
			'categoryTitle' => JText::_($category->title)
		));


		if ($task == 'savenew') {
			return $this->app->redirect($redirect . '&layout=form');
		}

		if ($task == 'apply') {
			$active = $this->input->get('active', '', 'word');

			if ($active) {
				$redirect .= '&active=' . $active;
			}

			return $this->app->redirect($redirect . '&layout=form&id=' . $category->id);
		}


		return $this->app->redirect($redirect);
	}

	/**
	 * Removes a category from the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('category');

		// Get the list of id's
		$ids = $this->input->get('cid', array(), 'array');

		$return = 'index.php?option=com_easyblog&view=categories';

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY', 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$category = EB::table('Category');
			$category->load($id);

			// Try to delete the category now.
			$state = $category->delete();

			if (!$state) {
				$this->info->set($category->getError(), 'error');
				return $this->app->redirect($return);
			}
		}

		$this->info->set('COM_EASYBLOG_CATEGORIES_DELETE_SUCCESS', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Publishes a category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function publish()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('category');

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$model = EB::model('Categories');
		$state = $model->publish($ids, 1);

		$this->info->set('COM_EASYBLOG_CATEGORIES_PUBLISHED_SUCCESS', 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=categories');
	}

	/**
	 * Unpublish category from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unpublish()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('category');

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY', 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$model = EB::model('categories');

		foreach ($ids as $id) {
			// Ensure that none of its child is currently set to default
			$category = EB::table('Category');
			$category->load((int) $id);

			// check if any of the category is default category or not. if yes, return error.
			if ($category->isDefault()) {
				$this->info->set('COM_EB_CATEGORY_UNPUBLISH_DEFAULT_CATEGORY_NOTICE', 'error');
				return $this->app->redirect('index.php?option=com_easyblog&view=categories');
			}

			$childs = $model->getChildCategories($id);

			// Ensure that all of the child does not have any default
			if ($childs) {
				foreach ($childs as $child) {
					$childCategory = EB::table('Category');
					$childCategory->load($child->id);

					if ($childCategory->default) {
						$this->info->set('COM_EB_CATEGORY_UNPUBLISH_CATEGORY_WITH_DEFAULT_CHILD_NOTICE', 'error');
						return $this->app->redirect('index.php?option=com_easyblog&view=categories');
					}
				}
			}
		}

		$model = EB::model('Categories');
		$state = $model->publish($ids, 0);

		$this->info->set('COM_EASYBLOG_CATEGORIES_UNPUBLISHED_SUCCESS', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=categories');
	}

	/**
	 * Toggles a category as the default category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function makeDefault()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the category id
		$id = $this->input->get('cid', array(), 'array()');

		if (!$id) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_INVALID_CATEGORY', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		// Check for acl rules.
		$this->checkAccess('category');

		// Since the id is an array, we only want the first item
		$id = (int) $id[0];

		// Set the current category as default
		$category = EB::table('Category');
		$category->load($id);

		// If the category is not public, don't set it as default
		if (!$category->isNotAssigned()) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_NOT_PUBLIC','error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		// Do not allow user to set default category if their parent is disabled
		$parent = $category->getParent();

		if ($parent && !$parent->published) {
			$this->info->set('COM_EB_CATEGORY_PARENT_PUBLISH_BEFORE_CHILD_DEFAULT_NOTICE','error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		// If the category is not published, don't set it as default.
		if (!$category->published) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_NOT_PUBLISHED','error');

			return $this->app->redirect('index.php?option=com_easyblog&view=categories');
		}

		$category->setDefault();

		$this->info->set('COM_EASYBLOG_CATEGORIES_MARKED_AS_DEFAULT', 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=categories');
	}

	/**
	 * Remove category avatar
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeAvatar()
	{
		$id = $this->input->get('id', 0, 'int');

		// Get the category
		$category = EB::table('Category');
		$category->load($id);

		// Remove avatar
		$category->removeAvatar(true);

		return $this->ajax->resolve();
	}

	/**
	 * Remove category default post cover
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function removePostCover()
	{
		$id = $this->input->get('id', 0, 'int');

		$category = EB::table('Category');
		$category->load($id);

		$category->removePostCover();

		return $this->ajax->resolve();
	}
}
