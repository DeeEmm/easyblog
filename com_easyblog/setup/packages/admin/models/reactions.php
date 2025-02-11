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

class EasyBlogModelReactions extends EasyBlogAdminModel
{
	public $total = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.reactions.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.reactions.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the pagination
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
	 * Delete reactions history for a particular post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deletePostReactions($postId)
	{
		$db = EB::db();
		$query = array();

		$query[] = 'DELETE FROM ' . $db->qn('#__easyblog_reactions_history');
		$query[] = 'WHERE ' . $db->qn('post_id') . '=' . $db->Quote($postId);

		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Used in the administration area to list recent reactions
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getData()
	{
		$db = EB::db();
		$query = array();

		$query[] = 'SELECT a.*, b.' . $db->qn('type');
		$query[] = 'FROM ' . $db->qn('#__easyblog_reactions_history') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_reactions') . ' AS b';
		$query[] = 'ON a.' . $db->qn('reaction_id') . ' = b.' . $db->qn('id');
		$query[] = 'ORDER BY a.' . $db->qn('created') . ' DESC';

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
	 * Retrieves type of reactions
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getReactions(EasyBlogPost $post)
	{
		$db = EB::db();
		$query = array();
		$query[] = 'SELECT a.*, count(b.' . $db->qn('id') . ') AS total FROM '. $db->qn('#__easyblog_reactions') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_reactions_history') . ' AS b';
		$query[] = 'ON a.' . $db->qn('id') . ' = b.' . $db->qn('reaction_id');
		$query[] = 'AND b.' . $db->qn('post_id') . '=' . $db->Quote($post->id);
		$query[] = 'WHERE a.' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'GROUP BY a.' . $db->qn('id');

		$query = implode(' ', $query);
		$db->setQuery($query);

		$reactions = $db->loadObjectList();

		return $reactions;
	}

	/**
	 * Retrieves the reaction object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUserReaction($postId, $userId, $tokenId)
	{
		$db = EB::db();
		$query = array();
		$query[] = 'SELECT b.* FROM '. $db->qn('#__easyblog_reactions_history') . ' AS a';
		$query[] = 'LEFT JOIN '. $db->qn('#__easyblog_reactions') . 'AS b';
		$query[] = 'ON a.' . $db->qn('reaction_id') .' =  b.' . $db->qn('id');
		$query[] = 'WHERE a.' . $db->qn('post_id') .' = ' . $db->Quote($postId);
		
		if ($userId) {
			$query[] = 'AND a.' . $db->qn('user_id') .' = ' . $db->Quote($userId);
		} else {
			$query[] = 'AND a.' . $db->qn('token_id') .' = ' . $db->Quote($tokenId);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		$reaction = $db->loadObject();

		return $reaction;
	}

	/**
	 * Retrieves the reaction history
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getReactionHistory($postId, $userId, $tokenId)
	{
		$db = EB::db();
		$query = array();
		$query[] = 'SELECT * FROM '. $db->qn('#__easyblog_reactions_history');
		$query[] = 'WHERE ' . $db->qn('post_id') .' = ' . $db->Quote($postId);
		
		if ($userId) {
			$query[] = 'AND ' . $db->qn('user_id') .' = ' . $db->Quote($userId);
		} else {
			$query[] = 'AND ' . $db->qn('token_id') .' = ' . $db->Quote($tokenId);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		$reaction = $db->loadObject();

		return $reaction;
	}

	/**
	 * Store reaction per post by given user
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function storeReaction($userId, $tokenId, $postId, $reactionId)
	{
		$db = EB::db();
		$query = array();
		
		$options = array(
						'post_id' => $postId,
						'user_id' => $userId
					);

		if (!$userId) {
			$options['token_id'] = $tokenId;
		}

		// Delete any previous reaction if there is any
		$previous = EB::table('ReactionHistory');
		$exists = $previous->load($options);

		if ($exists) {
			$previous->delete();
		}

		$history = EB::table('ReactionHistory');
		$history->post_id = $postId;
		$history->reaction_id = $reactionId;
		$history->user_id = $userId;
		$history->token_id = $tokenId;
		$history->created = JFactory::getDate()->toSql();
		$state = $history->store();

		return $state;
	}
}
