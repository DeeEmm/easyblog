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

require_once(__DIR__ . '/model.php');

class EasyBlogModelRatings extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Remove all ratings associated with a composite key
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeRating($type, $uid)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'DELETE FROM ' . $db->qn('#__easyblog_ratings');
		$query[] = 'WHERE ' . $db->qn('type') . '=' . $db->Quote($type);
		$query[] = 'AND ' . $db->qn('uid') . '=' . $db->Quote($uid);

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Determines if a particular user has voted the item before
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasVoted($uid, $type, $userId = 0, $hash = '', $ipaddr = '')
	{
		$db = EB::db();
		$query	= 'SELECT COUNT(1) FROM ' . $db->qn( '#__easyblog_ratings' ) . ' '
				. 'WHERE ' . $db->qn( 'uid' ) . '=' . $db->Quote( $uid ) . ' '
				. 'AND ' . $db->qn( 'type' ) . '=' . $db->Quote( $type );

		if ($userId) {
			$query .= ' AND ' . $db->qn( 'created_by' ) . '=' . $db->Quote( $userId );
		} else {
			// guest. we need to check the session as well as the ipaddr
			$query .= ' AND ' . $db->qn( 'created_by' ) . '=' . $db->Quote(0);
			$query .= ' AND (' . $db->qn('sessionid') . ' = ' . $db->Quote($hash) . ' OR ' . $db->qn('ip') . ' = ' . $db->Quote($ipaddr) . ')';
		}

		// echo $query;

		$db->setQuery( $query );
		$rating	= $db->loadResult();

		return ($rating) ? $rating : 0;
	}

	/**
	 * Retrieves the rated value of an object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRatingValues($uid, $type)
	{
		$db = EB::db();
		$query	= 'SELECT AVG(value) AS ratings, COUNT(1) AS total FROM ' . $db->qn( '#__easyblog_ratings' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $uid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );

		$db->setQuery($query);

		$rating = $db->loadObject();
		$rating->ratings = round($rating->ratings);

		return $rating;
	}

	/**
	 * Preload user rating for each posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadUserPostRatings($postIds, $userId, $hash = '', $ipaddr = '')
	{
		$db = EB::db();

		$query	= 'SELECT COUNT(1) as ' . $db->qn('voted').', ' . $db->qn('uid') . ' FROM ' . $db->qn( '#__easyblog_ratings' );
		if (count($postIds) == 1) {
			$query .= ' WHERE ' . $db->qn( 'uid' ) . ' = ' . $db->Quote( $postIds[0] );
		} else {
			$query .= ' WHERE ' . $db->qn( 'uid' ) . ' IN ('. implode(',', $postIds) .')';
		}
		$query .= ' AND ' . $db->qn( 'type' ) . '=' . $db->Quote( EASYBLOG_RATINGS_ENTRY );

		if ($userId) {
			$query .= ' AND ' . $db->qn( 'created_by' ) . '=' . $db->Quote( $userId );
		} else {
			// guest. we need to check the session as well as the ipaddr
			$query .= ' AND ' . $db->qn( 'created_by' ) . '=' . $db->Quote(0);
			$query .= ' AND (' . $db->qn('sessionid') . ' = ' . $db->Quote($hash) . ' OR ' . $db->qn('ip') . ' = ' . $db->Quote($ipaddr) . ')';
		}

		$query .= ' GROUP BY ' . $db->qn('uid');

		// we do this order by null to avoid filesort in mysql.
		$query .= ' ORDER BY NULL';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$results) {
			return array();
		}

		$ratings = array();
		foreach ($results as $row) {
			$ratings[$row->uid] = $row->voted;
		}

		return $ratings;
	}

	/**
	 * Preload a list of ratings for each posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadRatings($postIds = array())
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT AVG(' . $db->qn('value') . ') AS ' . $db->qn('ratings');
		$query[] = ',COUNT(1) AS ' . $db->qn('total');
		$query[] = ',' . $db->qn('uid');
		$query[] = 'FROM ' . $db->qn('#__easyblog_ratings') . ' AS a';
		$query[] = 'WHERE a.' . $db->qn('uid') . ' IN(' . implode(',', $postIds) . ')';
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote(EASYBLOG_RATINGS_ENTRY);
		$query[] = 'GROUP BY ' . $db->qn('uid');

		// we do this order by null to avoid filesort in mysql.
		$query[] = 'ORDER BY NULL';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}

		$ratings = array();

		foreach ($result as $row) {

			$obj = new stdClass();
			$obj->ratings = round($row->ratings);
			$obj->total = $row->total;

			$ratings[$row->uid] = $obj;
		}

		return $ratings;
	}


	/**
	 * Retrieves a list of users that rated on an item
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRatingUsers($uid, $type, $limit = 5)
	{
		$db = EB::db();

		$query	= 'SELECT COUNT(' . $db->nameQuote( 'created_by' ) . ') AS times, ' . $db->nameQuote( 'created_by' ) . ' , ' . $db->nameQuote( 'created' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__easyblog_ratings' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $uid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type ) . ' '
				. 'GROUP BY ' . $db->nameQuote( 'created_by' ) . ' '
				. 'LIMIT 0,' . $limit;

		$db->setQuery($query);

		$result	= $db->loadObjectList();
		return $result;
	}
}