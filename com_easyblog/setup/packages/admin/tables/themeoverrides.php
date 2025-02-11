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

class EasyBlogTableThemeOverrides extends EasyBlogTable
{
	public $id = null;
	public $file_id = null;
	public $notes = null;
	public $contents = null;

	public function __construct($db)
	{
		parent::__construct('#__easyblog_themes_overrides', 'id', $db);
	}
}
