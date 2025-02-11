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

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableFeedburner extends EasyBlogTable
{
	public $id = null;
	public $userid = null;
	public $url = '';

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_feedburner' , 'id' , $db);
	}

	public function load($id = null, $reset = true)
	{
		$db = $this->getDBO();

		$query = 'select `id` FROM ' . $db->nameQuote( $this->_tbl );
		$query .= ' where userid = ' . $db->Quote( $id );

		$db->setQuery($query);
		$result = $db->loadResult();

		if (empty($result)) {
			$this->userid  = $id;
			return $this;
		}

		return parent::load($result);
	}

	public function store($updateNulls = false)
	{
		$db = $this->getDBO();
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote($this->_tbl) . ' '
				. 'WHERE `userid`=' . $db->Quote($this->userid);
		$db->setQuery($query);

		if ($db->loadResult()) {
			return $db->updateObject($this->_tbl, $this, $this->_tbl_key);
		}

		$obj = new stdClass();
		$obj->userid = $this->userid;
		$obj->url = $this->url;

		return $db->insertObject($this->_tbl, $obj, $this->_tbl_key);
	}
}