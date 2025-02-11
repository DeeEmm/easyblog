<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableTwitterMicroblog extends EasyBlogTable
{
	public $id_str = null;
	public $oauth_id = null;
	public $post_id	= null;
	public $created	= null;
	public $tweet_author = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_twitter_microblog', 'id_str', $db);
	}

	public function store( $updateNulls = false )
	{
		$db = EB::db();
		$query = 'SELECT COUNT(1) FROM ' . $db->qn($this->_tbl) . ' '
				. 'WHERE `id_str`=' . $db->Quote( $this->id_str );
		$db->setQuery( $query );

		if( $db->loadResult() )
		{
			return $db->updateObject( $this->_tbl, $this, $this->_tbl_key );
		}
		return $db->insertObject( $this->_tbl, $this, $this->_tbl_key );
	}
	
	/**
	 * Loads a micro posting from twitter given the post id
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function loadByPostId($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn($this->_tbl);
		$query[] = 'WHERE ' . $db->qn('post_id') . '=' . $db->Quote($id);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObject();

		$state = parent::bind($result);

		return $state;
	}
}
