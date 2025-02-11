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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'table.php' );

class EasyBlogTableParams extends EasyBlogTable
{
	/*
	 * The id of the user
	 * @var int
	 */
	var $id = null;

	/*
	 * refer id
	 * @var string
	 */
	var $refer_id = null;

	/*
	 * param name
	 * @var string
	 */
	var $param_name = null;

	/*
	 * param value
	 * @var string
	 */
	var $param_value = null;

	/*
	 * param value type
	 * @var string
	 */
	var $param_value_type = null;

	/*
	 * param type.
	 * @var string
	 */
	var $param_type = null;

	/*
	 * Date when this apps record is created.
	 * @var string
	 */
	var $created = null;

	/*
	 * Date when this apps record is modified.
	 * @var string
	 */
	var $modified = null;

	/*
	 * publish
	 * @var object
	 */
	var $published = null;

	/*
	 * order
	 * @var object
	 */
	var $ordering = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db ){
		parent::__construct( '#__easyblog_users' , 'id' , $db );
	}

	/**
	 * override load method.
	 * if user record not found in eblog_profile, create one record.
	 *
	 */
   	function load($id){
		if((! parent::load($id)) && ($id != 0)){
			$db	= $this->getDBO();

			$obj					= new stdClass();
			$obj->refer_id			= '';
			$obj->param_name		= '';
			$obj->param_value 		= '';
			$obj->param_value_type	= '';
			$obj->params			= '';
			$obj->param_type		= '';
			$obj->created			= '';
			$obj->modified			= '';
			$obj->published			= '';
			$obj->ordering			= '';

			$db->insertObject('#__easyblog_users', $obj);
		}

		return parent::load($id);
   	}
}
