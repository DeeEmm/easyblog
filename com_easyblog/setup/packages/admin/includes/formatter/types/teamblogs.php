<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogFormatterTeamblogs extends EasyBlogFormatterStandard
{
	public function execute()
	{
		if (!$this->items) {
			return $this->items;
		}

		// cache teamblogs
		EB::cache()->insertTeams($this->items);


		$teams = array();

		// Load up the blogs model
		$model = EB::model('TeamBlogs');

		// Get the current user's group id's
		$gid = EB::getUserGids();

		foreach ($this->items as $item) {

			$team = EB::table('TeamBlog');
			$team->load($item->id);

			// Check if the logged in user is a member of the group
			$team->isMember = $team->isMember($this->my->id, $gid);
			$team->isActualMember = $team->isMember($this->my->id, $gid, false);

			$team->members = $model->getAllMembers($team->id, 5);

			// total member count ( including actual members and users from asociated joomla group.)
			$team->memberCount = $team->getAllMembersCount();

			// post count associated with this teamblog.
			$team->postCount = $team->getPostCount();

			// Get the list of blog posts form this team
			$blogs = array();

			if ($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || FH::isSiteAdmin()) {
				$blogs = $model->getPosts($team->id, EASYBLOG_TEAMBLOG_LISTING_NO_POST);
				$blogs = EB::formatter('list', $blogs);
			}

			$team->blogs = $blogs;

			// Get the list of tags
			// $team->tags = $team->getTags();

			// Get categories used in this team
			// $team->categories = $team->getCategories();

			// Determines if the team is featured
			if (isset($item->isfeatured)) {
				$team->isFeatured = $item->isfeatured;
			} else {
				$team->isFeatured = EB::isFeatured('teamblog', $team->id);
			}

			// check if team description is emtpy or not. if yes, show default message.
			if (empty($team->description)) {
				$team->description = JText::_('COM_EASYBLOG_TEAMBLOG_NO_DESCRIPTION');
			}

			// Determines if the viewer is subscribed to this team
			$team->isTeamSubscribed = $model->isTeamSubscribedEmail($team->id, $this->my->email);

			// If the user is subscribed, we need to get his subscription id
			$team->subscription_id = $team->isTeamSubscribed;
			
			$teams[] = $team;
		}

		return $teams;
	}
}
