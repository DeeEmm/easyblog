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

class EasyBlogGdprTemplate extends JObject
{
	public $id = null;
	public $type = null;
	public $preview = null;
	public $content = null;
	public $link = null;
	public $created = null;
	public $media = null;

	public function __construct()
	{
		$this->link = '';
		$this->content = '';
		$this->preview = '';
	}

}
