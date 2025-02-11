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

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerTeamBlogs extends EasyBlogController
{
	/**
     * Update a teamblog
     *
     * @since   5.1
     * @access  public
     */
	public function save()
	{
		FH::checkToken();
		EB::requireLogin();

		// Default return URL
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false);

		// Ensure that the user has access to create teamblog
		if (!FH::isSiteAdmin() && !$this->acl->get('create_team_blog')) {
			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_TEAMBLOGS', 'error');
			return $this->app->redirect($return);
		}

		$id = $this->input->get('id', 0, 'int');

		// Get the title of the teamblog
		$title = $this->input->get('title', '', 'default');

		if (!$title) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_EMPTY_TEAMBLOG_TITLE_ERROR', 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogForm&id=' . $id, false));
		}

		$teamblog = EB::table('Teamblog');
		$teamblog->load($id);

		// Default success message
		$message = 'COM_EASYBLOG_DASHBOARD_TEAMBLOG_UPDATED_SUCCESSFULLY';

		$post = $this->input->getArray('post');
		$post['title'] = $title;
		$post['access'] = $this->input->get('access', 0, 'int');
		$post['description'] = $this->input->get('description', '', 'raw');

		$teamblog->bind($post);

		// Set the teamblog as published by default.
		$teamblog->published = true;

		// Save the teamblog 1st so that the id get updated
		$teamblog->store();

		// Set a teamblog avatar if required
		$file = $this->input->files->get('Filedata', '', 'array');

		if (isset($file['name']) && !empty($file['name'])) {
			$teamblog->avatar = EB::uploadMediaAvatar('team', $teamblog);
			$teamblog->store();
		}

		$this->info->set(JText::sprintf($message, $teamblog->getTitle()), 'success');
		return $this->app->redirect($return);
	}

	/**
     * Function to create teamblog
     *
     * @since   5.1
     * @access  public
     */
	public function create()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Default return URL
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false);

		// Ensure that the user has access to create teamblog
		if (!FH::isSiteAdmin() && !$this->acl->get('create_team_blog')) {
			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_TEAMBLOGS', 'error');
			return $this->app->redirect($return);
		}

		// Possibility is that this teamblog is being edited.
		$id = $this->input->get('id', 0, 'int');

		// Get the title of the teamblog
		$title = $this->input->get('title', '', 'default');

		if (!$title) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_EMPTY_TEAMBLOG_TITLE_ERROR', 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogForm&id=' . $id, false));
		}

		$teamblog = EB::table('TeamBlog');
		$teamblog->load($id);

		// Default success message
		$message = 'COM_EASYBLOG_DASHBOARD_TEAMBLOG_ADDED_SUCCESSFULLY';

		$post = $this->input->getArray('post');
		$post['title'] = $title;
		$post['created_by'] = $this->my->id;
		$post['access'] = $this->input->get('access', 0, 'int');
		$post['description'] = $this->input->get('description', '', 'raw');

		$teamblog->bind($post);

		// Set the teamblog as published by default.
		$teamblog->published = true;

		// Save the cat 1st so that the id get updated
		$teamblog->store();

		// Set a teamblog avatar if required
		$file = $this->input->files->get('Filedata', '', 'array');

		if (isset($file['name']) && !empty($file['name'])) {
			$teamblog->avatar = EB::uploadMediaAvatar('team', $teamblog, false);
			$teamblog->store();
		}

		// Automatically add the user that created the team as team member with admin privilages.
		$member = EB::table('TeamBlogUsers');
		$member->load(array('team_id' => $teamblog->id, 'user_id' => $this->my->id));

		// If the user already exist, skip it.
		if (!$member->user_id) {
			$member->team_id = $teamblog->id;
			$member->user_id = $this->my->id;
			$member->isadmin = true;

			// Store the new members
			$member->store();
		}

		$this->info->set(JText::sprintf($message, $teamblog->getTitle()), 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Processes requests to join the team
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function join()
	{
		// Check for request forgeries
		FH::checkToken();

		// Only allow registered users
		EB::requireLogin();

		$return = $this->input->get('return', '', 'default');

		if ($return) {
			$return = base64_decode($return);
		}

		// Default return url
		if (!$return) {
			$return = EB::_('index.php?option=com_easyblog&view=teamblog', false);
		}

		// Get the team data
		$id = $this->input->get('id', 0, 'int');

		$team = EB::table('TeamBlog');
		$team->load($id);

		if (!$id || !$team->id) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect($return);
		}

		$model = EB::model('TeamBlogs');
		$isMember = $model->isMember($team->id, $this->my->id);

		// Check if the user already exists
		if ($isMember) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_ALREADY_MEMBER', 'error');
			return $this->app->redirect($return);
		}

		// If the user is a site admin, they are free to do whatever they want
		if (FH::isSiteAdmin()) {
			$map = EB::table('TeamBlogUsers');
			$map->user_id = $this->my->id;
			$map->team_id = $team->id;
			$map->store();

			$this->info->set('COM_EASYBLOG_TEAMBLOG_REQUEST_JOINED', 'success');
		} else {
			// Create a new request
			$request = EB::table('TeamBlogRequest');
			$request->team_id = $team->id;
			$request->user_id = $this->my->id;
			$request->ispending = true;
			$request->created = EB::date()->toSql();

			// If request was already made previously, skip this
			if ($request->exists()) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_REQUEST_ALREADY_SENT', 'error');

				return $this->app->redirect($return);
			}

			// Store the request now
			$state = $request->store();

			if (!$state) {
				$this->info->set($request->getError(), 'error');

				return $this->app->redirect($return);
			}

			// Send moderation emails
			$request->sendModerationEmail();

			$this->info->set('COM_EASYBLOG_TEAMBLOG_REQUEST_SENT', 'success');
		}


		return $this->app->redirect($return);
	}

	/**
	 * Allows caller to approve a team request
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function approve()
	{
		EB::requireLogin();

		$ids = $this->input->get('ids', array(), 'array');
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=requests', false);

		if (!$ids) {
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {

			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$request = EB::table('TeamBlogRequest');
			$request->load($id);

			if (!$id || !$request->id) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED');
				return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
			}

			// Ensure that the user has access to perform this
			if (!$request->canModerate()) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_MODERATE_NO_ACCESS');
				return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
			}

			// Approve the request
			$state = $request->approve();

			if (!$state) {
				$this->info->set($request->getError(), 'error');
				return $this->app->redirect($return);
			}
		}

		$this->info->set('COM_EASYBLOG_TEAMBLOG_APPROVAL_APPROVED', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Allows caller to reject a team request
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function reject()
	{
		$ids = $this->input->get('ids', array(), 'array');
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=requests', false);

		if (!$ids) {
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {

			$id = (int) $id;

			if (!$id) {
				continue;
			}
			
			$request = EB::table('TeamBlogRequest');
			$request->load($id);

			if (!$id || !$request->id) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED');
				return $this->app->redirect($return);
			}

			// Ensure that the user has access to perform this
			if (!$request->canModerate()) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_MODERATE_NO_ACCESS');
				return $this->app->redirect($return);
			}
			
			$state = $request->reject();

			if (!$state) {
				$this->info->set($request->getError(), 'error');
				return $this->app->redirect($return);
			}
		}

		$this->info->set('COM_EASYBLOG_TEAMBLOG_APPROVAL_REJECTED', 'success');
		return $this->app->redirect($return);
	}


	/**
	 * Allows caller to leave a team
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function leave()
    {
    	// Check for request forgeries
    	FH::checkToken();

    	// Ensure that the user is logged in first
    	EB::requireLogin();

		$return = $this->input->get('return', '', 'default');

		if ($return) {
			$return = base64_decode($return);
		}

		// Default return url
		if (!$return) {
			$return = EB::_('index.php?option=com_easyblog&view=teamblog', false);
		}

    	// Get the team object
    	$ids = $this->input->get('ids', array(), 'array');

    	foreach ($ids as $id) {
	    	$team = EB::table('TeamBlog');
	    	$team->load($id);

			if (!$id || !$team->id) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED', 'error');

				return $this->app->redirect($return);
			}

			// Ensure that the current user requesting to leave the team is really a member of the team
			$model = EB::model('TeamBlogs');
			$isMember = $model->isMember($team->id, $this->my->id);

			if (!$isMember) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_NOT_MEMBER_OF_TEAM', 'error');
				return $this->app->redirect($return);
			}

			// Get the total members in the team because we do not want to allow empty team members in a team
			$count = $team->getMemberCount();

			if ($count <= 1) {
				$this->info->set('COM_EASYBLOG_TEAMBLOG_YOU_ARE_LAST_MEMBER', 'error');

				return $this->app->redirect($return);
			}

			// Delete the member now
			$team->deleteMembers($this->my->id);
		}

		$this->info->set('COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM_SUCCESS', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Process publish teams
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function publish()
	{
		EB::requireLogin();
		FH::checkToken();

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false);

		if (!$this->acl->get('create_team_blog') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED_HERE', 'error');
			return $this->app->redirect($return);
		}

		$ids = $this->input->get('ids', array(), 'array');

		$team = EB::table('TeamBlog');
		$team->publish($ids);

		$this->info->set('COM_EASYBLOG_PUBLISH_TEAMBLOGS_SUCCESS', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Process unpublish teams
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function unpublish()
	{
		EB::requireLogin();
		FH::checkToken();

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false);

		if (!$this->acl->get('create_team_blog') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED_HERE', 'error');
			return $this->app->redirect($return);
		}

		$ids = $this->input->get('ids', array(), 'array');

		$team = EB::table('TeamBlog');
		$team->unpublish($ids);

		$this->info->set('COM_EASYBLOG_UNPUBLISHED_TEAMBLOG_SUCCESS', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Method to delete teams from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete()
	{
		EB::requireLogin();
		FH::checkToken();

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false);

		if (!FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED_HERE', 'error');
			return $this->app->redirect($return);
		}

		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$team = EB::table('TeamBlog');
			$team->load((int) $id);

			$team->delete();
		}

		$this->info->set('COM_EASYBLOG_TEAMBLOG_REMOVED_SUCCESS', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Add members into the teams
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function addMember()
	{
		EB::requireLogin();
		FH::checkToken();

		$teamId = $this->input->get('teamId', 0, 'int');

		$team = EB::table('TeamBlog');
		$team->load($teamId);

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false);		

		if (!$team->id || !$team->isTeamAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED_HERE', 'error');
			return $this->app->redirect($return);
		}

		$uid = $this->input->get('uid', array(), 'array');

		if (empty($uid)) {
			$this->info->set('COM_EASYBLOG_TEAMBLOG_ERRORS_PLEASE_SPECIFY_USERS', 'error');
			return $this->app->redirect($return);
		}

		// Add members now
		foreach ($uid as $id) {
			$member = EB::table('TeamBlogUsers');
			$member->load(array('team_id' => $team->id, 'user_id' => $id));

			// If the user already exist, skip it.
			if (!$member->user_id) {
				$member->team_id = $team->id;
				$member->user_id = $id;

				// Store the new members
				$member->store();
			}
		}

		$this->info->set('COM_EASYBLOG_TEAMBLOG_MEMBERS_ADDED_SUCCESSFULLY', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Remove member from team
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeMember()
	{
		EB::requireLogin();
		FH::checkToken();

		$id = $this->input->get('id', 0, 'int');
		$teamId = $this->input->get('teamId', 0, 'int');

		// load team
		$team = EB::table('Teamblog');
		$team->load($teamId);

		if (!$team->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_ID_PROVIDED'));
		}

		// Only team admin can perform this action
		if (!$team->isTeamAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED_HERE'));
		}

		// Ensure that user can really remove the team member
		if (!$team->canRemoveMember($id)) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED_HERE'));
		}

		$model = EB::model('Teamblogs');
		$state = $model->removeMember($id, $teamId);

		if (!$state) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_TEAMBLOG_REMOVE_MEMBER_ERROR'));
		}

		return $this->ajax->resolve();
	}

	/**
	 * Set member as admin
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function setAdmin()
	{
		EB::requireLogin();
		FH::checkToken();

		// Only site admin will have this privilege
		if (!FH::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED_HERE'));
		}

		$id = $this->input->get('id', 0, 'int');
		$teamId = $this->input->get('teamId', 0, 'int');

		$model = EB::model('Teamblogs');
		$state = $model->setAdmin($id, $teamId);

		if (!$state) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SET_ADMIN_ERROR'));
		}

		return $this->ajax->resolve();
	}

	/**
	 * Remove admin right from member
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeAdmin()
	{
		EB::requireLogin();
		FH::checkToken();

		if (!FH::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED_HERE'));
		}

		$id = $this->input->get('id', 0, 'int');
		$teamId = $this->input->get('teamId', 0, 'int');

		$model = EB::model('Teamblogs');
		$state = $model->removeAdmin($id, $teamId);

		if (!$state) {
			return $this->ajax->resolve(JText::_('COM_EASYBLOG_REMOVE_ADMIN_ERROR'));
		}

		return $this->ajax->resolve();
	}		
}
