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

class EasyBlogContributorEasySocialEvent extends EasyBlogContributorAbstract
{
	public $event = null;

	public function __construct($id, $type)
	{
		parent::__construct($id, 'event');

		if (!EB::easysocial()->exists()) {
			return;
		}

		$this->event = FD::event($id);
	}

	public function getHeader()
	{
		$output = EB::easysocial()->renderMiniHeader($this->event);
		echo $output;

		return $output;
	}

	public function getAvatar()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->event->getAvatar();
	}

	public function getTitle()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->event->getName();
	}

	public function getPermalink()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->event->getPermalink();
	}

	public function canDelete()
	{
		$allowed = $this->event->isAdmin();

		return $allowed;
	}

	public function canView()
	{
		$canView = $this->event->canViewItem();

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
		if (!$this->event->isAdmin() && !$this->event->isMember() && !FH::isSiteAdmin()) {
			return false;
		}

		$params = $this->event->getParams();

		// Check for blog creation permission
		if (!$this->event->isOwner() && !FH::isSiteAdmin()) {
			$allowed = $params->get('blogcreate', null);

			if (!is_null($allowed)) {
				$allowed = ES::makeArray($allowed);
				$isAllowed = false;

				if (in_array('admin', $allowed) && $this->event->isAdmin()) {
					$isAllowed = true;
				}

				if (in_array('member', $allowed) && $this->event->isMember()) {
					$isAllowed = true;
				}

				return $isAllowed;
			}
		}

		return true;
	}
}
