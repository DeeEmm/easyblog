<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EBHelperNavigation extends EasyBlog
{
	public $acl;

	public function __construct()
	{
		$this->acl = EB::acl();

		parent::__construct();
	}

	public function getPendingPosts()
	{
		$pending = EB::model('pending');

		return $pending->getTotal($this->my->id);
	}

	public function isAllowedManage()
	{
		$allowManage = false;

		$permissions = array(
			'add_entry',
			'moderate_entry',
			'manage_comment',
			'create_category',
			'create_tag',
			'create_team_blog',
			);

		foreach ($permissions as $permission) {
			if ($this->acl->get($permission)) {
				$allowManage = true;
				break;
			}
		}

		return $allowManage;
	}

	public function getPendingComments()
	{
		$comments = EB::model('Comments');
		
		return $comments->getTotalPending();
	}

	public function getTeamRequests()
	{
		$team = EB::model('TeamBlogs');
		
		return $team->getTotalRequest();
	}

	/* Load up the subscription record for the current user.
	 *
	 */
	public function getSubscriptions()
	{
		$subscription = EB::table('Subscriptions');

		if (!$this->my->guest) {
			$subscription->load(array('email' => $this->my->email, 'utype' => 'site'));
		}

		return $subscription;
	}
}