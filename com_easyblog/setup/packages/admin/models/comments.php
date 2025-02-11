<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyBlogModelComments extends EasyBlogAdminModel
{
	public $total = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.comments.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.comments.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Populate current stats
	 *
	 * @since	5.1
	 * @access	public
	 */
	protected function populateState()
	{
		parent::populateState();
	}

	/**
	 * Retrieve pagination for comments. Used at the back end only
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPagination()
	{
		$pagination = EB::Pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));
		return $pagination;
	}

	/**
	 * Retrieves comments from the back end
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getData()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.*, b.' . $db->qn('title') . ' AS ' . $db->qn('blog_name');
		$query[] = 'FROM ' . $db->qn('#__easyblog_comment') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_post') . ' AS b';
		$query[] = 'ON a.' . $db->qn('post_id') . ' = b.' . $db->qn('id');
		$query[] = 'WHERE 1';

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.comments.filter_state', 'filter_state', '', 'word');

		if ($filter_state) {
			if ($filter_state == 'P') {
				$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote('1');
			}

			if ($filter_state == 'U') {
				$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote('0');
			}

			if ($filter_state == 'M') {
				$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote('2');
			}
		}

		$search = $this->app->getUserStateFromRequest('com_easyblog.comments.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));

		if ($search) {
			$query[] = 'AND (';
			$query[] = 'LOWER (a.' . $db->qn('title') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$query[] = 'OR';
			$query[] = 'LOWER (b.' . $db->qn('title') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$query[] = 'OR';
			$query[] = 'LOWER (a.' . $db->qn('comment') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$query[] = ')';
		}

		$ordering = $this->app->getUserStateFromRequest('com_easyblog.comments.filter_order', 'filter_order', 'a.created', 'cmd');
		$orderingDirection = $this->app->getUserStateFromRequest('com_easyblog.comments.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		$query[] = 'ORDER BY ' . $ordering . ' ' . $orderingDirection;

		// First we get the total number of records before pagination
		$queryLimit = $query;

		array_shift($queryLimit);

		$queryLimit = 'SELECT COUNT(1) ' . implode(' ', $queryLimit);
		$db->setQuery($queryLimit);

		$this->total = (int) $db->loadResult();

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		// Reset the limitstart (perhaps caused by other filters)
		if ($this->total <= $limitstart) {
			$limitstart = 0;
			$this->setState('limitstart', 0);
		}

		$query = implode(' ', $query);

		if ($limit) {
			$query .= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$db->setQuery($query);
		$data = $db->loadObjectList();


		return $data;
	}


	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	public function publish( &$pks , $publish = 1 )
	{
		if( count( $pks ) > 0 )
		{
			$db		= EB::db();

			$tags	= implode( ',' , $pks );

			$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_comment' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $tags . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Get the total number of comments that are awaiting moderation
	 **/
	public function getTotalPending()
	{
		$db 	= EB::db();
		$query 	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_comment' );
		$query	.= ' WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( EBLOG_COMMENT_MODERATE );
		$db->setQuery( $query );
		$total	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieve a list of top commenters for author's posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTopCommentersForAuthorsPost($authorId = null, $limit = 5)
	{
		$db = EB::db();
		$user = JFactory::getUser($authorId);

		$query = array();

		$query[] = 'SELECT a.' . $db->quoteName('created_by') . ', COUNT(a.' . $db->quoteName('id') . ') AS ' . $db->quoteName('total') . ' FROM ' . $db->quoteName('#__easyblog_comment') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_post') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('post_id') . ' = b.' . $db->quoteName('id');
		$query[] = 'WHERE b.' . $db->quoteName('created_by') . '=' . $db->Quote($user->id);
		$query[] = 'AND a.' . $db->quoteName('created_by') . '!=' . $db->Quote($user->id);
		$query[] = 'AND a.' . $db->quoteName('created_by') . '!=' . $db->Quote(0);
		$query[] = 'AND a.' . $db->quoteName('published') . '=' . $db->Quote(1);

		$query[] = 'AND b.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND b.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query[] = 'GROUP BY a.' . $db->quoteName('created_by');
		$query[] = 'ORDER BY ' . $db->quoteName('total') . ' DESC';
		$query[] = 'LIMIT 0,' . (int) $limit;

		$query = implode(' ', $query);

		// echo str_ireplace('#__', 'jos_', $query);
		// exit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		//preload users
		$ids = array();
		foreach ($result as $item) {
			$ids[] = $item->created_by;
		}

		EB::user($ids);

		foreach ($result as &$row) {
			$row->author = EB::user($row->created_by);
		}

		return $result;
	}

	/**
	 * Get a list of recent comments posted on the author's post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRecentCommentsOnAuthor($authorId = null, $limit = 5)
	{
		$db = EB::db();
		$user = JFactory::getUser($authorId);

		$query = array();

		$query[] = 'SELECT b.* FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->quoteName('#__easyblog_comment') . ' AS b';
		$query[] = 'ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');
		$query[] = 'WHERE a.' . $db->quoteName('created_by') . '=' . $db->Quote($user->id);
		$query[] = 'AND a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'AND b.' . $db->quoteName('published') . '=' . $db->Quote(1);
		$query[] = 'ORDER BY b.' . $db->quoteName('created') . ' DESC';
		$query[] = 'LIMIT 0,' . (int) $limit;

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		// Format the comments
		$result = EB::comment()->format($result);

		$comments = array();

		foreach ($result as $row) {

			$comment = EB::table('Comment');
			$comment->bind($row);

			$comments[] = $comment;
		}

		return $comments;
	}

	/**
	 * Delete comments from particular post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deletePostComments($postId)
	{
		$db = EB::db();
		$config	= EB::getConfig();

		// if komento exist and check the integration option
		$komentoEngine = JPATH_ROOT . '/components/com_komento/helpers/helper.php';

		if (JFile::exists($komentoEngine) && $config->get('comment_komento') == true) {

			require_once($komentoEngine);
			$model = Komento::getModel('comments');

			// delete comment based on the article id
			$model->deleteArticleComments('com_easyblog', $postId);
		}

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_comment');
		$query[] = 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($postId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Get user's comments used by GDPR processing.
	 * @since 5.1
	 * @access public
	 */
	public function getGDPRComments($userId)
	{
		$db = EB::db();

		$query = 'SELECT a.* FROM ' . $db->quoteName('#__easyblog_comment') . ' AS a';
		$query .= ' WHERE a.' . $db->quoteName('created_by') . '=' . $db->Quote($userId);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Delete comments from particular user
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function deleteUserComments($key, $type = 'created_by')
	{
		$db = EB::db();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_comment');
		$query[] = 'WHERE ' . $db->quoteName($type) . '=' . $db->Quote($key);

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}



}
