<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableApps extends EasyBlogTable
{
	var $id          = null;

	/*
	 * The apps name
	 * @var string
	 */
	var $appname    = null;

	/*
	 * Apps description / summary
	 * @var string
	 */
	var $description = null;

	/*
	 * Date when this apps record is created.
	 * @var string
	 */
	var $created         = null;

	/*
	 * Date when this apps record is modified.
	 * @var string
	 */
	var $modified      = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db ){
		parent::__construct( '#__easyblog_apps' , 'appname' , $db );
	}

	/**
	 * override load method.
	 * if user record not found in eblog_profile, create one record.
	 *
	 */
   	function load($appname){
		if((! parent::load($appname)) && (!empty($appname))){
			$db	= $this->getDBO();

			$obj				= new stdClass();
			$obj->appname		= $appname;
			$obj->description	= '';
			$obj->created 		= '';
			$obj->modified		= '';

			$db->insertObject('#__easyblog_apps', $obj);
		}

		return parent::load($appname);
   	}
}
