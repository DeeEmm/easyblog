<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewTeamblogs extends EasyBlogAdminView
{
	/**
	 * Displays confirmation to auto post the post
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function markAdmin()
	{

		FH::checkToken();

		$this->checkAccess('easyblog.manage.teamblog');

		$userId = $this->input->get('userid', 0, 'int');
		$teamId = $this->input->get('teamid', 0, 'int');

		if(empty($teamId) || empty($userId))
		{
			$this->ajax->reject(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		$this->setAsAdmin($teamId, $userId, true);
		return $this->ajax->resolve(true, JText::_('COM_EASYBLOG_TEAMBLOGS_REMOVE_ADMIN'));
	}

	public function removeAdmin()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('easyblog.manage.teamblog');

		$userId = $this->input->get('userid', 0, 'int');
		$teamId = $this->input->get('teamid', 0, 'int');

		if (empty($teamId) || empty($userId)) {
			$this->ajax->reject(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		$this->setAsAdmin($teamId, $userId, false);
		return $this->ajax->resolve(true, JText::_('COM_EASYBLOG_TEAMBLOGS_SET_ADMIN'));
	}


	public function setAsAdmin($teamId, $userId, $isAdmin)
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('easyblog.manage.teamblog');

		$db = EB::db();

		$query  = 'UPDATE `#__easyblog_team_users` SET ';
		if($isAdmin)
			$query	.= ' `isadmin` = ' . $db->Quote('1');
		else
			$query	.= ' `isadmin` = ' . $db->Quote('0');
		$query  .= ' WHERE `team_id` = ' . $db->Quote($teamId);
		$query  .= ' AND `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	 * Allows caller to browse for team blogs
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function browse()
	{
		$theme = EB::themes();

		$output	= $theme->output('admin/teamblogs/dialogs/browse');
		
		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to accept a teamblog request
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function confirmAccept()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = EB::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('admin/teamblogs/dialogs/accept');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to reject a teamblog request
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function confirmReject()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = EB::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('admin/teamblogs/dialogs/reject');

		return $this->ajax->resolve($output);
	}
}
