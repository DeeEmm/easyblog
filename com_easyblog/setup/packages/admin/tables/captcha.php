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

class EasyBlogTableCaptcha extends EasyBlogTable
{
	var $id = null;
	var $response = null;
	var $created = null;

	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_captcha' , 'id' , $db );
	}

	/**
	 * Verify response
	 * @param	$response	The response code given.
	 * @return	boolean		True on success, false otherwise.
	 **/
	public function verify( $response )
	{
		return $this->response == $response;
	}


	/**
	 * Delete the outdated entries.
	 */
	public function clear()
	{
	    $db = EB::db();
	    $date 	= EB::date();

	    $query  = 'DELETE FROM `#__easyblog_captcha` WHERE `created` <= DATE_SUB( ' . $db->Quote($date->toSql()) . ', INTERVAL 12 HOUR )';
	    $db->setQuery($query);
	    $db->query();

	    return true;
	}
}
