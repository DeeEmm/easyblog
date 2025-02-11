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

class EasyBlogModelPostReject extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Clear any existing post reject messages
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function clear($postId)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_post_rejected') . ' WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($postId);

		$db->setQuery($query);
		return $db->Query();
	}
	
	/**
	 * Clear any existing post reject messages
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isRejected($postId)
	{
		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post_rejected') . ' WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($postId);

		$db->setQuery($query);
		return $db->loadObject();
	}	
}
