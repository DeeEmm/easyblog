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

class EasyBlogAcl extends EasyBlog
{
	/**
	 * Retrieves the ACL ruleset
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getRuleSet($cid = '')
	{
		static $rulesData = array();

		$my = JFactory::getUser($cid);

		// If the data is already stored, we just load back the data.
		if (isset($rulesData[$my->id])) {
			return $rulesData[$my->id];
		}

		$rulesets = new EasyBlogAclRuleset();

		// Retrieve rules
		$rules = self::getRules('id');

		$db = EB::db();

		if (!empty($my->id)) {

			$rulesets->id = $my->id;
			$rulesets->name = $my->name;
			$rulesets->group = isset($my->usertype) ? $my->usertype : '';

			// @Task: Load default values.
			foreach ($rules as $rule) {
				$rulesets->rules->{$rule->action} = (int) $rule->default;
			}

			$result = array();

			// Get user's joomla usergroups ids.
			$groupIds = '';
			$query = 'SELECT `group_id` FROM `#__user_usergroup_map` WHERE `user_id` = ' . $db->Quote($my->id);
			$db->setQuery($query);

			$groupIds = $db->loadColumn();

			if (!$groupIds) {
				// something went wrong. this user did not have any Joomla group ;(
				$groupIds = array('1'); // default to Public group.
			}

			$groups = self::getEasyBlogAclGroups($groupIds);

			// if there is no acls return, that means the Joomla groups that are assigned to this user,
			// admin has yet to configure the EB ACL. Probably a new joomla user group.
			// Lets treat this user as guest. # 1920
			if (!$groups && !FH::isSiteAdmin()) {
				$groups = self::getEasyBlogAclGroups(array('1'));
			}

			// Allow explicit overrides in the groups
			// If user A is in group A (allow) and group B (not allowed) , user A should be allowed
			$result = array();

			foreach ($groups as $rule) {
				if (!isset($result[0][$rule->acl_id])) {
					$result[0][ $rule->acl_id ]	= new stdClass();
				}

				if (isset( $result[0][ $rule->acl_id]->acl_id ) && $result[0][$rule->acl_id]->status != '1' || !isset($result[0][$rule->acl_id]->acl_id)) {
					$result[0][$rule->acl_id]->acl_id = $rule->acl_id;
					$result[0][$rule->acl_id]->status = $rule->status;
				}
			}

			$rulesets = self::mapRules($result, $rules, $rulesets);

		} else {

			// Cannot rely on JComponentHelper::getParams( 'com_users' )->get( 'guest_usergroup' )
			// because Joomla 3.2.0 onwards always returns 13.
			$user = JFactory::getUser();
			$gids  = $user->getAuthorisedGroups();

			$groups = self::getEasyBlogAclGroups($gids);

			// Allow explicit overrides in the groups
			// If user A is in group A (allow) and group B (not allowed) , user A should be allowed
			$result = array();

			foreach ($groups as $rule) {

				if (!isset($result[0][$rule->acl_id])) {
					$result[0][$rule->acl_id] = new stdClass();
				}

				if (isset($result[0][$rule->acl_id]->acl_id)
					&& $result[0][$rule->acl_id]->status != '1'
					|| !isset($result[0][$rule->acl_id]->acl_id)) {

					$result[0][$rule->acl_id]->acl_id = $rule->acl_id;
					$result[0][$rule->acl_id]->status = $rule->status;
				}
			}

			$rulesets = self::mapRules($result, $rules, $rulesets);
		}

		$rulesData[$my->id]	= $rulesets;

		return $rulesData[$my->id];
	}

	/**
	 * Retrieves EasyBlog ACL groups
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function getEasyBlogAclGroups($gids) {

		static $_cache = array();
		$db = EB::db();

		if (!is_array($gids)) {
			$gids = array($gids);
		}

		$ids = implode(', ', $gids);

		if (!isset($_cache[$ids])) {

			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_acl_group' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'content_id' ) . ' IN (' . $ids . ') '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' );

			$db->setQuery($query);
			$results = $db->loadObjectList();

			$_cache[$ids] = $results;
		}

		return $_cache[$ids];
	}

	/**
	 * Retrieves a list of filtered html tags
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getFilterTags($userId = null)
	{
		$my = JFactory::getUser($userId);
		$groupIds = EB::getUserGids($my->id);

		if (!$groupIds) {
			return false;
		}

		$filterTags = [];

		foreach ($groupIds as $groupId) {
			$filter = self::getFilterRule($groupId);

			if ($filter === false) {
				continue;
			}

			// Always respect empty tags
			$tags = $filter->get('disallow_tags');
			$tags = EBString::trim($tags);

			// When a user belongs to multiple group, always allow empty strings to have the highest precedence
			if (!$tags) {
				return [];
			}

			$tags = strtolower($tags);
			$tags = explode(',', $tags);

			$filterTags = array_merge($filterTags, $tags);
		}

		return $filterTags;
	}

	/**
	 * Retrieves a list of filter attributes
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getFilterAttributes($userId = null)
	{
		$my = JFactory::getUser($userId);
		$groupIds = EB::getUserGids($my->id);

		$filterAttributes = [];

		foreach ($groupIds as $groupId) {
			$filter = self::getFilterRule($groupId);

			if ($filter === false) {
				continue;
			}

			// Always respect empty tags
			$attributes = $filter->get('disallow_attributes');
			$attributes = EBString::trim($attributes);

			// When a user belongs to multiple group, always allow empty strings to have the highest precedence
			if (!$attributes) {
				return [];
			}

			$attributes = strtolower($attributes);
			$attributes = explode(',', $attributes);

			$filterAttributes = array_merge($filterAttributes, $attributes);
		}

		return $filterAttributes;
	}

	/**
	 * Retrieves a list of rules
	 *
	 * @since	5.0
	 * @access	public
	 */
	private static function getFilterRule($contentId)
	{
		static $cache = [];

		if (!isset($cache[$contentId])) {

			$db = EB::db();

			$query = [
				'SELECT * FROM ' . $db->nameQuote('#__easyblog_acl_filters'),
				'WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($contentId),
				'AND ' . $db->nameQuote('type') . '=' . $db->Quote('group')
			];
			$db->setQuery($query);

			$result	= $db->loadObject();

			if (!$result) {
				$cache[$contentId] = false;
			}

			if ($result) {
				$filter = EB::table('AclFilter');
				$filter->bind($result);

				$cache[$contentId] = $filter;
			}
		}

		return $cache[$contentId];
	}

	/**
	 * Rules mapping
	 *
	 * @since	5.0
	 * @access	public
	 */
	private static function mapRules( $result , $rules , $rulesets )
	{
		foreach ($result as $items) {
			foreach ($items as $rule) {
				if (isset($rules[$rule->acl_id])) {
					$action	= $rules[$rule->acl_id]->action;

					if (isset($rulesets->rules->{$action})) {
						// 'No' explicitly win
						if ($rulesets->rules->{$action} == '0') {
							continue;
						} else {
							$rulesets->rules->{$action}	= $rule->status;
						}
					} else {
						$rulesets->rules->{$action}	= $rule->status;
					}
				}
			}
		}

		return $rulesets;
	}

	/**
	 * Retrieves a list of rules
	 *
	 * @since	5.0
	 * @access	public
	 */
	private static function getRules()
	{
		static $data = null;

		$db = EB::db();

		if (!$data) {
			$query = array();
			$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_acl');
			$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
			$query[] = 'ORDER BY ' . $db->qn('id') . ' ASC';

			$query = implode(' ', $query);

			$db->setQuery($query);

			$result = $db->loadObjectList();

			if ($result) {
				foreach ($result as $row) {
					$data[$row->id]	 = $row;
				}
			}
		}

		return $data;
	}
}

class EasyBlogAclRuleset
{
	public $rules = null;
	public $id = null;
	public $name = null;
	public $group = null;

	public function __construct()
	{
		$this->rules = new stdClass();
	}

	public function get($rule)
	{
		if (isset($this->rules->$rule)) {
			return $this->rules->$rule;
		}

		// By default return false
		return false;
	}
}
