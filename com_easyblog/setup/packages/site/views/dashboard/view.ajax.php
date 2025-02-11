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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewDashboard extends EasyBlogView
{
	/**
	 * Confirmation to autopost to the respective social sites
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmAutopost()
	{
		// Only allow authors with write privileges to use this
		if (!$this->acl->get('add_entry')) {
			die();
		}

		$type = $this->input->get('type', '' ,'cmd');
		$id = $this->input->get('id', 0, 'int');

		$theme = EB::themes();
		$theme->set('id', $id);
		$theme->set('type', $type);

		$output = $theme->output('site/dashboard/entries/dialogs/autopost');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to publish a comment
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmPublishComment()
	{
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/comments/dialogs/publish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to unpublish a comment
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmUnpublishComment()
	{
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/comments/dialogs/unpublish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to delete comments
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDeleteComment()
	{
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/comments/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to permanently delete blog posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmPermanentDelete()
	{
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=' . EASYBLOG_DASHBOARD_TRASHED, false);

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$theme->set('return', base64_encode($return));
		$output = $theme->output('site/dashboard/trash/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders a dialog for confirmation to approve posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmApproveBlog()
	{
		$ids = $this->input->get('ids', '', 'array');

		if (!$this->my->id || !$this->acl->get('manage_pending') && !FH::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'));
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/moderate/dialogs/approve');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to reject a blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmRejectBlog()
	{
		$ids = $this->input->get('ids', '', 'array');

		if (!$this->my->id || !$this->acl->get('manage_pending') && !FH::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'));
		}

        $theme = EB::themes();
        $theme->set('ids', $ids);
        $output = $theme->output('site/dashboard/moderate/dialogs/reject');

        return $this->ajax->resolve($output);

	}

	/**
	 * Confirmation to delete a tag from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDeleteTag()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/tags/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Trash blog posts
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function trash()
	{
		// Check for tokens
		FH::checkToken();

		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as $id) {

			$post = EB::post($id);

			if (!$post->canDelete()) {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_BLOG'));
			}

			$post->trash();
		}

		return $this->ajax->resolve(JText::_('COM_EASYBLOG_DASHBOARD_TRASH_SUCCESS'));
	}

	/**
	 * Display confirmation box to send notifications for the selected post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmNotify()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = EB::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('site/dashboard/entries/dialogs/notify');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to unpublish a post on the report page
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function confirmUnpublishPost()
	{
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/reports/dialogs/unpublish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to delete a post on the report page
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function confirmDeletePost()
	{
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/reports/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to delete a post on the report page
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function confirmDownloadGDPR()
	{
		$userId = $this->my->id;

		$table = EB::table('download');
		$table->load(array('userid' => $userId));
		$state = $table->getState();

		if ($state == EASYBLOG_DOWNLOAD_REQ_READY) {

			// generate download link.
			$link = $table->getDownloadLink();
			$message = JText::sprintf('COM_EB_GDPR_REQUEST_IS_READY_DESC', $link);

			$theme = EB::themes();
			$theme->set('title', JText::_('COM_EB_GDPR_REQUEST_IS_READY'));
			$theme->set('message', $message);
			$output = $theme->output('site/dashboard/account/dialogs/gdpr.ready');

			return $this->ajax->resolve($output);
		}

		// If the user already requested, we do not need to resend request.
		if ($state !== false) {
			$theme = EB::themes();
			$theme->set('title', JText::_('COM_EB_GDPR_REQUEST_BEING_PROCESS'));
			$theme->set('message', JText::_('COM_EB_GDPR_DOWNLOAD_DESC1'));
			$output = $theme->output('site/dashboard/account/dialogs/gdpr.progress');

			return $this->ajax->resolve($output);
		}

		$email = $this->my->email;
		$emailPart = explode('@', $email);
		$email = substr($emailPart[0], 0, 2) . '****' . substr($emailPart[0], -1) . '@' . $emailPart[1];

		$theme = EB::themes();
		$theme->set('userId', $userId);
		$theme->set('email', $email);
		$output = $theme->output('site/dashboard/account/dialogs/gdpr.confirm');

		return $this->ajax->resolve($output);
	}
}
