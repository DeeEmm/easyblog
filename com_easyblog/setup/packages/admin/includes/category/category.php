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

class EasyBlogCategory extends EasyBlog
{
	/**
	 * Generates a list of subcategories given the main categories
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getAllSubcategories($categories = [])
	{
		$items = [];

		foreach ($categories as $id) {

			// Retrieve nested categories
			$category = new stdClass();
			$category->id = $id;
			$category->childs = null;

			EB::buildNestedCategories($category->id, $category);

			$linkage = '';
			EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

			$catIds = [];
			$catIds[] = $category->id;
			EB::accessNestedCategoriesId($category, $catIds);

			$items = array_merge($items, $catIds);
		}

		return $items;
	}

	/**
	 * generate category access sql that used with blogs
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function genAccessSQL($columnId, $options = array(), $acl = CATEGORY_ACL_ACTION_VIEW)
	{
		$gid = [];

		if ($this->my->guest) {
			$gid = JAccess::getGroupsByUser(0, false);
		} else {
			$gid = JAccess::getGroupsByUser($this->my->id, false);
		}

		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $id : ',' . $id;
			}
		}

		$excludeCatSQL = '';
		$includeCatSQL = '';
		$typeCatSQL = '';
		$statCatSQL = '';

		if ($options) {
			if (isset($options['exclude']) && $options['exclude']) {

				if (is_array($options['exclude'])) {
					$options['exclude'] = array_unique($options['exclude']);
				}

				if (is_array($options['exclude']) && count($options['exclude']) > 1) {
					$excludeCatSQL = " AND cat.`id` NOT IN (" . implode(',', $options['exclude']) . ")";
				} else {
					$excludeCatSQL = (is_array($options['exclude'])) ? " AND cat.`id` != " . $options['exclude'][0] : " AND cat.`id` != " . $options['exclude'];
				}
			}

			if (isset($options['include']) && $options['include']) {

				if (is_array($options['include'])) {
					$options['include'] = array_unique($options['include']);
				}

				if (is_array($options['include']) && count($options['include']) > 1) {
					$includeCatSQL = " AND cat.`id` IN (" . implode(',', $options['include']) . ")";
				} else {
					$includeCatSQL = (is_array($options['include'])) ? " AND cat.`id` = " . $options['include'][0] : " AND cat.`id` = " . $options['include'];
				}
			}

			if (isset($options['type']) && $options['type']) {

				if (is_array($options['type'])) {
					$options['type'] = array_unique($options['type']);
				}

				if (is_array($options['type']) && count($options['type']) > 1) {
					$typeCatSQL = " AND cat.`id` IN (" . implode(',', $options['type']) . ")";
				} else {
					$typeCatSQL = (is_array($options['type'])) ? " AND cat.`id` = " . $options['type'][0] : " AND cat.`id` = " . $options['type'];
				}
			}

			if (isset($options['statType']) && $options['statType']) {
				$statCatSQL = " AND cat.`id` = " . $options['statType'];
			}
		}


		# 2863
		if (!$typeCatSQL && !$statCatSQL && !$includeCatSQL && !$excludeCatSQL) {
			// since there is no condition for categories, lets simplify the query further
			$sql = " NOT EXISTS (";
			$sql .= "	select acp.post_id from `#__easyblog_post_category` as acp";
			$sql .= "		inner join  `#__easyblog_category` as cat on acp.`category_id` = cat.`id`";
			$sql .= "	where acp.`post_id` = $columnId";
			$sql .= "	and ( ";
			$sql .= "		( cat.`private` = 1 and (" . $this->my->id . " = 0) ) OR ";
			$sql .= "		( cat.`private` = 2 and (select count(1) from `#__easyblog_category_acl` as cacl where cacl.`category_id` = cat.`id` and cacl.`acl_id` = $acl and cacl.`content_id` IN ($gids)) = 0 )";
			$sql .= "	)";
			$sql .= ")";

			return $sql;
		}

		//starting bracket
		$sql = "1 <= (";

		$sql .= "select count(1) from `#__easyblog_post_category` AS acp";
		$sql .= " INNER JOIN `#__easyblog_category` as cat on acp.`category_id` = cat.`id`";
		$sql .=	" where acp.`post_id` = $columnId";
		$sql .= $typeCatSQL;
		$sql .= $statCatSQL;
		$sql .= $includeCatSQL;
		$sql .= $excludeCatSQL;

		$sql .= " and (";
		$sql .= " 	( cat.`private` = 0 ) OR";
		$sql .= " 	( (cat.`private` = 1) and (" . $this->my->id . " > 0) ) OR";
		$sql .= " 	( (cat.`private` = 2) and ( (select count(1) from `#__easyblog_category_acl` as cacl where cacl.`category_id` = cat.id and cacl.`acl_id` = $acl and cacl.`content_id` in ($gids)) > 0 ) )";
		$sql .= " )";

	// if a post has multiple categories and one of the category is not accessible, we should exclude this post. #790

		$sql .= " and not exists (";
		$sql .= "	select acp2.post_id from `#__easyblog_post_category` as acp2";
		$sql .= "		inner join  `#__easyblog_category` as cat2 on acp2.`category_id` = cat2.`id`";
		$sql .= "	where acp2.`post_id` = acp.`post_id`";
		$sql .= "	and ( ";
		$sql .= "		( cat2.`private` = 1 and (" . $this->my->id . " = 0) ) OR ";
		$sql .= "		( cat2.`private` = 2 and (select count(1) from `#__easyblog_category_acl` as cacl2 where cacl2.`category_id` = cat2.`id` and cacl2.`acl_id` = $acl and cacl2.`content_id` IN ($gids)) = 0 )";
		$sql .= "	)";
		$sql .= " )";

		//ending bracket
		$sql .= " )";

		return $sql;
	}


	/**
	 * generate category filter SQL
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function genBasicSQL($columnId, $options = array())
	{
		$excludeCatSQL = '';
		$includeCatSQL = '';
		$typeCatSQL = '';
		$statCatSQL = '';

		$hasCondition = false;

		if ($options) {
			if (isset($options['exclude']) && $options['exclude']) {

				$hasCondition = true;

				if (is_array($options['exclude'])) {
					$options['exclude'] = array_unique($options['exclude']);
				}

				if (is_array($options['exclude']) && count($options['exclude']) > 1) {
					$excludeCatSQL = " AND cat.`id` NOT IN (" . implode(',', $options['exclude']) . ")";
				} else {
					$excludeCatSQL = (is_array($options['exclude'])) ? " AND cat.`id` != " . $options['exclude'][0] : " AND cat.`id` != " . $options['exclude'];
				}
			}

			if (isset($options['include']) && $options['include']) {

				$hasCondition = true;

				if (is_array($options['include'])) {
					$options['include'] = array_unique($options['include']);
				}

				if (is_array($options['include']) && count($options['include']) > 1) {
					$includeCatSQL = " AND cat.`id` IN (" . implode(',', $options['include']) . ")";
				} else {
					$includeCatSQL = (is_array($options['include'])) ? " AND cat.`id` = " . $options['include'][0] : " AND cat.`id` = " . $options['include'];
				}
			}

			if (isset($options['type']) && $options['type']) {

				$hasCondition = true;

				if (is_array($options['type'])) {
					$options['type'] = array_unique($options['type']);
				}

				if (is_array($options['type']) && count($options['type']) > 1) {
					$typeCatSQL = " AND cat.`id` IN (" . implode(',', $options['type']) . ")";
				} else {
					$typeCatSQL = (is_array($options['type'])) ? " AND cat.`id` = " . $options['type'][0] : " AND cat.`id` = " . $options['type'];
				}
			}

			if (isset($options['statType']) && $options['statType']) {

				$hasCondition = true;

				$statCatSQL = " AND cat.`id` = " . $options['statType'];
			}
		}

		if (!$hasCondition) {
			return false;
		}

		//starting bracket
		$sql = " exists (";

		$sql .= "select acp.`post_id` from `#__easyblog_post_category` AS acp";
		$sql .= " INNER JOIN `#__easyblog_category` as cat on acp.`category_id` = cat.`id`";
		$sql .=	" where acp.`post_id` = $columnId";
		$sql .= $typeCatSQL;
		$sql .= $statCatSQL;
		$sql .= $includeCatSQL;
		$sql .= $excludeCatSQL;

		//ending bracket
		$sql .= " )";

		return $sql;

	}

	/**
	 * generate category access for sql used only in categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function genCatAccessSQL($column, $columnId, $aclQuery = CATEGORY_ACL_ACTION_VIEW, $my = false)
	{
		// We'll need to load user because of mailbox publishing do not logged-in. #2221
		if (!$my) {
			$my = JFactory::getUser();
		}

		$gid = EB::getUserGids();
		$gids = '';
		$db = EB::db();

		// @task: Get the user's specific categories.
		$categoryAcl = EB::table('categoryacl');
		$assignedCategoryCount = $categoryAcl->getUserAssignedCategoryACL($my->id, false, true);

		// @task: Prepare aclQuery.
		$aclQuery = trim($aclQuery);
		$acls = array(CATEGORY_ACL_ACTION_VIEW, CATEGORY_ACL_ACTION_SELECT);

		if (!in_array($aclQuery, $acls)) {
			echo '<pre>'; debug_print_backtrace(-2); echo '</pre>';
			dump('error');
		}

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= $db->Quote($id);
				$gids .= (next($gid)) ? ', ' : '';
			}
		}

		$sql = array();
		$sql[] = '('; // Open bracket 1

		// If no privacy set.
		$sql[] = '(' . $column . ' = 0) OR';

		// If privacy is set to logged-in user.
		$sql[] = '(' . $column . ' = 1 AND ' . $db->Quote($my->id) . ' > 0) OR';

		// If privacy is set to private and category acl is set.
		$sql[] = '(' . $column . ' = 2 AND'; // Open bracket 2
		$sql[] = '('; // Open bracket 3
		$sql[] = 'SELECT COUNT(1) FROM `#__easyblog_category_acl` as ca';
		$sql[] = 'WHERE ca.`category_id` = ' . $columnId;
		// $sql[] = 'AND ca.`acl_id` IN (' . $aclQuery . ')';
		$sql[] = 'AND ca.`acl_id` = ' . $db->Quote($aclQuery);

		if ($assignedCategoryCount > 0 && $aclQuery != CATEGORY_ACL_ACTION_VIEW) {

			$sql[] = 'AND ('; // Open bracket 4
			$sql[] = '(ca.`content_id` = ' . $db->Quote($my->id) . ' AND ca.`type` = ' . $db->Quote('user') . ')';
			$sql[] = ')'; // Close bracket 4

		} else {
			$sql[] = 'AND ('; // Open bracket 4
			$sql[] = '(ca.`content_id` IN (' . $gids . ') AND ca.`type` = ' . $db->Quote('group') . ')';
			$sql[] = 'OR';
			$sql[] = '(ca.`content_id` = ' . $db->Quote($my->id) . ' AND ca.`type` = ' . $db->Quote('user') . ')';
			$sql[] = ')'; // Close bracket 4
		}

		$sql[] = ') > 0'; // Close bracket 3
		$sql[] = ')'; // Close bracket 2
		$sql[] = ')'; // Close bracket 1

		$sql = implode(' ', $sql);

		return $sql;

	}

	public static function addChilds(&$parent, $items)
	{
		// preform safety checking here.
		if (! $items) {
			return false;
		}

		foreach($items as $cItem) {
			if ($cItem->parent_id == $parent->id) {

				$tmpParent = $cItem;
				$tmpParent->childs = array();

				self::addChilds($tmpParent, $items);

				$parent->childs[] = $tmpParent;
			}
		}

		return false;
	}

	public static function validateCategory($categories, $authorId)
	{
		// We'll need to load user because of mailbox publishing. #2221
		$my = JFactory::getUser($authorId);
		$db = EB::db();

		$allowed = new stdClass();
		$allowed->allowed = true;
		$allowed->message = '';

		if (empty($categories)) {
			$allowed->allowed = false;
			$allowed->message = 'COM_EASYBLOG_COMPOSER_UNABLE_STORE_POST';

			return $allowed;
		}

		$categoriesQuery = '';
		foreach ($categories as $category) {
			$categoriesQuery .= $db->Quote($category);
			$categoriesQuery .= (next($categories)) ? ', ' : '';
		}

		$query = array();

		$query[] = 'SELECT cnt.`title`';
		$query[] = 'FROM `#__easyblog_category` AS cnt';
		$query[] = 'WHERE cnt.`id` IN (' . $categoriesQuery . ')';
		$query[] = 'AND cnt.`id` NOT IN (';
		$query[] = 'SELECT a.`id` FROM `#__easyblog_category` as a';

		// @task: Respect against category acl

		$catAccess = self::genCatAccessSQL('a.`private`', 'a.`id`', CATEGORY_ACL_ACTION_SELECT, $my);
		$query[] = 'WHERE (' . $catAccess . ')';
		$query[] = ')';

		$query = implode(' ', $query);

		// echo '-- ' . $my->name . ' new<br/>';
		// echo '<code>' . str_replace('#_', 'jos', $query) . ';</code>';exit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		// @task: Return allowed if there is no category permission set for this category.
		if (empty($result)) {
			return $allowed;
		}

		$plural = (count($result)) > 1 ? JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_COUNT_PLURAL') : JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_COUNT_SINGULAR');
		$noAccessCat = '';

		foreach ($result as $cat) {
			$noAccessCat .= $cat->title;
			$noAccessCat .= (next($result)) ? ', ' : '';
		}

		$allowed->allowed = false;
		$allowed->message = JText::sprintf('COM_EB_COMPOSER_UNABLE_STORE_POST', $noAccessCat, strtolower($plural));

		return $allowed;
	}
}