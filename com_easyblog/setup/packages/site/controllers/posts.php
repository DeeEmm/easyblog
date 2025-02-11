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

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerPosts extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');

		$this->registerTask('archive', 'toggleArchive');
		$this->registerTask('unarchive', 'toggleArchive');
	}

	/**
	 * Duplicates a blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function copy()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the id's
		$ids = $this->input->get('ids', array(), 'array');

		// Default redirection
		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_COPY_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$post = EB::post($id);
			$post->duplicate();
		}

		$this->info->set('COM_EASYBLOG_DASHBOARD_BLOG_COPIED_SUCCESS', 'success');
		return $this->app->redirect($redirect);
	}


	/**
	 * Auto posts blog posts into social sites
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function autopost()
	{
		// Set the default redirection url
		$return = $this->input->get('return', '', 'default');

		if ($return) {
			$return = base64_encode($return);
		} else {
			$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);
		}

		// Only allow authors with write privileges to use this
		if (!$this->acl->get('add_entry')) {
			die();
		}

		// Get the auto post type
		$type = $this->input->get('type', '', 'cmd');

		// Get the pot id
		$id = $this->input->get('id', 0, 'int');

		// Load up the post
		$post = EB::post($id);

		// Try to autopost now
		$post->autopost($type);

		$message = JText::sprintf('COM_EASYBLOG_OAUTH_POST_SUCCESS', $type);

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}


	/**
	 * Authorizes the password for the blog post.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function authorize($task = null)
	{
		// Default return url
		$return = $this->input->get('return', '', 'default');

		if ($return) {
			$return = base64_encode($return);
		}

		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			$this->info->set('COM_EASYBLOG_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect($return);
		}

		if (!$return) {
			$post = EB::post($id);

			$return = $post->getPermalink(false);
		}

		// Get the submitted password
		$password = $this->input->get('blogpassword_' . $id, '', 'var');

		// Get the current session data
		$session = JFactory::getSession();
		$session->set('PROTECTEDBLOG_' . $id, $password, 'EASYBLOG');

		$this->app->redirect($return);
	}

	/**
	 * Archives / unarchives a post or a list of post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toggleArchive()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the post id
		$ids = $this->input->get('ids', 0, 'array');

		// Get any return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Test the provided id
		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');
			return $this->app->redirect($return);
		}

		// Determines the current operation
		$task = $this->getTask();

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);

			// Check for permissions
			if (!$post->canModerate()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'), 'error');
				return $this->app->redirect($return);
			}

			$post->$task();
		}

		$message = 'COM_EASYBLOG_POST_ARCHIVED_SUCCESSFULLY';

		if ($task == 'unarchive') {
			$message = 'COM_EASYBLOG_POST_UNARCHIVED_SUCCESSFULLY';
		}

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Unfeature a blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function unfeature()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the list of blog id's
		$ids = $this->input->get('ids', array(), 'array');

		// Get any return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Check if user has access
		if (!FH::isSiteAdmin() && !$this->acl->get('feature_entry')) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			// Load the blog object
			$post = EB::post($id);

			// Check if user has access
			if (!$id || !$post->id) {

				$this->info->set('COM_EASYBLOG_INVALID_ID_PROVIDED', 'error');

				return $this->app->redirect($return);
			}

			// Ensure that the current user can moderate the post.
			if (!$post->canModerate()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'), 'error');
				return $this->app->redirect($return);
			}

			// Unfeature the post
			$post->removeFeatured();
		}

		$this->info->set('COM_EASYBLOG_BLOG_POSTS_UNFEATURED_SUCCESS', 'success');

		if ($this->doc->getType() == 'ajax') {
			return $this->ajax->redirect($return);
		}

		return $this->app->redirect($return);
	}

	/**
	 * Features a blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function feature()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		// Get any return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Check if user has access
		if (!FH::isSiteAdmin() && !$this->acl->get('feature_entry')) {

			EB::info()->set('COM_EASYBLOG_NOT_ALLOWED', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);

			// Do not allow password protected blog posts to be featured
			if ($this->config->get('main_password_protect') && $post->isPasswordProtected()) {
				$this->info->set('COM_EASYBLOG_PASSWORD_PROTECTED_CANNOT_BE_FEATURED', 'error');

				return $this->app->redirect($return);
			}

			$post->setFeatured();
		}

		$this->info->set('COM_EASYBLOG_BLOG_POSTS_FEATURED_SUCCESS', 'success');

		if ($this->doc->getType() == 'ajax') {
			return $this->ajax->redirect($return);
		}

		return $this->app->redirect($return);
	}

	/**
	 * Deletes a revision from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteRevision()
	{
		// Check for token
		FH::checkToken();

		// Load the revision
		$id = $this->input->get('id', 0, 'int');
		$revision = EB::table('Revision');
		$revision->load($id);

		if (!$revision->canDelete()) {
			return $this->ajax->reject(EB::exception(JText::_('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_DELETE_REVISION')));
		}

		if (!$revision->delete()) {
			return $this->ajax->reject(EB::exception('COM_EASYBLOG_COMPOSER_REVISIONS_ERRORS_DELETING_REVISION'));
		}

		return $this->ajax->resolve();
	}

	/**
	 * Purge revisions for a particular post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function purgeRevisions()
	{
		// Check for token
		FH::checkToken();

		// Load the revision
		$revisionid = $this->input->get('revisionid');
		$id = $this->input->get('id');

		$post = EB::post($id);

		if (!$post->canPurgeRevisions()) {
			return $this->ajax->reject(EB::exception(JText::_('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_PURGE_REVISIONS'), EASYBLOG_MSG_ERROR));
		}

		$ids = array();
		$ids[] = $post->revision->id;

		$ignoreRev = null;
		if ($post->revision->id != $revisionid) {
			$ignoreRev = $revisionid;

			$ids[] = $revisionid;
		}

		// purge now
		$state = $post->purgeRevisions($ignoreRev);

		$message = JText::_('COM_EASYBLOG_COMPOSER_PURGE_REVISIONS_SUCCESS');
		return $this->ajax->resolve($ids, EB::exception($message, EASYBLOG_MSG_SUCCESS));

	}

	/**
	 * Delete mulitple revisions from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteRevisions()
	{
		// Check for token
		FH::checkToken();

		$ids = $this->input->get('ids', '', 'array');

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=revisions', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {

			$revision = EB::table('Revision');
			$revision->load($id);

			if (!$revision->canDelete()) {
				$this->info->set(JText::_('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_DELETE_REVISION'), 'error');
				return $this->app->redirect($return);
			}

			$revision->delete();
		}

		EB::info()->set(JText::_('COM_EASYBLOG_DELETE_REVISIONS_SUCCESS'), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Trash blog posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function trash()
	{
		// Check for tokens
		FH::checkToken();

		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		if (!$ids) {
			$ids = $this->input->get('id', 0, 'int');

			if ($ids) {
				$ids = array($ids);
			}
		}

		if (!$ids) {
			throw EB::exception('Invalid id provided', 500);
		}

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$post = EB::post($id);

			if (!$post->canDelete()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_BLOG'), 'error');
				return $this->app->redirect($return);
			}

			$post->trash();
		}

		EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_TRASH_SUCCESS'), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Restoring blog posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function restore()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get a list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		if (!$ids) {
			$ids = $this->input->get('id', 0, 'int');

			if ($ids) {
				$ids = array($ids);
			}
		}

		if (!$ids) {
			throw EB::exception(JText::_('COM_EASYBLOG_INVALID_BLOG_ID'), 500);
		}

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// We should notify if the blog post is under moderation
		foreach ($ids as $id) {
			$id = (int) $id;
			$post = EB::post($id);

			$post->restore();
		}

		EB::info()->set(JText::_('COM_EASYBLOG_POSTS_RESTORED_SUCCESS'), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Deletes blog posts
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
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {
			$id = (int) $id;
			$post = EB::post($id);

			if (!$post->canDelete()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_BLOG'), 'error');
				return $this->app->redirect($return);
			}

			$post->delete();
		}

		EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_DELETE_SUCCESS'), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Toggle publish for posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function togglePublish()
	{
		// Check for tokens
		FH::checkToken();

		// Build the return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Ensure that the user has access to publish items
		if ($this->my->guest) {
			throw EB::exception('No permissions to publish or unpublish blog posts', 500);
		}

		// Get the task
		$task = $this->getTask();

		// Get id's
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$post = EB::post($id);

			if (!$this->acl->get('moderate_entry') && !$this->acl->get('publish_entry') && !FH::isSiteAdmin()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'), 'error');
				return $this->app->redirect($return);
			}

			if (method_exists($post, $task)) {

				$options = array();

				// require to normalise the post data if the post isnew
				if ($task == 'publish' && $post->isnew) {
					$options = array('normalizeData' => true);
				}

				$post->$task($options);
			}
		}

		$message = JText::_('COM_EASYBLOG_POSTS_PUBLISHED_SUCCESS');

		if ($task == 'unpublish') {
			$message = JText::_('COM_EASYBLOG_POSTS_UNPUBLISHED_SUCCESS');
		}

		// Set info data
		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Ensure that the user is allowed to save the blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function verifyAccess()
	{
		FH::checkToken();
		EB::requireLogin();

		// Ensure that the user really has permissions to create blog posts on the site
		if (!$this->acl->get('add_entry')) {
			throw EB::exception('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG');
		}

		// Ensure uid is provided
		$uid = $this->input->get('uid');

		if (empty($uid)) {
			throw EB::exception('COM_EASYBLOG_MISSING_UID');
		}
	}

	/**
	 * Given a revision id, update the post to use the revision
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function useRevision()
	{
		try {
			$this->verifyAccess();
		} catch (EasyBlogException $exception) {

			dump($exception->getMessage());
		}

		$return = $this->input->get('return', '', 'default');
		$uid = $this->input->get('uid', '', 'default');

		if (! $return) {
			$return = EBR::_('index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $uid, false);
		} else {
			$return = base64_decode($return);
		}

		// Load up the post
		$uid = $this->input->get('uid');
		$post = EB::post($uid);

		$post->published = EASYBLOG_POST_PUBLISHED;
		$post->useRevision();

		return $this->app->redirect($return);
	}

	/**
	 * Checks against the user's permissions
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function checkAccess()
	{
		try {
			$this->verifyAccess();
		} catch(EasyBlogException $exception) {

			if ($this->doc->getType() == 'html') {
				$this->info->set($exception);
				return $this->app->redirect(EBR::_('index.php?option=com_easyblog'));
			}

			return $this->ajax->reject($exception);
		}

		return true;
	}

	/**
	 * Auto saving process
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function autosave()
	{
		$this->checkAccess();

		// Get uid & data
		$uid = $this->input->get('uid');
		$data = $this->input->getArray('post');

		// Contents needs to be raw
		$data['content'] = $this->input->get('content', '', 'raw');
		$data['document'] = $this->input->get('document', '', 'raw');

		// Load up the post library
		$post = EB::post($uid);

		// let perform autosave checking for non new post.
		if (! $post->hasChanges($data)) {
			// return without further saving.
			return $this->ajax->resolve();
		}

		$post->bind($data, array());

		// Default options
		$options = array();
		$options['applyDateOffset'] = true;

		// Since this is auto saving, we do not want to verify this post as it isn't published yet.
		$options['validateData'] = false;
		$options['checkAutosave'] = true;

		// If this is a quote quick post, do not validate the content.
		if ($post->getType() == 'quote') {
			$options['validateData'] = false;
		}

		// Save post
		try {
			$post->save($options);
		} catch(EasyBlogException $exception) {

			// Reject if there is an error while saving post
			return $this->ajax->reject($exception);
		}

		$date = EB::date();
		$date->setTimezone();

		$message = JText::sprintf('COM_EASYBLOG_POST_AUTOMATICALLY_SAVED_AT', $date->format(JText::_('COM_EASYBLOG_COMPOSER_AUTOSAVE_TIME_FORMAT'), true));

		// Resolve with post data
		$data = $post->toData();

		// Reduces number of slashes.
		$data->revision->content = json_decode($data->revision->content);

		// Get the post's edit url
		$editLink = $post->getEditLink(false);

		return $this->ajax->resolve($message, $data, $editLink);
	}


	/**
	 * validate content used by ajax.
	 *
	 * @since	5.3
	 * @access	public
	 */
	public function validateContent()
	{
		$this->checkAccess();

		// Get uid & data
		$uid = $this->input->get('uid');
		$data = $this->input->getArray('post');

		// Contents needs to be raw
		$data['content'] = $this->input->get('content', '', 'raw');
		$data['document'] = $this->input->get('document', '', 'raw');

		// Load up the post library
		$post = EB::post($uid);
		$post->bind($data, array());

		// perform validate
		try {
			// init save options so that validation will go smooth
			$post->initSaveOptions();

			$post->validateTitle();
			$post->validateContent();
			$post->validateFields();

			return $this->ajax->resolve();

		} catch(EasyBlogException $exception) {
			return $this->ajax->reject($exception);
		}
	}


	/**
	 * Saves a blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save()
	{
		// Check for access
		$this->checkAccess();

		// Default redirection url after saving
		$redirect = EBR::_('index.php?option=com_easyblog&view=composer&tmpl=component&uid=1', false);

		// Get content and document data first before anything else. #1177
		// Contents needs to be raw
		$content = $this->input->get('content', '', 'raw');
		$document = $this->input->get('document', '', 'raw');

		// Unset these two post data to avoid timeout issue if the content is too large. #1177
		unset($_POST['document']);
		unset($_POST['content']);

		// Get uid & data
		$uid = $this->input->get('uid');
		$data = $this->input->getArray('post');

		// Re-map back the post data
		$data['content'] = $content;
		$data['document'] = $document;

		$_POST['document'] = $document;
		$_POST['content'] = $content;

		// Load up the post library
		$post = EB::post($uid);

		// Determines if this is a preview request
		$preview = $this->input->get('preview', false, 'bool');

		// Default options
		$options = [
			// since this is a form submit and we knwo the date that submited already with the offset timezone. we need to reverse it.
			'applyDateOffset' => true,

			// Determines this process from the preview or not
			'isPostPreviewProcess' => $preview,

			// We need to reverse the value because the options is to skip while the fieldset is to send or not.
			'skipNotifications' => !$this->input->get('send_notification_emails', true, 'bool'),

			'rejectMessage' => $this->input->get('rejectMessage', '', 'default')
		];

		// Check for subscription setting forgery. #2478#note_173917
		if ($this->config->get('main_subscription') && (!$this->acl->get('change_setting_subscription') && !EB::isSiteAdmin())) {

			// Retrieve the original value
			$options['skipNotifications'] = !$post->send_notification_emails;
			$data['send_notification_emails'] = $post->send_notification_emails;
		}

		$post->bind($data, []);

		// Default redirection url
		$redirect = EBR::_('index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $post->uid, false);

		// If this is a quote quick post or the post is just a draft, do not validate the data
		if ($post->isDraft() || $post->getType() == 'quote') {
			$options['validateData'] = false;
		}

		// Save post
		try {
			$post->save($options);
		} catch(EasyBlogException $exception) {

			// reset the dates to GMT values.
			$data['created'] = $post->created;
			$data['publish_up'] = $post->publish_up;
			$data['publish_down'] = $post->publish_down;

			EB::storeSession($data, 'EASYBLOG_COMPOSER_POST');

			// Reject if there is an error while saving post
			$this->info->set($exception);
			return $this->app->redirect($redirect);
		}

		// Default redirection url after saving
		$redirect = EBR::_('index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $post->uid, false);

		// Notify that post is successfully
		$state = EASYBLOG_MSG_SUCCESS;
		$message = '';

		if (!$post->isNew()) {
			$message = 'COM_EASYBLOG_POST_UPDATED_SUCCESS';
			$state = EASYBLOG_MSG_INFO;

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_POST_UPDATED', 'post', array(
				'link' => $post->getEditLink(),
				'postTitle' => JText::_($post->getTitle())
			));
		}

		// if this is a pending post, this mean admin is updating the post which is under pending approval.
		if ($post->isPending()) {
			$message = 'COM_EASYBLOG_POST_UPDATED_SUCCESS';
		}

		// If this is being submitted for approval
		if ($post->isBeingSubmittedForApproval()) {
			$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

			// there is a case where admin has no permission to publish #593
			if (EB::isFromAdmin()) {
				$redirect = 'index.php?option=com_easyblog&view=blogs';
			}

			$message = 'COM_EASYBLOG_POST_SUBMITTED_FOR_APPROVAL';
			$state = EASYBLOG_MSG_WARNING;
		}

		// If this is a draft post.
		if ($post->isDraft()) {
			$message = 'COM_EASYBLOG_POST_SAVED_FOR_LATER_SUCCESS';
			$state = EASYBLOG_MSG_INFO;
		}

		if ($post->isBeingRejected()) {
			$message = 'COM_EASYBLOG_POST_UPDATED_SUCCESS';
		}

		if ($post->isScheduled()) {
			$message = JText::sprintf('COM_EASYBLOG_POST_BEING_SCHEDULED', $post->getPublishDate(true)->format(JText::_('DATE_FORMAT_LC2')));
		}

		// if this is approval action, we should redirect the user back to pending review page.
		if (($post->isBeingApproved() || $post->isBeingRejected()) && ($this->acl->get('moderate_entry') || ($this->acl->get('manage_pending') && $this->acl->get('publish_entry')))) {

			$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=moderate', false);

			if (EB::isFromAdmin()) {
				$redirect = 'index.php?option=com_easyblog&view=blogs&layout=pending';
			}

		}

		if ($preview) {
			$redirect = $post->getPreviewLink(false);
		}

		// if this is being published and post is not published and user do not have edit entry acl,
		// we will redirect user to the dasshboard entries page. #2207
		if ($post->isBeingPublished() && !FH::isSiteAdmin() && !$this->acl->get('edit_entry')) {
			$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

			if (EB::isFromAdmin()) {
				$redirect = 'index.php?option=com_easyblog&view=blogs';
			}
		}

		if (!$message) {
			$message = JText::sprintf('COM_EASYBLOG_POST_SAVED_SUCCESS', $post->getPermalink(true, true));
		}

		// We do not want to set any info messages when a post is previewed
		if (!$preview) {
			$this->info->set($message, 'success');
		}

		return $this->app->redirect($redirect);
	}

	/**
	 * Method to re-send the notifications for selected blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function notify()
	{
		// Check for request forgeries
		FH::checkToken();

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries');

		// Get the blog post id
		$ids = $this->input->get('ids', array(), 'array');

		if (empty($ids)) {
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_INVALID_ID'), 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$post = EB::post($id);

			if (!$id || !$post->id) {
				$this->info->set(JText::_('COM_EASYBLOG_BLOGS_INVALID_ID'), 'error');
				return $this->app->redirect($return);
			}

			// Notify users
			$post->notify();
		}

		$message = JText::_('COM_EASYBLOG_BLOGS_NOTIFY_SUBSCRIBERS');

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Method to filter blog listing by custom field
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function filterField()
	{
		// Check for request forgeries!
		FH::checkToken();

		$data = $this->input->getArray('post');
		$view = $this->input->get('view', 'latest', 'string');
		$layout = $this->input->get('layout', '', 'string');
		$filterMode = $this->input->get('filtermode', 'include', 'string');
		$inclusion = $this->input->get('inclusion', '', 'string');
		$strictmode = $this->input->get('strictmode', false, 'bool') ? '1' : '0';

		$allowedViews = array('latest', 'categories');

		if (!in_array($view, $allowedViews)) {
			return;
		}

		$redirect = 'index.php?option=com_easyblog&view=' . $view;

		if ($view == 'categories' && $layout == 'listings') {
			$catid = $this->input->get('id', 0, 'int');
			$redirect .= '&layout=listings&id=' . $catid;
		}


		$redirect = EBR::_($redirect, false);

		$querystr = array();

		// Build the query
		foreach ($data as $key => $value) {

			if (strpos($key, 'field') !== false) {
				foreach ($value as $val) {
					$querystr[] = $key . '[]=' .$val;
				}

			}
		}

		if ($inclusion) {
			$inclusion = explode(',', $inclusion);

			foreach ($inclusion as $id) {
				$querystr[] = 'inclusion[]=' . $id;
			}
		}

		$querystr = implode('&',$querystr);

		if (strpos($redirect, '?')) {
			$redirect .= '&filter=field&filtermode=' . $filterMode . '&' . $querystr . '&strictmode=' . $strictmode;
		} else {
			$redirect .= '?filter=field&filtermode=' . $filterMode . '&' . $querystr . '&strictmode=' . $strictmode;
		}

		return $this->app->redirect($redirect);
	}

	/**
	 * Method to filter blog listing by custom field
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function saveFilter()
	{
		// Check for request forgeries!
		FH::checkToken();

		$jsonString = $this->input->get('jsonString', '', 'string');
		$view = $this->input->get('view', 'latest', 'string');
		$layout = $this->input->get('layout', '', 'string');
		$cid = $this->input->get('id', 0, 'int');

		$allowedViews = array('latest', 'categories');

		if (!in_array($view, $allowedViews)) {
			return;
		}

		// Get the field model
		$model = EB::model('fields');

		$state = $model->saveSearchFilter($jsonString, $cid);

		return $this->ajax->resolve();
	}

	/**
	 * Method to clear saved filter
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function clearFilter()
	{
		// Check for request forgeries!
		FH::checkToken();

		$view = $this->input->get('view', 'latest', 'string');
		$layout = $this->input->get('layout', '', 'string');
		$cid = $this->input->get('id', 0, 'int');

		$allowedViews = array('latest', 'categories');

		if (!in_array($view, $allowedViews)) {
			return;
		}

		$redirect = 'index.php?option=com_easyblog&view=' . $view;

		if ($view == 'categories' && $layout == 'listings') {
			$redirect .= '&layout=listings&id=' . $cid;
		}

		$redirect = EBR::_($redirect, false);

		// Get the field model
		$model = EB::model('fields');

		$state = $model->clearSearchFilter($cid);

		return $this->ajax->resolve($redirect);
	}

	/**
	 * Favourite a blog post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function favourite()
	{
		FH::checkToken();

		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=favourites', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);

			if (!$post->canFavourite()) {
				$this->info->set('COM_EB_NOT_ALLOW_TO_FAVOURITE', 'error');
				return $this->app->redirect($return);
			}

			if ($post->isFavourited()) {
				$this->info->set('COM_EB_POST_ALREADY_FAVOURITED', 'error');
				return $this->app->redirect($return);
			}

			$post->favourite($this->my->id);
		}

		if ($this->doc->getType() == 'ajax') {
			$buttonMessage = JText::_('COM_EB_UNFAVOURITE_THIS_POST');
			return $this->ajax->resolve('unfavourite', $buttonMessage);
		}

		$this->info->set('COM_EB_POST_FAVOURITE_SUCCESS', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Unfavourite a blog post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function unfavourite()
	{
		FH::checkToken();

		$ids = $this->input->get('ids', '', 'array');

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=favourites', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$post = EB::post($id);

			if (!$post->canFavourite()) {
				$this->info->set('COM_EB_NOT_ALLOW_TO_FAVOURITE', 'error');
				return $this->app->redirect($return);
			}

			if (!$post->isFavourited()) {
				$this->info->set('COM_EB_POST_IS_NOT_FAVOURITED_BEFORE', 'error');
				return $this->app->redirect($return);
			}

			$post->unfavourite($this->my->id);
		}

		if ($this->doc->getType() == 'ajax') {
			$buttonMessage = JText::_('COM_EB_FAVOURITE_THIS_POST');
			return $this->ajax->resolve('favourite', $buttonMessage);
		}

		$this->info->set('COM_EB_POST_UNFAVOURITE_SUCCESS', 'success');

		return $this->app->redirect($return);
	}
}
