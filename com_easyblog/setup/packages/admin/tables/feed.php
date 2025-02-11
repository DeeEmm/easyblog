<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogTableFeed extends EasyBlogTable
{
	public $id = null;
	public $title = '';
	public $url = '';
	public $interval = 5;
	public $cron = true;
	public $item_creator = null;
	public $item_team = null;
	public $item_category = null;
	public $item_frontpage = true;
	public $item_published = 1;
	public $item_get_fulltext = false;
	public $language = null;
	public $item_content = null;
	public $author = null;
	public $params = null;
	public $published = true;
	public $created	= null;
	public $last_import	= null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_feeds', 'id', $db);

		$this->last_import = '0000-00-00 00:00:00';
	}

	public function store($updateNulls = false)
	{
		if (!$this->created) {
			$this->created = EB::date()->toSql();
		}

		return parent::store($updateNulls);
	}

	public function getCategoryName()
	{
		$db = EB::db();

		if (!empty($this->item_category)) {
			$query  = 'SELECT `title` FROM `#__easyblog_category` WHERE `id` = ' . $db->Quote( $this->item_category );
			$db->setQuery( $query );
			return $db->loadResult();
		}

		return '';
	}
}
