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

class EasyBlogModelModerate extends EasyBlogAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		//get the number of events from database
		$limit = $mainframe->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart	= $this->input->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Removes any pending messages for post rejects
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function clearPendingMessages($id)
	{
		$db = EB::db();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__easyblog_post_rejected' ) . ' WHERE '
				. $db->quoteName('post_id') . '=' . $db->Quote($draft_id);

		$db->setQuery($query);
		return $db->Query();
	}
}
