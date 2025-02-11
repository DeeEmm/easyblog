<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelFeatured extends EasyBlogAdminModel
{
	private $data = null;
	private $total = null;
	private $pagination = null;

	public function __construct()
	{
		parent::__construct();

		$limit = EB::getLimit();
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}


	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();

		$db = EB::db();

		$query = 'SELECT a.* FROM `#__easyblog_post` AS a';
		$query .= ' INNER JOIN `#__easyblog_featured` AS c';
		$query .= ' 	ON a.`id` = c.`content_id` AND c.`type` = ' . $db->Quote('post');
		$query .= $where;
		$query .= $orderby;

		return $query;
	}

	public function _buildQueryLanguage()
	{
		$mainframe = JFactory::getApplication();
		$my = JFactory::getUser();
		$db = EB::db();

		$languageQ = '';

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$languageQ	.= EBR::getLanguageQuery('AND', 'a.language');
		}

		return $languageQ;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$my = JFactory::getUser();
		$db = EB::db();
		$config = EB::config();

		$languageQ  = $this->_buildQueryLanguage();

		$where = array();

		$where[] = ' a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$where[] = ' a.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		if ($my->id == 0) {
			$where[] = ' a.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccessSQL = $catLib->genAccessSQL('a.`id`');
			$where[] = ' (' . $catAccessSQL . ')';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$where .= $languageQ;

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$orderby = ' ORDER BY a.`created` DESC';
		return $orderby;
	}

	public function getPosts($options = array())
	{
		$limit = isset($options['limit']) ? $options['limit'] : $this->getState('limit', 0);
		$limitstart = $this->getState('limitstart', 0);

		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			$db = EB::db();

			$query .= ' LIMIT ' . $limitstart . ', ' . $limit;
			$db->setQuery($query);

			$this->_data = $db->loadObjectList();


			// $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Determines if an object is featured on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isFeatured($type, $id)
	{
		static $objects = array();

		$key = $type . '.' . $id;

		if (!isset($objects[$key])) {

			$db = EB::db();

			$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_featured');
			$query .= ' WHERE ' . $db->quoteName('content_id') . '=' . $db->Quote($id);
			$query .= ' AND ' . $db->quoteName('type') . '=' . $db->Quote($type);

			$db->setQuery($query);

			$result = $db->loadResult();

			$objects[$key] = $result > 0;
		}

		return $objects[$key];
	}

	/**
	 * Removes an object from being featured
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function removeFeatured($type, $contentId)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->qn('#__easyblog_featured');
		$query[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($contentId);
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote($type);

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Determines if a featured item already exists on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function exists($type, $contentId)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_featured');
		$query[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($contentId);
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote($type);

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Get the featured id
		$exists = $db->loadResult() > 0;

		return $exists;
	}

	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function preload($postIds = array())
	{
		if (!$postIds) {
			return $postIds;
		}

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_featured');
		$query[] = 'WHERE ' . $db->qn('content_id') . ' IN(' . implode(',', $postIds) . ')';
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('post');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$featuredItems = array();

		// Initialize the values first
		foreach ($postIds as $id) {
			$featuredItems[$id] = false;
		}

		if ($result) {
			foreach ($result as $item) {
				$featuredItems[$item->content_id] = true;
			}
		}

		return $featuredItems;
	}

	/**
	 * Makes an object a featured item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function makeFeatured($type, $contentId)
	{
		$db = EB::db();
		$date = EB::date();

		// If it has already been featured previously, we shouldn't be featuring it again
		$exists = $this->exists($type, $contentId);

		if ($exists) {
			return false;
		}

		$featured = EB::table('Featured');
		$featured->content_id = $contentId;
		$featured->type = $type;

		// Store the item
		$state = $featured->store();

		if (!$state) {
			$this->setError($featured->getError());

			return false;
		}

		return true;
	}
}
