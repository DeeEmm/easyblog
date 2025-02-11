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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'table.php' );

class EasyBlogTableMigrate extends EasyBlogTable
{
	/*
	 * The id of the comment
	 * @var int
	 */
	var $id 						= null;

	/*
	 * The id of the joomla article from jos_content
	 * @var int
	 */
	var $content_id					= null;

	/*
	 * The id of the new blog
	 * @var int
	 */
	var $post_id					= null;

	/*
	 * The session id of the migration process
	 * @var varchar
	 */
	var $session_id					= null;


	/*
	 * The component we migrate from
	 * @var varchar
	 */
	var $component					= null;

	/*
	 * The file we migrate from when xml
	 * @var varchar
	 */
	var $filename					= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_migrate_content' , 'id' , $db );
	}

//    	function load($id){
// 		return parent::load($id);
//    	}


}
