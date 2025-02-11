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

require_once(__DIR__ . '/table.php');

class EasyBlogTableTeamBlogRequest extends EasyBlogTable
{
	public $id = null;
	public $team_id	= null;
	public $user_id	= null;
	public $ispeding = null;
	public $created = null;

	public function __construct(& $db )
	{
		parent::__construct( '#__easyblog_team_request' , 'id' , $db );
	}

	/**
	 * Determines if the current viewer can moderate this request
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function canModerate()
	{
		$team = $this->getTeam();

		return $team->isTeamAdmin() || FH::isSiteAdmin();
	}

	/**
	 * Retrieves the team object
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getTeam()
	{
		static $teams = array();

		if (!isset($teams[$this->team_id])) {

			$team = EB::table('TeamBlog');
			$team->load($this->team_id);

			$teams[$this->team_id] = $team;
		}

		return $teams[$this->team_id];
	}


	/**
	 * Retrieves the user object that initiated this request
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getUser()
	{
		static $users = array();

		if (!isset($users[$this->user_id])) {
			$user = EB::user($this->user_id);
			$users[$this->user_id] = $user;
		}

		return $users[$this->user_id];
	}

	/**
	 * Approves a team request
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function approve()
	{
		// Add the user into the team blog users listing
		$teamUser = EB::table('TeamBlogUsers');
		$teamUser->user_id = $this->user_id;
		$teamUser->team_id = $this->team_id;

		$state = $teamUser->store();

		if (!$state) {
			$this->setError($teamUser->getError());
			return false;
		}

		// Send notifications to the user that he's been approved.
		$this->notify();

		// Once the request is approved, delete this record.
		$this->delete();

		return true;
	}

	/**
	 * Rejects a team request
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function reject()
	{
		// Send notifications to the user that he's been rejected.
		$this->notify(true);

		// Once the request is approved, delete this record.
		$this->delete();

		return true;
	}

	/**
	 * Determines if a request already exists on the site
	 *
	 * @since	5.0
	 * @access	public	
	 */
	public function exists()
	{
		$db		= EB::db();

		$query	= 'SELECT COUNT(1) FROM `#__easyblog_team_request` '
				. 'WHERE `team_id`=' . $db->Quote( $this->team_id ) . ' '
				. 'AND `user_id`=' . $db->Quote( $this->user_id ) . ' '
				. 'AND `ispending` = ' . $db->Quote('1');
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	/**
	 * Notifies the admin that a new request to join the team
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function sendModerationEmail()
	{
		// Send email to the team admin's.
		$team = EB::table('TeamBlog');
		$team->load($this->team_id);

		$notification = EB::notification();
		$emails = array();

		$config = EB::config();

		if ($config->get('custom_email_as_admin')) {
			$notification->getCustomEmails($emails);
		} else {
			$notification->getAdminEmails($emails);
		}

		$notification->getTeamAdminEmails($emails, $team->id);

		$user = EB::user($this->user_id);

		if (!$emails) {
			return false;
		}

		$data = array(
					'teamName' => $team->title,
					'teamLink' => $team->getExternalPermalink(),
					'authorAvatar' => $user->getAvatar(),
					'authorLink' => EBR::getRoutedURL('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $user->id, false, true),
					'authorName' => $user->getName(),
					'requestDate' => EB::date()->format(JText::_('DATE_FORMAT_LC1')),
					'reviewLink' => EBR::getRoutedURL('index.php?option=com_easyblog&view=dashboard&layout=requests', false, true)
				);

		$subject = JText::sprintf('COM_EASYBLOG_TEAMBLOGS_JOIN_REQUEST', $team->title);

		return $notification->send($emails, $subject, 'team.request', $data);
	}

	/**
	 * Sends notifications out
	 *
	 * @since	5.0
	 * @access	public	
	 */
	public function notify($reject = false)
	{
		$notification = EB::notification();

		// Load up the team blog
		$team = EB::table('TeamBlog');
		$team->load($this->team_id);

		// Send email notifications to the requester
		$requester = JFactory::getUser($this->user_id);

		$data = array( 'teamName' => $team->title,
						'teamDescription' => $team->getDescription(),
						'teamAvatar' => $team->getAvatar(),
						'teamLink' => EBR::getRoutedURL('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $this->team_id , false , true)
				);

		$template = $reject ? 'team.rejected' : 'team.approved';

		// Load site language file
		EB::loadLanguages();

		$subject = $reject ? JText::sprintf('COM_EASYBLOG_TEAMBLOGS_REQUEST_REJECTED', $team->title) : JText::sprintf('COM_EASYBLOG_TEAMBLOGS_REQUEST_APPROVED', $team->title);

		$obj = new stdClass();
		$obj->unsubscribe = false;
		$obj->email = $requester->email;

		$emails = array($obj);

		$notification->send($emails, $subject, $template, $data);
	}
}
