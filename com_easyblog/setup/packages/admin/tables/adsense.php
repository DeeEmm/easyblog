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

class EasyBlogTableAdsense extends EasyBlogTable
{
	public $id = null;
	public $user_id	= null;
	public $code = null;
	public $published = null;
	public $display = null;

	public function __construct(&$db)
	{
		parent::__construct( '#__easyblog_adsense' , 'id' , $db );
	}

	function load($id = null, $reset = true)
	{
		$db		= $this->getDBO();

		$query  = 'select `id` FROM ' . $db->nameQuote( $this->_tbl );
		$query  .= ' where user_id = ' . $db->Quote( $id );

		$db->setQuery( $query );

		$result = $db->loadResult();

		if(empty($result))
		{
			$this->user_id  = $id;
			return $this;
		}
		else
		{
			return parent::load($result, $reset);
		}

	}

	function store($updateNulls = false)
	{
		$db		= $this->getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE user_id=' . $db->Quote( $this->user_id );

		$db->setQuery( $query );

		if( $db->loadResult() )
		{
			return $db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}
		return $db->insertObject( $this->_tbl, $this, $this->_tbl_key );
	}
}
