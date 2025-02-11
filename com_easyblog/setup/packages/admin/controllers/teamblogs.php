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

class EasyBlogControllerTeamBlogs extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('add' , 'edit');

		// Saving
		$this->registerTask('savenew', 'save');
		$this->registerTask('apply', 'save');

		$this->registerTask('approve', 'respond');
		$this->registerTask('reject', 'respond');

		$this->registerTask('publish','togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Allows caller to save the blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('teamblog');

		// Get the posted data
		$post = $this->input->getArray('post');

		// Get the team id if this is being edited
		$id = $this->input->get('id', 0, 'int');

		// Allow form to submit html codes
		$desc = $this->input->get('write_description', '', 'raw');
		$post['description'] = $desc;

		// Prepare the redirection url
		$task = $this->getTask();

		// Set the initial default redirection url
		$redirect = 'index.php?option=com_easyblog&view=teamblogs&layout=form';

		if ($task === 'apply') {
			$redirect .= '&id=' . $id;
		}

		// If the title is not set, we need to prevent users from saving the team blog
		if (!isset($post['title'])) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_TEAM_BLOG_TITLE'), 'error');

			return $this->app->redirect($redirect);
		}

		// Load up the team object.
		$team = EB::table('TeamBlog');
		$team->load($id);

		$isNew = $team->id ? false : true;

		// Bind the posted data
		$team->bind($post);

		// Ensure that the data is correct
		$team->title = EBString::trim($team->title);
		$team->alias = EBString::trim($team->alias);

		// Try to save the team
		$state = $team->store();

		if (!$state) {
			$this->info->set($team->getError(), 'error');

			return $this->app->redirect($redirect);
		}

		// Process team avatars
		$file = $this->input->files->get('avatar', '');

		if (isset($file['name']) && !empty($file['name'])) {
			$team->uploadAvatar($file);
		}

		// Bind the meta data
		$meta = [];
		$meta['keywords'] = $this->input->get('keywords', '', 'raw');
		$meta['description'] = $this->input->get('description', '', 'raw');
		$meta['type'] = META_TYPE_TEAM;
		$meta['content_id'] = $team->id;

		// Try to load the meta for the team
		$metaTable = EB::table('Meta');
		$metaTable->load(['type' => META_TYPE_TEAM, 'content_id' => $team->id]);
		$metaTable->bind($meta);

		// Save the meta data
		$metaTable->store();

		// Get the team model
		$model = EB::model('TeamBlogs');

		// Delete groups first before adding new
		if ($team->id) {
			$model->deleteGroupRelations($team->id);
		}

		// Try to set the groups
		if (isset($post['groups'])) {
			foreach ($post['groups'] as $gid) {

				$group = EB::table('TeamBlogGroup');
				$group->team_id = $team->id;
				$group->group_id = $gid;

				$group->store();
			}
		}

		// Delete team members here
		if (isset($post['deletemembers'])) {
			$items = explode(',', $post['deletemembers']);

			if (count($items) > 0) {
				foreach ($items as $id) {
					$team->deleteMembers($id);
				}
			}
		}

		// Store new members items
		if (isset($post['members'])) {
			foreach ($post['members'] as $id) {

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
		}

		$actionString = $isNew ? 'COM_EB_ACTIONLOGS_TEAMBLOGS_CREATED' : 'COM_EB_ACTIONLOGS_TEAMBLOGS_UPDATED';
		$actionlog = EB::fd()->getActionLog();
		$actionlog->log($actionString, 'post', array(
			'link' => 'index.php?option=com_easyblog&view=teamblogs&layout=form&id=' . $team->id,
			'teamTitle' => ucfirst($team->title)
		));

		// Set the info
		$this->info->set('COM_EASYBLOG_TEAMBLOG_SAVED_SUCCESSFULLY', 'success');

		if ($task == 'apply') {
			return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs&layout=form&id=' . $team->id);
		}

		if ($task == 'savenew') {
			return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs&layout=form');
		}

		return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs');
	}

	/**
	 * Remove team blogs from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('teamblog');

		// Get the id's
		$ids = $this->input->get('cid', array(), 'array');


		if (!$ids) {
			$this->info->set('COM_EASYBLOG_TEAMBLOGS_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs');
		}

		foreach ($ids as $id) {

			$team = EB::table('TeamBlog');
			$team->load((int) $id);

			$team->delete();
		}

		$this->info->set('COM_EASYBLOG_TEAMBLOGS_DELETED_SUCCESSFULLY', 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs');
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

		$this->checkAccess('teamblog');

		// Get the team ids to be published
		$ids = $this->input->get('cid', '', 'array');


		if (!$ids) {
			EB::info()->set(JText::_('COM_EASYBLOG_TEAMBLOGS_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs');
		}


		$team = EB::table('TeamBlog');
		$task = $this->getTask();
		$team->$task($ids);

		$message = $task == 'publish' ? JText::_('COM_EASYBLOG_TEAMBLOGS_PUBLISHED_SUCCESS') : JText::_('COM_EASYBLOG_TEAMBLOGS_UNPUBLISHED_SUCCESS');

		EB::info()->set($message, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs');
	}

	function markAdmin()
	{
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('teamblog');

		$teamId	= $this->input->get('teamid', '');
		$userId	= $this->input->get('userid', '');

		if (empty($teamId) || empty($userId)) {
			$this->setRedirect('index.php?option=com_easyblog&view=teamblogs');
		}

		$this->setAsAdmin($teamId, $userId, true);

		$this->setRedirect('index.php?option=com_easyblog&c=teamblogs&task=edit&id=' . $teamId);
	}

	function removeAdmin()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('teamblog');

		$teamId = $this->input->get('teamid', '');
		$userId = $this->input->get('userid', '');

		if (empty($teamId) || empty($userId)) {
			$this->setRedirect('index.php?option=com_easyblog&view=teamblogs');
		}

		$this->setAsAdmin($teamId, $userId, false);

		$this->setRedirect('index.php?option=com_easyblog&c=teamblogs&task=edit&id=' . $teamId);
	}

	function setAsAdmin($teamId, $userId, $isAdmin)
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('teamblog');

		$db = EB::db();

		$query = 'UPDATE `#__easyblog_team_users` SET ';
		
		if ($isAdmin) {
			$query .= ' `isadmin` = ' . $db->Quote('1');
		} else {
			$query .= ' `isadmin` = ' . $db->Quote('0');
		}

		$query  .= ' WHERE `team_id` = ' . $db->Quote($teamId);
		$query  .= ' AND `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	 * Respond to a team request
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function respond()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('teamblog');

		$ids = $this->input->get('ids', '', 'array');

		if (!$ids) {
			EB::info()->set(JText::_('COM_EASYBLOG_TEAMBLOGS_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs&layout=requests');
		}

		// Get the task
		$task = $this->getTask();

		foreach ($ids as $id) {
			// Load the request
			$request = EB::table('TeamBlogRequest');
			$request->load($id);

			$request->$task();
		}

		$message = $task == 'approve' ? JText::_('COM_EASYBLOG_TEAMBLOGS_APPROVED_REQUESTS_SUCCESS') : JText::_('COM_EASYBLOG_TEAMBLOGS_REJECT_REQUESTS_SUCCESS');

		EB::info()->set($message, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=teamblogs&layout=requests');
	}
}
