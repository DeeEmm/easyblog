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

abstract class EasyBlogContributorAbstract extends EasyBlog
{
	public $id = null;
	public $type = null;
	
	public function __construct($id, $type)
	{
		$this->id = $id;
		$this->type = $type;
	}

	public function getHeader()
	{
		return;
	}

	public abstract function getAvatar();

	public abstract function getTitle();

	public abstract function getPermalink();
}