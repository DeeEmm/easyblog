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

class EasyBlogControllerComment extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		// Save
		$this->registerTask('apply', 'save');

		// Publish / Unpublish
		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Allows caller to delete comments from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('comment');

		// Get the list of comments to be deleted
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_COMMENTS_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=comments');
		}

		foreach ($ids as $id) {

			// Load the comment object
			$comment = EB::table('Comment');
			$comment->load((int) $id);

			// Try to delete the comment
			$comment->delete();
		}

		$this->info->set('COM_EASYBLOG_COMMENTS_COMMENT_REMOVED', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=comments');
	}

	/**
	 * Saves a comment item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('comment');

		// Get the comment id
		$id = $this->input->get('id', 0, 'int');

		// Get the comment object
		$comment = EB::table('Comment');
		$comment->load($id);

		// Get the posted data
		$post = $this->input->getArray('post');

		// Bind the post data
		$comment->bind($post);

		// Try to save the comment
		$state = $comment->store();

		if (!$state) {
			$this->info->set($comment->getError(), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=comments&layout=form&id=' . $comment->id);
		}

		// If the comment is published and no notifications sent yet, we need to send it out
		if ($comment->published && !$comment->sent) {

			$comment->comment = EB::comment()->parseBBCode($comment->comment);
			$comment->comment = nl2br($comment->comment);

			// Get the blog object associated with the comment
			$post = $comment->getBlog();

			// Process the emails now
			$comment->processEmails(false, $post);

			// Update the sent flag to sent, so we will never notify more than once
			$comment->updateSent();
		}

		$this->info->set('COM_EASYBLOG_COMMENTS_SAVED', 'success');

		$task = $this->getTask();
		$redirect = 'index.php?option=com_easyblog&view=comments';

		if ($task == 'apply') {
			$redirect .= '&layout=form&id=' . $comment->id;
		}

		return $this->app->redirect($redirect);
	}

	/**
	 * Toggles publishing of a comment item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('comment');

		// Get the id's
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_COMMENTS_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=comments');
		}

		// Get the comments model
		$model = EB::model('Comments');

		// Get the current task
		$task = $this->getTask();

		foreach ($ids as $id) {

			$comment = EB::table('Comment');
			$comment->load((int) $id);

			// Publish the comment
			$comment->$task();
		}

		$message = 'COM_EASYBLOG_COMMENTS_COMMENT_PUBLISHED';

		if ($task == 'unpublish') {
			$message = 'COM_EASYBLOG_COMMENTS_COMMENT_UNPUBLISHED';
		}

		$this->info->set($message, 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=comments');
	}
}
