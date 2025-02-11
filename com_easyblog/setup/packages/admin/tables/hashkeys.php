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

class EasyBlogTableHashKeys extends EasyBlogTable
{
	public $id = null;
	public $uid = null;
	public $type = null;
	public $key = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_hashkeys' , 'id' , $db);
	}

	public function load( $uid = null , $type = '' )
	{
		$db		= EB::db();
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) .  ' WHERE '
				. $db->nameQuote( 'uid' ) . '=' . $db->Quote( $uid ) . ' AND '
				. $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
		$db->setQuery( $query );

		$data	= $db->loadObject();

		return parent::bind( $data );
	}

	public function loadByKey( $key )
	{
		$db		= EB::db();
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) .  ' WHERE '
				. $db->nameQuote( 'key' ) . '=' . $db->Quote( $key );
		$db->setQuery( $query );

		$data	= $db->loadObject();

		return parent::bind( $data );
	}

	public function store($updateNulls = false)
	{
		if( empty($this->key ) )
		{
			$this->key	= $this->generate();
		}
		return parent::store($updateNulls);
	}

	/**
	 * Verify response
	 * @param	$response	The response code given.
	 * @return	boolean		True on success, false otherwise.
	 **/
	function verify($response)
	{
	}

	/*
	 * Generates a hashkey
	 *
	 * @param	null
	 * @return	string	Returns an md5 generated key.
	 */
	public function generate()
	{
		return EBString::substr(md5($this->uid . $this->type . EB::date()->toSql()), 0 , 12);
	}
}
