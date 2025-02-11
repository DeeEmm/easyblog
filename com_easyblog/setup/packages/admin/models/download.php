<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelDownload extends EasyBlogAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.download.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);	
	}

	/**
	 * Method to retrieve records pending for cron processing.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function getCronDownloadReq($max = 5)
	{
		$db = EB::db();

		$query = "select * from `#__easyblog_download`";
		$query .= " where `state` IN (" . $db->Quote(EASYBLOG_DOWNLOAD_REQ_NEW) . ',' . $db->Quote(EASYBLOG_DOWNLOAD_REQ_PROCESS) . ")";
		$query .= " order by `id`";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}


	/**
	 * Method to retrieve records pending for cron processing.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function getExpiredRequest($max = 10)
	{
		$db = EB::db();
		$config = EB::config();

		$days = $config->get('gdpr_archive_expiry', 14);
		$now = EB::date()->toSql();

		$query = "select a.* from `#__easyblog_download` as a";
		$query .= " where a.`state` = " . $db->Quote(EASYBLOG_DOWNLOAD_REQ_READY);
		$query .= " and a.`created` <= DATE_SUB(" . $db->Quote($now) . ", INTERVAL " . $days . " DAY)";
		$query .= " order by `id`";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Retrieves download requests
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function getRequests($options = array())
	{
		$db = EB::db();
		$query = array();

		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_download');

		$query = implode(' ', $query);

		$countQuery = str_replace('SELECT * FROM', 'SELECT COUNT(1) FROM', $query);
		$db->setQuery($countQuery);
		$this->_total = $db->loadResult();

		// Get the list of users
		$db->setQuery($query);
		$this->_data = $db->loadObjectList();
		$rows = $this->_data;

		if (!$rows) {
			return array();
		}

		$requests = array();

		foreach ($rows as $row) {
			$request = EB::table('Download');
			$request->bind($row);

			$requests[] = $request;
		}

		return $requests;
	}

	/**
	 * Method to get the total of user download data
	 *
	 * @since  5.2.6
	 * @access public
	 */
	public function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get a pagination object for user download data
	 *
	 * @since  5.2.6
	 * @access public
	 */
	public function getPagination()
	{
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');			
			$this->_pagination = EB::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Removes all download requests and delete the files
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function purgeRequests()
	{
		$db = EB::db();
		
		$query = 'DELETE FROM ' . $db->qn('#__easyblog_download');

		$db->setQuery($query);
		$db->query();

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$folders = JFolder::folders(EBLOG_GDPR_DOWNLOADS, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}
		}

		$files = JFolder::files(EBLOG_GDPR_DOWNLOADS, '.', false, true);

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		return true;
	}	
}
