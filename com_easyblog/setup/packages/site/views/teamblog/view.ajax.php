<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewTeamBlog extends EasyBlogView
{
	/**
	 * Displays the confirmation to join the team
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function join()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Get the team object
		$id = $this->input->get('id', 0, 'int');
		$team = EB::table('TeamBlog');
		$team->load($id);

		if (!$id || !$team->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED'));
		}

		// Return url
		$return = $this->input->get('return', '', 'default');

		$template = EB::themes();
		$template->set('team', $team);
		$template->set('return', $return);

		$output = $template->output('site/teamblogs/dialogs/join');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays the confirmation to leave the team
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function leave($return = null)
	{
		// Require user to be logged in
		EB::requireLogin();

		// Get the team object
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		// Return url
		if (!$return) {
			$return = $this->input->get('return', '', 'default');
		}

		$template = EB::themes();
		$template->set('ids', $ids);
		$template->set('return', $return);

		$output = $template->output('site/teamblogs/dialogs/leave');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays all members from a team
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function viewMembers()
	{
		$ids = $this->input->get('ids', array(), 'array');
		$team = EB::table('TeamBlog');

		if (count($ids) > 1) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED'));
		}

		// Since we only display a team, the we can safely get the first id.
		$team->load($ids[0]);

		if (!$team->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED'));
		}

		$model = EB::model('TeamBlogs');
		$members = $model->getAllMembers($team->id);

		$template = EB::themes();
		$template->set('members', $members);
		$template->set('team', $team);
		$output = $template->output('site/teamblogs/dialogs/members');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation dialog to leave team from dasboard
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmLeave()
	{
		$returnUrl = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs');
		$returnUrl = base64_encode($returnUrl);

		return $this->leave($returnUrl);
	}

	/**
	 * Confirmation dialog to delete teams
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDelete()
	{
		FH::checkToken();
		EB::requireLogin();

		if (!FH::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$ids = $this->input->get('ids', array(), 'array');

		$template = EB::themes();
		$template->set('ids', $ids);
		$output = $template->output('site/dashboard/teamblogs/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Dialog to display list of authors
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function inviteMembers()
	{
		FH::checkToken();
		EB::requireLogin();

		$ids = $this->input->get('ids', array(), 'array');
		$id = isset($ids[0]) ? $ids[0] : 0;

		$team = EB::table('TeamBlog');
		$team->load($id);

		if (!$team->id) {
			return $this->ajax->reject('Invalid Id');
		}

		// Only admin and site admin can invite members
		if (!$team->isTeamAdmin()) {
			return $this->ajax->reject('Not Allowed');
		}

		// Retrieve current team members to exclude in the search list
		$members = $team->getMembers();
		$users = array();

		foreach ($members as $member) {
			$users[] = $member->user_id;
		}

		$template = EB::themes();
		$template->set('teamId', $id);
		$template->set('users', $users);
		$output = $template->output('site/dashboard/teamblogs/dialogs/invite');

		return $this->ajax->resolve($output);
	}
}
