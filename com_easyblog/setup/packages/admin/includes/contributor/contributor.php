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

class EasyBlogContributor extends EasyBlog
{
	public $type = null;
	public $uid = null;

	static $items = array();


	public function load($id, $type)
	{
		$this->uid = $id;
		$this->type = $type;

		$index = $this->uid . $this->type;

		if (!isset(self::$items[$index])) {
			self::$items[$index] = $this->getItem($id, $type);
		}

		return self::$items[$index];
	}

	/**
	 * Retrieves the contributor item
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getItem()
	{
		require_once(__DIR__ . '/adapters/' . $this->type . '.php');

		$class = $this->getClassName();

		$obj = new $class($this->uid, $this->type);

		return $obj;
	}

	/**
	 * Retrieves the class name for the adapter
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getClassName()
	{
		if ($this->type == EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP) {
			return 'EasyBlogContributorEasySocialGroup';
		}

		if ($this->type == EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT) {
			return 'EasyBlogContributorEasySocialEvent';
		}

		if ($this->type == EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE) {
			return 'EasyBlogContributorEasySocialPage';
		}		

		if ($this->type == EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP) {
			return 'EasyBlogContributorJomsocialGroup';
		}

		if ($this->type == EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT) {
			return 'EasyBlogContributorJomsocialEvent';
		}

		return 'EasyBlogContributorTeamBlog';
	}

	/**
	 * Determine if the the contributor is from EasySocial
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function isEasySocial($type = null)
	{
		$easysocial = array(
			EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP,
			EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT,
			EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE
		);

		if (!$type) {
			$type = $this->type;
		}

		if (in_array($type, $easysocial)) {
			return true;
		}

		return false;
	}

	/**
	 * generate contribution access sql that used with blogs
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function genAccessSQL($contributorType, $columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$gid	= array();

		if ($my->id == 0) {
			$gid	= JAccess::getGroupsByUser(0, false);
		} else {
			$gid	= JAccess::getGroupsByUser($my->id, false);
		}

		$gids = '';
		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $id : ',' . $id;
			}
		}

		$sourceSQL = '';
		if ($contributorType == EASYBLOG_POST_SOURCE_TEAM) {
			$sourceSQL = self::getTeamBlogSQL($columnPrefix, $gids, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP) {
			$sourceSQL = self::getJomSocialGroupSQL($columnPrefix, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT) {
			$sourceSQL = self::getJomSocialEventSQL($columnPrefix, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP) {
			$sourceSQL = self::getEasySocialGroupSQL($columnPrefix, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT) {
			$sourceSQL = self::getEasySocialEventSQL($columnPrefix, $options);

		} else if ($contributorType == EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE) {
			$sourceSQL = self::getEasySocialPageSQL($columnPrefix, $options);
		}

		$concate = isset($options['concateOperator']) ? $options['concateOperator'] : 'OR';

		$sql = '';
		if ($sourceSQL) {
			//starting bracket
			$sql = " $concate (";
			$sql .= $sourceSQL;
			//ending bracket
			$sql .= ")";
		}

		return $sql;
	}

	private static function getEasySocialGroupSQL($columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP);
		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__social_clusters` as srcesgroup";
		$query .= " LEFT JOIN `#__social_clusters_nodes` as nodes";
		$query .= " ON srcesgroup.`id` = nodes.`cluster_id`";
		$query .= " where srcesgroup.`id` = $columnPrefix.`source_id` and srcesgroup.`cluster_type` = 'group'";

		$query .= self::getClusterPrivacySql('srcesgroup', 'nodes');

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}

	private static function getEasySocialPageSQL($columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE);
		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__social_clusters` as srcespage";
		$query .= " LEFT JOIN `#__social_clusters_nodes` as nodes";
		$query .= " ON srcespage.`id` = nodes.`cluster_id`";
		$query .= " where srcespage.`id` = $columnPrefix.`source_id` and srcespage.`cluster_type` = 'page'";

		$query .= self::getClusterPrivacySql('srcespage', 'nodes');

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}	

	private static function getEasySocialEventSQL($columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT);
		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__social_clusters` as srcesevent";
		$query .= " LEFT JOIN `#__social_clusters_nodes` as nodes";
		$query .= " ON srcesevent.`id` = nodes.`cluster_id`";
		$query .= " where srcesevent.`id` = $columnPrefix.`source_id` and srcesevent.`cluster_type` = 'event'";

		$query .= self::getClusterPrivacySql('srcesevent', 'nodes');

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}


	private static function getJomSocialEventSQL($columnPrefix, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		// $queryEvent	= 'SELECT ' . $db->nameQuote( 'post_id' ) . ' FROM';
		// $queryEvent	.= ' ' . $db->nameQuote( '#__easyblog_external' ) . ' AS ' . $db->nameQuote( 'a' );
		// $queryEvent	.= ' INNER JOIN' . $db->nameQuote( '#__community_events' ) . ' AS ' . $db->nameQuote( 'b' );
		// $queryEvent	.= ' ON ' . $db->nameQuote( 'a' ) . '.uid = ' . $db->nameQuote( 'b' ) . '.id';
		// $queryEvent	.= ' AND ' . $db->nameQuote( 'a' ) . '.' . $db->nameQuote( 'source' ) . '=' . $db->Quote( 'jomsocial.event' );
		// $queryEvent	.= ' WHERE ' . $db->nameQuote( 'b' ) . '.' . $db->nameQuote( 'permission' ) . '=' . $db->Quote( 0 );

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT);
		$mainQuery .= " and 1 <= (";

		// $query = "select count(1) from `#__community_events` as srcjsevent";
		// $query .= " where srcjsevent.`id` = $columnPrefix.`source_id`";
		// $query .= " and srcjsevent.`permission` = 0";

		// $mainQuery .= $query;
		// $mainQuery .= ')';

		// return $mainQuery;

		$query = "select count(1) from `#__community_events` as srcjsevent";
		$query .= " LEFT JOIN `#__community_events_members` as nodes";
		$query .= " ON srcjsevent.`id` = nodes.`eventid`";
		$query .= " where srcjsevent.`id` = $columnPrefix.`source_id`";
		$query .= " and nodes.`approval` = 0";

		$query .= self::getClusterPrivacySql('srcjsevent', 'nodes', 'com_community');

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}


	private static function getJomSocialGroupSQL($columnPrefix, $options = array())
	{
		$db = EB::db();

		// $queryJSGrp = 'select `post_id` from `#__easyblog_external_groups` as exg inner join `#__community_groups` as jsg';
		// $queryJSGrp .= '      on exg.group_id = jsg.id ';
		// $queryJSGrp .= '      where jsg.`approvals` = 0';

		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP);
		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__community_groups` as srcjsgroup";
		$query .= " LEFT JOIN `#__community_groups_members` as nodes";
		$query .= " ON srcjsgroup.`id` = nodes.`groupid`";
		$query .= " where srcjsgroup.`id` = $columnPrefix.`source_id`";
		$query .= " and nodes.`approved` = 1";

		$query .= self::getClusterPrivacySql('srcjsgroup', 'nodes', 'com_community');

		$mainQuery .= $query;
		$mainQuery .= ')';

		return $mainQuery;
	}

	private static function getTeamBlogSQL($columnPrefix, $gids = '', $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$isAdminOnly = (isset($options['isAdminOnly'])) ? $options['isAdminOnly'] : false;


		$mainQuery = " $columnPrefix.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_TEAM);

		if (isset($options['teamId']) && $options['teamId']) {
			$mainQuery .= " and $columnPrefix.source_id = " . $db->Quote($options['teamId']);
		}

		$mainQuery .= " and 1 <= (";

		$query = "select count(1) from `#__easyblog_team` as srcteam";
		$query .= " where srcteam.`id` = $columnPrefix.`source_id`";
		$query .= " and srcteam.`published` = 1";

		if ($isAdminOnly) {
			$query .= " AND (select count(1) from `#__easyblog_team_users` as srcteamuser where srcteamuser.`team_id` = srcteam.`id` and srcteamuser.`user_id` = " . $my->id ." and srcteamuser.`isadmin` = 1  ) > 0";
		} else {

			$query .= " and ( (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_EVERYONE .")";

			if ($gids) {
				$query .= "       OR (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_REGISTERED ." and (select count(1) from `#__easyblog_team_groups` as srcteamgrp where srcteamgrp.`team_id` = srcteam.`id` and srcteamgrp.`group_id` IN (" . $gids . ") ) > 0)";
			}

			if ($my->id) {
				$query .= "       OR (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_MEMBER ." and (select count(1) from `#__easyblog_team_users` as srcteamuser where srcteamuser.`team_id` = srcteam.`id` and srcteamuser.`user_id` = " . $my->id ."  ) > 0)";

				$query .= "       OR (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_MEMBER ." and (select count(1) from `#__easyblog_team_groups` as srcteamgrp where srcteamgrp.`team_id` = srcteam.`id` and srcteamgrp.`group_id` IN (" . $gids . ") ) > 0)";

				$query .= "		OR (srcteam.`access` = ". EBLOG_TEAMBLOG_ACCESS_REGISTERED . ")";
			}

			$query .= ")";

		}


		$mainQuery .= $query;
		$mainQuery .= ')';


		return $mainQuery;
	}

	/**
	 * Method to generate query for EasySocial cluster privacy access
	 *
	 * @since	5.1
	 * @access	public
	 */
	private static function getClusterPrivacySql($clusterPrefix, $nodesPrefix, $componentPrefix = 'com_easysocial')
	{
		$db = EB::db();
		$user = EB::user();

		if ($componentPrefix == 'com_community') {

			// Jomsocial group part
			// 0 = public , 1 = private define from the group approvals column

			if ($clusterPrefix == 'srcjsgroup') {
				
				$query = " AND (";
					$query .= " $clusterPrefix.`approvals` = " . $db->Quote('0');
					$query .= " OR (";
						$query .= " $clusterPrefix.`approvals` = "  . $db->Quote('1');
						$query .= " AND $nodesPrefix.`memberid` = " . $db->Quote($user->id);
						$query .= " AND $nodesPrefix.`approved` = " . $db->Quote('1');
					$query .= " )";
				$query .= " )";

			} else {
				// Jomsocial event part
				// 0 = public , 1 = invite define from the event `permission` column

				$query = " AND (";
					$query .= " $clusterPrefix.`permission` = " . $db->Quote('0');
					$query .= " OR (";
						$query .= " $clusterPrefix.`permission` = "  . $db->Quote('1');
						$query .= " AND $nodesPrefix.`memberid` = " . $db->Quote($user->id);					
					$query .= " )";
				$query .= " )";
			}
			
		} else {

			// Easysocial part
			// 1 = public, 2 = private, 3 = invite only, 4 = public (need approval to join)

			$query = " AND (";
				$query .= " $clusterPrefix.`type` = " . $db->Quote('1');
				$query .= " OR $clusterPrefix.`type` = " . $db->Quote('4');
				$query .= " OR (";
					$query .= " $clusterPrefix.`type` = "  . $db->Quote('2');
					$query .= " AND $nodesPrefix.`uid` = " . $db->Quote($user->id);
				$query .= " )";
				$query .= " OR (";
					$query .= " $clusterPrefix.`type` = "  . $db->Quote('3');
					$query .= " AND $nodesPrefix.`uid` = " . $db->Quote($user->id);
				$query .= " )";
			$query .= " )";
		}

		return $query;
	}
}
