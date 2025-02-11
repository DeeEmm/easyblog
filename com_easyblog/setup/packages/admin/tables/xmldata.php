<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableXmldata extends EasyBlogTable
{
	public $id = null;
	public $session_id = null;
	public $filename = null;
	public $post_id = null;
	public $source = null;
	public $data = null;
	public $comments = null;

	public function __construct($db)
	{
		parent::__construct( '#__easyblog_xml_wpdata' , 'id' , $db );
	}
}
