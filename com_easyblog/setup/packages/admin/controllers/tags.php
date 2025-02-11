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

class EasyBlogControllerTags extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		// Saving
		$this->registerTask('savenew', 'save');
		$this->registerTask('saveclose', 'save');
		$this->registerTask('apply', 'save');

		// Toggle default
		$this->registerTask('setDefault', 'toggleDefault');
		$this->registerTask('removeDefault', 'toggleDefault');

		// Register the publish and unpublish actions
		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Saves a tag
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('tag');

		// Get the posted data
		$post = $this->input->getArray('post');

		// If this tag is being edited, we should load it first
		$id = $this->input->get('id', 0, 'int');
		$tag = EB::table('Tag');
		$tag->load($id);

		// Bind the posted data
		$tag->bind($post);

		// Remove any trailing spaces on the tags
		$tag->title = EBString::trim($tag->title);
		$tag->alias = EBString::trim($tag->alias);

		// Sanitize the tag title
		$tag->title = str_ireplace(',', '', $tag->title);

		// Throw error if tag is not valid
		if (!$tag->title) {
			$this->info->set('COM_EASYBLOG_TAGS_TAG_ERROR_INVALID_TITLE', 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=tags&layout=form');
		}

		// Set the tag author to the current user
		$tag->created_by = $this->my->id;

		// Save the tag
		$state = $tag->store();

		if (!$state) {
			$this->info->set($tag->getError(), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=tags&layout=form');
		}

		$message = JText::_('COM_EASYBLOG_TAGS_TAG_SAVED');

		// Get the current task we are executing
		$task = $this->getTask();

		$this->info->set($message, 'success');

		if ($task == 'savenew') {
			return $this->app->redirect('index.php?option=com_easyblog&view=tags&layout=form');
		}

		if ($task == 'saveclose') {
			return $this->app->redirect('index.php?option=com_easyblog&view=tags');
		}

		return $this->app->redirect('index.php?option=com_easyblog&view=tags&layout=form&id=' . $tag->id);
	}

	/**
	 * Deletes a tag from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('tag');

		// Get the list of tags to be deleted
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_TAGS_INVALID_ID_PROVIDED'), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=tags');
		}

		// Do whatever you need to do here
		foreach ($ids as $id) {

			$tag = EB::table('Tag');
			$tag->load((int) $id);

			// Delete the tag
			$tag->delete();
		}

		$this->info->set('COM_EASYBLOG_TAGS_TAG_REMOVED', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=tags');
	}

	/**
	 * Toggles publishing
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('tag');

		// Get the current task, whether to publish or unpublish
		$task = $this->getTask();

		// Get the list of ids
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_TAGS_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=tags');
		}

		// Try to perform the task
		$method = $task . 'Items';
		$model = EB::model('Tags');
		$model->$method($ids);

		$message = 'COM_EASYBLOG_TAGS_TAG_PUBLISHED';

		if ($task == 'unpublish') {
			$message = 'COM_EASYBLOG_TAGS_TAG_UNPUBLISHED';
		}

		$this->info->set($message, 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=tags');
	}

	/**
	 * Toggles a default state
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function toggleDefault()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('tag');

		// Get the list of ids
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_TAGS_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=tags');
		}

		$method = $this->getTask();

		$model = EB::model('Tags');

		$model->$method($ids);

		if ($method == 'setDefault') {
			$this->info->set('COM_EASYBLOG_TAGS_TAG_SET_DEFAULT_SUCCESS', 'success');
		} else {
			$this->info->set('COM_EASYBLOG_TAGS_TAG_UNSET_DEFAULT_SUCCESS', 'success');
		}

		return $this->app->redirect('index.php?option=com_easyblog&view=tags');
	}
}
