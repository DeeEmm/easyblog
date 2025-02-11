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

class EasyBlogModelComment extends EasyBlogAdminModel
{
	protected $_total = null;
	protected $_pagination = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * Method to get total comment post currently iregardless the status and associated blogs.
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalComment( $userId = 0 )
	{
		$db			= EB::db();
		$config		= EB::getConfig();

		if ($config->get('comment_compojoom')) {
			$file	= JPATH_ROOT . '/administrator/components/com_comment/plugin/com_easyblog/josc_com_easyblog.php';

			if (JFile::exists($file)) {
				require_once($file);

				$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__comment') . ' '
						. 'WHERE ' . $db->nameQuote('component') . ' = ' . $db->Quote('com_easyblog') . ' '
						. 'AND ' . $db->nameQuote('userid') . ' = ' . $db->Quote($userId) . ' '
						. 'AND ' . $db->nameQuote('published') . ' = ' . $db->Quote(1);
				$db->setQuery($query);
				return $db->loadResult();
			}
		}

		$where  = array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_comment');

		if (!empty($userId)) $where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra 		= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$query      = $query . $extra;

		$db->setQuery($query);

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Retrieves comments
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getComments($max = 0, $userId = 0, $sort = 'latest',  $base='comment', $search = '', $published = 'all', $limit = 0)
	{
		$limit			= ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart 	= $this->input->get( 'limitstart', $this->getState('limitstart'), 'int');
		$config	= EB::config();

		if ($config->get('comment_compojoom')) {

			$file = JPATH_ADMINISTRATOR . '/components/com_comment/plugin/com_easyblog/josc_com_easyblog.php';
			$exists = JFile::exists($file);

			if ($exists) {
				require_once($file);

				return $this->getCompojoomComment();
			}
		}

		$db	= EB::db();

		$queryPagination	= false;
		$queryLimit			= '';
		$queryOrder			= ' ORDER BY a.`created` DESC';
		$queryWhere			= '';

		switch($sort)
		{
			case 'latest' :
			default :
				$queryOrder			= ' ORDER BY a.`created` DESC';
				break;
		}

		if (!empty($userId)) {
			if ($base == 'comment') {
				$queryWhere	.= ' WHERE a.`created_by` = '. $db->Quote($userId);
			} else {
				$queryWhere	.= ' WHERE b.`created_by` = '. $db->Quote($userId);
			}
		}

		if ($published == 'published') {
			$queryWhere	.= ' AND a.`published` = '. $db->Quote('1');
		}

		if ($published == 'unpublished') {
			$queryWhere	.= ' AND a.`published` = '. $db->Quote('0');
		}

		if ($published == 'moderate') {
			$queryWhere	.= ' AND a.`published` = '. $db->Quote('2');
		}

		// post state and published
		$queryWhere	.= ' AND b.`published` = '. $db->Quote(EASYBLOG_POST_PUBLISHED);
		$queryWhere	.= ' AND b.`state` = '. $db->Quote(EASYBLOG_POST_NORMAL);

		if (!empty($search)) {
			$queryWhere .= (!empty($queryWhere)) ? ' AND' : ' WHERE';
			$queryWhere	.= ' ( a.`title` LIKE '.$db->Quote('%' . $search . '%') . ' OR ';
			$queryWhere	.= ' a.`comment` LIKE '.$db->Quote('%' . $search . '%' ) . ' OR ';
			$queryWhere .= ' b.`title` LIKE ' . $db->Quote( '%' . $search . '%' ) . ' )';
		}

		// exclude block users. #1978
		$queryWhere .= ' AND (uu.`block` = 0 OR uu.`id` IS NULL)';


		if ($max > 0) {
			$queryLimit	= ' LIMIT '.$max;
		} else {
			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		if ($queryPagination) {
			$query = 'SELECT COUNT(1)';
			$query .= ' FROM `#__easyblog_comment` AS a';
			$query .= ' INNER JOIN `#__easyblog_post` AS b ON a.`post_id` = b.`id`';

			// exclude block users. #1978
			$query .= ' LEFT JOIN `#__users` AS uu ON a.`created_by` = uu.`id` and uu.`block` = 0';
			$query .= $queryWhere;

			$db->setQuery( $query );
			$this->_total	= $db->loadResult();

			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination( $this->_total , $limitstart , $limit );
		}

		$query	= 'SELECT a.*, b.`created_by` AS `blog_owner`, b.`title` AS `blog_title`'
				. ' FROM `#__easyblog_comment` AS a'
				. ' INNER JOIN `#__easyblog_post` AS b ON a.`post_id` = b.`id`'
				. ' LEFT JOIN `#__users` AS uu ON a.`created_by` = uu.`id` and uu.`block` = 0'
				. $queryWhere
				. $queryOrder
				. $queryLimit;

		$db->setQuery($query);

		$result	= $db->loadObjectList();

		if ($db->getErrorNum() > 0) {
			throw EB::exception($db->getErrorMsg() . $db->stderr(), $db->getErrorNum());
		}

		return $result;

	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCompojoomComment($max = 0, $userId = 0, $sort = 'latest',  $base='comment', $search = '', $published = 'all')
	{
		$db	= EB::db();

		$queryPagination	= false;
		$queryLimit			= '';
		$queryOrder			= ' ORDER BY a.`date` DESC';
		$queryWhere			= '';

		switch($sort)
		{
			case 'latest' :
			default :
				$queryOrder			= ' ORDER BY a.`date` DESC';
				break;
		}

		if (! empty($userId)) {
			if ($base == 'comment') {
				$queryWhere	.= ' WHERE a.`userid` = '. $db->Quote($userId);
			} else {
				$queryWhere	.= ' WHERE b.`created_by` = '. $db->Quote($userId);
			}
		}

		switch ( $published )
		{
			case 'published':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('1');
				break;

			case 'unpublished':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('0');
				break;

			case 'moderate':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('2');
				break;

			case 'all':
			default:
				break;
		}


		if (!empty($search)) {
			$queryWhere .= (! empty($queryWhere)) ? ' AND' : ' WHERE';
			$queryWhere	.= ' a.`comment` LIKE '.$db->Quote('%' . $search . '%');

		}

		$queryWhere		.= ' AND a.`component` = ' . $db->quote('com_easyblog');

		if ($max > 0) {
			$queryLimit	= ' LIMIT '.$max;
		} else {
			$limit		= $this->getState('limit');
			$limitstart = $this->getState('limitstart');
			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		if ($queryPagination) {
			$query	= 'SELECT COUNT(1)';
			$query	.= ' FROM `#__comment` AS a INNER JOIN `#__easyblog_post` AS b';
			$query	.= ' ON a.`contentid` = b.`id`';
			$query	.= $queryWhere;

			$db->setQuery( $query );
			$this->_total	= $db->loadResult();

			jimport('joomla.html.pagination');
			$this->_pagination	= EB::pagination( $this->_total , $limitstart , $limit );
		}

		$query	= 'SELECT'
				. ' a.`id` AS `id`,'
				. ' a.`contentid` AS `post_id`,'
				. ' a.`comment` AS `comment`,'
				. ' a.`name` AS `name`,'
				. ' a.`title` AS `title`,'
				. ' a.`email` AS `email`,'
				. ' a.`website` AS `url`,'
				. ' a.`ip` AS `ip`,'
				. ' a.`userid` AS `created_by`,'
				. ' a.`date` AS `created`,'
				. ' a.`date` AS `modified`,'
				. ' a.`published` AS `published`,'
				. ' ' . $db->quote('0000-00-00 00:00:00') . ' AS `publish_up`,'
				. ' ' . $db->quote('0000-00-00 00:00:00') . ' AS `publish_down`,'
				. ' ' . $db->quote('0') . ' AS `ordering`,'
				. ' a.`voting_yes` AS `vote`,'
				. ' ' . $db->quote('0') . ' AS `hits`,'
				. ' ' . $db->quote('1') . ' AS `sent`,'
				. ' ' . $db->quote('0') . ' AS `parent_id`,'
				. ' ' . $db->quote('0') . ' AS `lft`,'
				. ' ' . $db->quote('0') . ' AS `rgt`,'
				. ' b.`created_by` AS `blog_owner`, b.`title` AS `blog_title`'
				. ' FROM `#__comment` AS a INNER JOIN `#__easyblog_post` AS b'
				. ' ON a.`contentid` = b.`id`'
				. $queryWhere
				. $queryOrder
				. $queryLimit;

		$db->setQuery($query);

		$result	= $db->loadObjectList();

		if ($db->getErrorNum() > 0) {
			throw EB::exception($db->getErrorMsg() . $db->stderr(), $db->getErrorNum());
		}

		return $result;
	}

	function getLatestComment($blogId, $parentId = 0)
	{
		$db	= EB::db();

		$query	= 'SELECT `id`, `lft`, `rgt` FROM `#__easyblog_comment`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($blogId);
		if($parentId != 0)
			$query	.= ' AND `parent_id` = ' . $db->Quote($parentId);
		else
			$query	.= ' AND `parent_id` = ' . $db->Quote('0');
		$query	.= ' ORDER BY `lft` DESC LIMIT 1';

		$db->setQuery($query);
		$result	= $db->loadObject();

		return $result;
	}

	function updateCommentSibling($blogId, $nodeValue)
	{
		$db	= EB::db();

		$query	= 'UPDATE `#__easyblog_comment` SET `rgt` = `rgt` + 2';
		$query	.= ' WHERE `rgt` > ' . $db->Quote($nodeValue);
		$query	.= ' AND `post_id` = ' . $db->Quote($blogId);
		$db->setQuery($query);
		$db->query();

		$query	= 'UPDATE `#__easyblog_comment` SET `lft` = `lft` + 2';
		$query	.= ' WHERE `lft` > ' . $db->Quote($nodeValue);
		$query	.= ' AND `post_id` = ' . $db->Quote($blogId);
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Allows caller to add like on a comment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function like($id, $userId = null)
	{
		$config = EB::getConfig();
		$user = JFactory::getUser($userId);
		$userId = $user->id;

		$date = EB::date();
		$likes = EB::table('Likes');

		// Check if the likes already exists
		$state = $likes->load(array('type' => 'comment', 'content_id' => $id, 'created_by' => $userId));

		if ($state) {
			return false;
		}

		$likes->type = 'comment';
		$likes->content_id = $id;
		$likes->created_by = $userId;
		$likes->created = $date->toSql();

		$likes->store();

		// @rule: Send notification to comment authors.
		if ($config->get('notification_commentlike')) {

			//send notification to comment's author
			$likeActor 	= EB::user($userId);

			$model = EB::model('Comment');
			$notification = EB::notification();
			$commentObj	= $model->getComment($id);
			$commentAuthor = JFactory::getUser($commentObj->created_by);

			$obj = new stdClass();
			$obj->unsubscribe = false;
			$obj->email = $commentAuthor->email;

			$emails[$commentAuthor->email] = $obj;

			$data = array(
					'commentLikedActor' => $likeActor->getName(),
					'commentContent' => $commentObj->comment,
					'commentLikedActorAvatar' => $likeActor->getAvatar(),
					'commentDate' => $date->format(JText::_('DATE_FORMAT_LC3')),
					'commentLink' => EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id='. $commentObj->post_id, true, true)
			);

			$notification->send($emails ,JText::_('COM_EASYBLOG_NOTIFICATION_NEW_LIKE') ,'comment.like' ,$data);
		}

		return $likes;
	}

	/**
	 * Allows caller to unlike a comment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unlike($id, $userId = null)
	{
		$user   = JFactory::getUser($userId);
		$userId = $user->id;

		$likes	= EB::table('Likes');
		$likes->load(array('type' => 'comment', 'content_id' => $id, 'created_by' => $userId));

		return $likes->delete();
	}

	function isLikeComment($commentId, $userId)
	{
		// @rule: Since guest cannot like a comment, no point running the following query
		if ($userId == 0) {
			return 0;
		}

		$db = EB::db();

		$query  = 'SELECT `id` FROM `#__easyblog_likes`';
		$query  .= ' WHERE `type` = ' . $db->Quote('comment');
		$query  .= ' AND `content_id` = ' . $db->Quote($commentId);
		$query  .= ' AND `created_by` = ' . $db->Quote($userId);

		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}

	function getComment($commentId)
	{
		$db = EB::db();

		$query = 'SELECT * FROM `#__easyblog_comment`';
		$query .= ' WHERE `id` = ' . $db->Quote($commentId);

		$db->setQuery($query);

		$result = $db->loadObject();
		return $result;
	}

	function getCommentTotalLikes($commentId)
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM `#__easyblog_likes`';
		$query .= ' WHERE `type` = ' . $db->Quote('comment');
		$query .= ' AND `content_id` = ' . $db->Quote($commentId);

		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}

	function getUserModerateCommentCount($userId)
	{
		if($userId == 0) {
			return 0;
		}

		$db = EB::db();

		$query	= 'select count(1) from `#__easyblog_comment` as a';
		$query	.= '  inner join `#__easyblog_post` as b on a.`post_id` = b.`id`';
		$query	.= '  and b.`created_by` = ' . $db->Quote($userId);
		$query	.= ' where a.`published` = ' . $db->Quote(EBLOG_COMMENT_STATUS_MODERATED);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves the comment count for a post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getCount($postId)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_comment') . ' as a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__users') . ' AS uu ON a.`created_by` = uu.`id`';
		$query[] = 'WHERE a.' . $db->qn('post_id') . '=' . $db->Quote($postId);
		$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND (uu.`block` = 0 OR uu.`id` IS NULL)';

		$query = implode(' ', $query);
		$db->setQuery($query);

		$count	= $db->loadResult();

		return $count;
	}

	/**
	 * Preload a list of comment counter for each posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadComments($postIds = array())
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.* FROM ' . $db->qn('#__easyblog_comment') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__users') . ' AS uu ON a.`created_by` = uu.`id`';
		$query[] = 'WHERE a.' . $db->qn('post_id') . ' IN(' . implode(',', $postIds) . ')';
		$query[] = 'AND a.' . $db->qn('published')  . '=' . $db->Quote(1);
		$query[] = 'AND (uu.`block` = 0 OR uu.`id` IS NULL)';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}

		$comments = array();

		foreach ($result as $row) {

			if (!isset($comments[$row->post_id])) {
				$comments[$row->post_id] = array();
			}

			$comment = EB::table('Comment');
			$comment->bind($row);

			$comments[$row->post_id][] = $comment;
		}

		return $comments;
	}

	/**
	 * Preload a list of comment counter for each posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadCommentCount($postIds = array())
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT a.' . $db->qn('post_id') . ', COUNT(1) AS ' . $db->qn('count');
		$query[] = 'FROM ' . $db->qn('#__easyblog_comment') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__users') . ' AS uu ON a.`created_by` = uu.`id`';
		$query[] = 'WHERE a.' . $db->qn('post_id') . ' IN(' . implode(',', $postIds) . ')';
		$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND (uu.`block` = 0 OR uu.`id` IS NULL)';
		$query[] = 'GROUP BY a.' . $db->qn('post_id');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}

		$counter = array();

		foreach ($result as $row) {
			$counter[$row->post_id] = $row->count;
		}

		return $counter;
	}

	/**
	 * Get row number of a comment
	 *
	 * @since   6.0
	 * @access  public
	 */
	public function getRowNumber($postId, $commentId)
	{
		$config = EB::config();
		$db = EB::db();

		$commentSort = $config->get('comment_sort');
		$commentLimit = $config->get('comment_pagination');

		$sortCondition = ' AND a.`lft` >= b.`lft`';
		$sortOrderBy = ' ORDER BY a.`lft` ASC';

		if ($commentSort === 'desc') {
			$sortCondition = ' AND a.`rgt` <= b.`rgt`';
			$sortOrderBy = ' ORDER BY a.`rgt` ASC';
		}

		$query = [];
		$query[] = 'SELECT COUNT(1) AS `row_number`';
		$query[] = 'FROM `#__easyblog_comment` AS a';
		$query[] = 'INNER JOIN `#__easyblog_comment` AS b ON a.`post_id` = b.`post_id`';
		$query[] = 'AND a.`post_id` = ' . $db->Quote($postId);
		$query[] = $sortCondition;
		$query[] = 'WHERE a.`published` = ' . $db->Quote(1);
		$query[] = 'AND a.`id` = ' . $db->Quote($commentId);
		$query[] = 'GROUP BY a.`id`';
		$query[] = $sortOrderBy;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$row = $db->loadResult();

		return $row;
	}
}
