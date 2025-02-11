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

class EasyBlogModelXmlrpc extends EasyBlogAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the category object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategory($title, $userId)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_category');
		$query[] = 'WHERE ' . $db->quoteName('title') . '=' . $db->Quote($title);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObject();

		$category = EB::table('Category');

		// If it doesn't exist, create it now
		if (!$result) {
			$category->title = $title;
			$category->created_by = $userId;

			$category->store();

			return $category;
		}

		$category->bind($result);

		return $category;
	}


	/**
	 * Checks if the given category title exists on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function categoryExists($title)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_category');
		$query[] = 'WHERE ' . $db->quoteName('title') . '=' . $db->Quote($title);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Retrieves the default category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultCategory()
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_category');
		$query[] = 'WHERE ' . $db->quoteName('default') . '=' . $db->Quote(1);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$category = $db->loadObject();

		return $category;
	}

	/**
	 * Retrieves a list of tags on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getTags()
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_tag');
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}

		$tags = array();

		foreach ($result as $row) {
			$tag = EB::table('Tag');
			$tag->bind($row);

			$tags[] = $tag;
		}

		return $tags;
	}

	/**
	 * Retrieves a list of categories on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCategories()
	{
		$db = EB::db();

		$query = array();


		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_category');
		$query[] = 'WHERE ' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'ORDER BY ' . $db->quoteName('title') . ' ASC';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$categories = array();

		foreach ($result as $row) {
			$category = EB::table('Category');
			$category->bind($row);

			$categories[] = $category;
		}

		return $categories;
	}

	/**
	 * Retrieves the recent posts created by the specified author
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getRecentPosts($authorId = null)
	{
		// Get the author id
		$authorId = JFactory::getUser($authorId)->id;

		$db = EB::db();

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_post');
		$query[] = 'WHERE ' . $db->qn('created_by') . '=' . $db->Quote($authorId);
		$query[] = 'AND ' . $db->qn('published') . '!=' . $db->Quote(EASYBLOG_POST_BLANK);
		$query[] = 'AND ' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'ORDER BY ' . $db->qn('created') . ' DESC';


		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$posts = array();

		foreach ($result as $row) {

			$post = EB::post($row->id);
			// $post->bind($row);

			$posts[] = $post;
		}

		return $posts;
	}
}
