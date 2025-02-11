<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableConfigs extends EasyBlogTable
{
	public $name = null;
	public $params = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_configs', 'name', $db);
	}

	public function store($key = 'config')
	{
		$db = EB::db();

		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__easyblog_configs') . ' '
				. 'WHERE ' . $db->nameQuote('name') . '=' . $db->Quote($key);
		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;


		$data = new stdClass();
		$data->name = empty($this->name) ? $key : $this->name ;
		$data->params = trim($this->params);

		if ($exists) {
			return $db->updateObject('#__easyblog_configs', $data, 'name');
		}

		return $db->insertObject( '#__easyblog_configs' , $data );
	}
}
