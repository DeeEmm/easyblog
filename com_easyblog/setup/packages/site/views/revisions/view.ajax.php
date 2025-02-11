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

require_once(EBLOG_ROOT . '/views/views.php');

class EasyBlogViewRevisions extends EasyBlogView
{
	/**
	 * Given the current revision id, and the target id, display a comparison between 2 revisions
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compare()
	{
		// Current is the current revision that is being edited
		$current = $this->input->get('current', 0, 'int');

		// Target is the comparison target
		$target = $this->input->get('target', 0, 'int');

		// Load the current revision that is being edited
		$currentRevision = EB::table('Revision');
		$currentRevision->load($current);

		// Check if the user has access to the post or not
		$post = $currentRevision->getPost();

		if (!$post->canEdit()) {
			return $this->ajax->reject(EB::exception('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_COMPARE_REVISION'));
		}

		// Revision being compared to
		$targetRevision = EB::table('Revision');
		$targetRevision->load($target);

		$title = JText::sprintf('COM_EASYBLOG_COMPARING_REVISION', $currentRevision->ordering, $targetRevision->ordering);

		$theme = EB::themes();
		$theme->set('current', $currentRevision);
		$theme->set('target', $targetRevision);

		$output = $theme->output('site/composer/revisions/compare');

		return $this->ajax->resolve($title, $output);
	}

	/**
	 * Confirmation to delete a revision
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteRevision()
	{
		$id = $this->input->get('id', 0, 'int');

		// Load the revision
		$revision = EB::table('Revision');
		$revision->load($id);

		if (!$revision->canDelete()) {
			return $this->ajax->reject(EB::exception('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_DELETE_REVISION'));
		}

		$theme = EB::themes();
		$theme->set('revision', $revision);

		$output = $theme->output('site/composer/revisions/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to purge revisions
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function purgeRevision()
	{
		// Check for tokens
		FH::checkToken();

		$id = $this->input->get('id', 0, 'int');
		if (! $id) {
			return $this->ajax->reject(EB::exception('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Load the post
		$post = EB::post($id);

		if (!$post->canPurgeRevisions()) {
			return $this->ajax->reject(EB::exception('COM_EASYBLOG_COMPOSER_NOT_ALLOWED_TO_PURGE_REVISIONS'));
		}

		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/composer/revisions/dialogs/purge');

		return $this->ajax->resolve($output);
	}


	/**
	 * Confirmation to switch post to use specific revision
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmUseRevision()
	{
		$uid = $this->input->get('uid');

		$post = EB::post($uid);

		if (!$post->canEdit()) {
			return $this->ajax->reject(EB::exception('You are not allowed to edit this post'));
		}

		$theme = EB::themes();
		$theme->set('post', $post);

		$output = $theme->output('site/composer/revisions/dialogs/switch');

		return $this->ajax->resolve($output);
	}

	/**
	 * Retrieves a list of revisions for the post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getRevisions()
	{
		$uid = $this->input->get('uid');

		// Load up the post
		$post = EB::post($uid);

		// Ensure that the user is allowed to edit and view revisions from this post
		if (!$post->canEdit()) {
			return $this->ajax->reject(EB::exception('You are not allowed to edit this post'));
		}

		$revisions = $post->getRevisions();

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('revisions', $revisions);

		$output = $theme->output('site/composer/revisions/list');

		return $this->ajax->resolve($output);
	}

	/**
	 * Set a revision as the current revision
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function setAsCurrent()
	{
		$revisionId = $this->input->get('revisionId', null, 'int');

		// Stop if no revisionId given.
		if (is_null($revisionId)) {
			return $this->ajax->reject(EB::Exception('COM_EASYBLOG_REVISION_UNKNOWN_REVISION_ID'));
		}

		$revision = EB::table('Revision');
		$revision->load($revisionId);
		$state = $revision->setAsCurrent();

		if (!$state) {
			return $this->ajax->reject(EB::Exception('COM_EASYBLOG_REVISION_USE_REVISION_FAILED'));
		}

		return $this->ajax->reject(EB::Exception('COM_EASYBLOG_REVISION_USE_REVISION_SUCCESS'), EASYBLOG_MSG_SUCCESS);
	}
}
