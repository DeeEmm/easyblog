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

require_once(__DIR__ . '/table.php');

class EasyBlogTableCategoryAcl extends EasyBlogTable
{
	public $id = null;
	public $category_id = null;
	public $acl_id = null;
	public $acl_type = null;
	public $type = null;
	public $content_id = null;
	public $status = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_category_acl' , 'id' , $db);
	}

	/**
	 * For category acl assigned to specific users
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function getUserAssignedCategoryACL($user, $parentOnly = false, $count = false)
	{
		if (!is_object($user)) {
			$user = EB::user($user);
		}

		// @task: Return empty array if user is a guest.
		if ($user->id == '0') {
			return $count ? 0 : array();
		}

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT';

		if ($count) {
			$query[] = 'COUNT(1)';
		} else {
			$query[] = ' a.`category_id`, b.`parent_id`';
		}

		$query[] = 'FROM `#__easyblog_category_acl` as a';
		$query[] = 'LEFT JOIN `#__easyblog_category` as b';
		$query[] = 'ON a.`category_id` = b.`id`';
		$query[] = 'WHERE b.`published` = ' . $db->Quote('1');
		$query[] = 'AND a.`acl_id` = ' . $db->Quote(CATEGORY_ACL_ACTION_SELECT);
		$query[] = 'AND a.`type` = ' . $db->Quote('user');
		$query[] = 'AND a.`content_id` = ' . $db->Quote($user->id);

		if ($parentOnly) {
			$query[] = 'AND b.`parent_id` = ' . $db->Quote('0');
		}

		$query = implode(' ', $query);
		
		// echo '-- ' . $user->name . ' new<br/>';
		// echo '<code>' . str_replace('#_', 'jos', $query) . ';</code>';exit;

		$db->setQuery($query);

		if ($count) {
			return $db->loadResult();
		}

		$result = $db->loadObjectList();

		if (empty($result)) {
			return array();
		}

		$assignedCategory = array();

		foreach ($result as $category) {
			$assignedCategory[] = $category;
		}

		return $assignedCategory;
	}

	/**
	 * For categories acl that have group assigned permissions.
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function getGroupAssignedACL($categoryId)
	{
		$db = EB::db();

		// @task: Prepare the rules.
		$rules = array();
		$rules[CATEGORY_ACL_ACTION_VIEW] = array('type' =>'view');
		$rules[CATEGORY_ACL_ACTION_SELECT] = array('type' =>'create');

		$query = array();
		$query[] = 'SELECT `category_id`, `content_id`, `acl_id`, `acl_type`, `status`';
		$query[] = 'FROM `#__easyblog_category_acl`';
		$query[] = 'WHERE `category_id` = ' . $db->Quote($categoryId);
		$query[] = 'AND `type` = ' . $db->Quote('group');

		$query = implode(' ', $query);
		// echo str_replace('#_', 'jos', $query) . ';';exit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		// @task: Get usergroups.
		$model = EB::model('bloggers');
		$joomlaGroups = $model->getJoomlaUserGroups();
		
		// @task: Populate rules with usergroups.
		$acl = $this->mapRules($result, $joomlaGroups, $rules);

		return $acl;
	}

	/**
	 * For categories that have user assigned permissions instead of user groups
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function getUserAssignedACL($categoryId)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT a.`category_id`, a.`content_id` as `userId`, a.`acl_id`, b.`username`, b.`name`';
		$query[] = ' FROM `#__easyblog_category_acl` as a';
		$query[] = ' LEFT JOIN `#__users` as b';
		$query[] = ' ON a.`content_id` = b.`id`';
		$query[] = ' WHERE a.`category_id` = ' . $db->Quote($categoryId);
		$query[] = ' AND a.`type` = ' . $db->Quote('user');

		$query = implode(' ', $query);
		// echo str_replace('#_', 'jos', $query) . ';';exit;
		$db->setQuery($query);
		$result = $db->loadObjectList();

		$usertags = array();

		if (empty($result)) {
			return $usertags;
		}

		foreach ($result as $row) {
			$user = new stdClass();
			$user->id = $row->userId;
			$user->username = $row->name;

			$usertags[] = $user;
		}

		return $usertags;
	}

	private function mapRules($catRules, $joomlaGroups, $rules)
	{
		$db = EB::db();
		$acl = array();

		foreach ($rules as $id => $item) {
			$aclId = $id;
			$default = '1';

			foreach ($joomlaGroups as $joomla) {
				$groupId = $joomla->id;
				$catRulesCnt = count($catRules);

				if (empty($acl[$aclId][$groupId])) {
					$acl[$aclId][$groupId] = new stdClass();
				}

				if ($catRulesCnt > 0) {
					$cnt = 0;

					foreach ($catRules as $rule) {
						if ($rule->acl_id == $aclId && $rule->content_id == $groupId) {
							$acl[$aclId][$groupId]->status = $rule->status;
							$acl[$aclId][$groupId]->acl_id = $aclId;
							$acl[$aclId][$groupId]->groupname = $joomla->name;
							$acl[$aclId][$groupId]->groupid = $groupId;
							break;
						} else {
							$cnt++;
						}
					}

					if ($cnt == $catRulesCnt) {
						$acl[$aclId][$groupId]->status = '0';
						$acl[$aclId][$groupId]->acl_id = $aclId;
						$acl[$aclId][$groupId]->groupname = $joomla->name;
						$acl[$aclId][$groupId]->groupid = $groupId;
					}
				} else {
					$acl[$aclId][$groupId]->status = $default;
					$acl[$aclId][$groupId]->acl_id = $aclId;
					$acl[$aclId][$groupId]->groupname = $joomla->name;
					$acl[$aclId][$groupId]->groupid = $groupId;
				}
			}
		}

		return $acl;
	}
}
