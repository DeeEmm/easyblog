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

require_once(__DIR__ . '/abstract.php');

class EasyBlogContributorTeamBlog extends EasyBlogContributorAbstract
{
	public $team = null;

	public function __construct($id, $type)
	{
		parent::__construct($id, $type);

		$team = EB::table('TeamBlog');
		$team->load($id);

		$this->team = $team;
	}
	
	public function getHeader()
	{
		return;
	}

	public function getAvatar()
	{
		return $this->team->getAvatar();
	}

	public function getTitle()
	{
		return $this->team->getTitle();
	}

	public function getPermalink()
	{
		return $this->team->getPermalink();
	}

	public function canView()
	{
		// Post library already handle this
		return true;
	}
}