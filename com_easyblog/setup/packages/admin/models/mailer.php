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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelMailer extends EasyBlogAdminModel
{
	/**
	 * Clean up sent emails
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cleanup()
	{
		$db = EB::db();

		$query = array();

		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_mailq');
		$query[] = 'WHERE ' . $db->quoteName('status') . '=' . $db->Quote(1);
		$query[] = 'AND DATEDIFF(NOW(), ' . $db->quoteName('created') . ') >=' . $db->Quote(7);

		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Retrieves a list of emails that needs to be dispatched
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPendingEmails($limit)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName('#__easyblog_mailq');
		$query[] = 'WHERE ' . $db->quoteName('status') . '=' . $db->Quote(0);

		// we need to order by id so that the same date value will not messed up the sorting. #1047
		$query[] = 'ORDER BY ' . $db->quoteName('id') . ' ASC';
		$query[] = 'LIMIT ' . $limit;

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result	= $db->loadObjectList();

		return $result;
	}
}
