<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogContributorEasySocialGroup extends EasyBlogContributorAbstract
{
	public $group = null;

	public function __construct($id)
	{
		parent::__construct($id, 'group');

		if (!EB::easysocial()->exists()) {
			return;
		}

		$this->group = ES::group($id);
	}

	public function getHeader()
	{
		$output = EB::easysocial()->renderMiniHeader($this->group);

		// $output .= '<button class="btn btn-default">← Back</button>';
		echo $output;
		return $output;
	}

	public function getAvatar()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->group->getAvatar();
	}

	public function getTitle()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->group->getName();
	}

	public function getPermalink()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->group->getPermalink();
	}

	public function canDelete()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		$allowed = $this->group->isAdmin();

		return $allowed;
	}

	public function canView()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		$canView = $this->group->canViewItem();

		return $canView;
	}

	public function canCreatePost()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		if (!$this->canView()) {
			return false;
		}

		// Only available when user is part of the cluster
		if (!$this->group->isAdmin() && !$this->group->isMember() && !FH::isSiteAdmin()) {
			return false;
		}

		$params = $this->group->getParams();

		// Check for blog creation permission
		if (!$this->group->isOwner() && !FH::isSiteAdmin()) {
			$allowed = $params->get('blogcreate', null);

			if (!is_null($allowed)) {
				$allowed = ES::makeArray($allowed);
				$isAllowed = false;

				if (in_array('admin', $allowed) && $this->group->isAdmin()) {
					$isAllowed = true;
				}

				if (in_array('member', $allowed) && $this->group->isMember()) {
					$isAllowed = true;
				}

				return $isAllowed;
			}
		}

		return true;
	}
}
