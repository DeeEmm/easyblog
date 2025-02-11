<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerComments extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');

		// Register tasks for moderation
		$this->registerTask('approve', 'moderate');
		$this->registerTask('reject', 'moderate');
	}

	/**
	 * Allows visitor to approve a comment
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function moderate()
	{
		// Get the hash key
		$key = $this->input->get('key', '', 'default');

		// Default redirection url
		$redirect = EBR::_('index.php?option=com_easyblog', false);

		if (!$key) {
			$this->info->set('COM_EB_COMMENT_MODERATED', 'error');
			return $this->app->redirect($redirect);
		}

		// Get the hashkey from the site
		$hashkey = EB::table('Hashkeys');
		$state = $hashkey->loadByKey($key);

		// If the key doesn't exist, it may no longer be a valid request
		if (!$state || !$hashkey->id) {
			$this->info->set('COM_EB_COMMENT_MODERATED', 'error');
			return $this->app->redirect($redirect);
		}

		// Load the comment now
		$comment = EB::table('Comment');
		$comment->load($hashkey->uid);

		// Get the task to perform
		$task = $this->getTask();

		// Load up the post library
		$post = EB::post($comment->post_id);

		if ($task == 'approve') {
			$comment->published = EBLOG_COMMENT_PUBLISHED;

			// Save the comment now
			$comment->store(true);

			// Process the mails for comments now
			$comment->processEmails(false, $post, $task);

			// Update the sent flag for the comment
			$comment->updateSent();
		}

		if ($task == 'reject') {
			$comment->published = EBLOG_COMMENT_UNPUBLISHED;

			// Process the mails for comments now
			$comment->processEmails(false, $post, $task);

			$comment->store(true);
		}

		// Delete the unused hashkey now.
		$hashkey->delete();

		$message = 'COM_EASYBLOG_MODERATE_COMMENT_PUBLISHED_SUCCESS';

		if ($task == 'reject') {
			$message = 'COM_EASYBLOG_MODERATE_COMMENT_UNPUBLISHED_SUCCESS';
		}

		$this->info->set($message, 'success');

		// Get the permalink to the post
		$permalink = $post->getPermalink(false);

		return $this->app->redirect($permalink);
	}

	/**
	 * Updates existing comment
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function update()
	{
		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=comments');

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Get comment id
		$id	= $this->input->get('id', '', 'int');

		// Load the comment
		$comment = EB::table('Comment');
		$comment->load($id);

		// Check if comment exists
		if (!$id || !$comment->id) {
			EB::info()->set(JText::_('COM_EASYBLOG_COMMENT_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect($return);
		}

		// Get the posted data
		$post = $this->input->getArray('post');
		$comment->bindPost($post);

		// Load the blog object
		$post = EB::table('Post');
		$post->load($comment->post_id);

		// Test if user can really edit the comment
		if ($post->created_by != $my->id && !FH::isSiteAdmin() && !$this->acl->get('edit_comment')) {
			EB::info()->set(JText::_('COM_EB_NO_PERMISSIONS'), 'error');

			return $this->app->redirect($return);
		}

		// Validate comment stuffs
		$valid 	= $comment->validateData();

		if (!$valid) {
			EB::info()->set($comment->getError(), 'error');

			return $this->app->redirect($return);
		}

		// Set the last modified date
		$comment->modified = EB::date()->toSql();

		// Try to store the comment now
		$state = $comment->store();

		if (!$state) {
			EB::info()->set(JText::_('COM_EASYBLOG_COMMENT_FAILED_TO_SAVE'), 'error');

			return $this->app->redirect($return);
		}

		// Redirect users back
		EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_BOMMENTS_COMMENT_UPDATED_SUCCESS'), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Delete comment from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete()
	{
		// Check for tokens
		FH::checkToken();

		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=comments');

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {

			$comment = EB::table('Comment');
			$comment->load((int) $id);

			// Ensure that the user has access to publish items
			if (($this->my->id == 0 || $this->my->id != $comment->created_by || !$this->acl->get('delete_comment')) && !$this->acl->get('manage_comment') && !FH::isSiteAdmin()) {
				
				$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_COMMENT', 'error');

				return $this->app->redirect($return);
			}

			$state = $comment->delete();
		}

		$this->info->set('COM_EASYBLOG_DASHBOARD_DELETE_COMMENTS_SUCCESS', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Toggle publish for comments
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function togglePublish()
	{
		FH::checkToken();
		EB::requireLogin();

		// Get any return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=comments', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Ensure that the user is really allowed to perform this function
		if (!$this->acl->get('manage_comment') && !FH::isSiteAdmin()) {
			die();
		}

		$task = $this->getTask();
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as $id) {
			$comment = EB::table('Comment');
			$comment->load((int) $id);

			if (method_exists($comment, $task)) {
				$comment->$task();
			}
		}

		$message = 'COM_EASYBLOG_DASHBOARD_COMMENTS_COMMENT_PUBLISHED_SUCCESS';

		if ($task == 'unpublish') {
			$message = 'COM_EASYBLOG_DASHBOARD_COMMENTS_COMMENT_UNPUBLISHED_SUCCESS';
		}

		$message = JText::_($message);

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

}
