<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class modEasyBlogMapLocation
{
	public $latitude;
	public $longitude;
	public $address;
	public $title;
	public $content;
	public $ratingid;

	public function __construct($item)
	{
		$this->id = $item->id;
		$this->latitude = $item->latitude;
		$this->longitude = $item->longitude;
		$this->address = $item->address;
		$this->title = $item->title;
		$this->content = $item->title;
		if (isset($item->html)) {
			$this->content = $item->html;
		}

		// stores rating id in an array (custom hack when same location is loaded multiple times, rating form doesn't not get parsed properly)
		$this->ratingid = array($item ->id);
	}
}
