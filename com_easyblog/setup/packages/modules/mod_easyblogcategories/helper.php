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

class modEasyBlogCategoriesHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	/**
	 * Normalize the layout file name
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getLayoutFile()
	{
		$layout = $this->params->get('layouttype', 'tree');

		// Flatmain seems to no longer exists since 5.0.
		// We should just normalize the value
		if ($layout == 'flat' || $layout == 'flatmain' || $layout == 'tree') {
			return 'tree';
		}

		return $layout;
	}

	/**
	 * Normalize the category id from the module settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCategoryIds()
	{
		$categories = $this->params->get('catid', '');

		if (!empty($categories)) {

			if (is_string($categories)) {
				$categories = explode(',', $categories);
			}
		}

		return $categories;
	}

	/**
	 * Retrieve categories with the module parameters
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCategories()
	{
		$layout = $this->params->get('layouttype', 'tree');
		$ordering = $this->params->get('order', 'latest');
		$limit = (int) $this->params->get('count', 0);
		$hideEmptyPost = $this->params->get('hideemptypost', false);
		$categoryIds = $this->getCategoryIds();

		// in this module context, 0 means all
		if ($limit === 0) {
			$limit = 'all';
		}

		$model = EB::model('Category');

		// Retrieve parent categories
		$parent = $model->getCategories($ordering, $hideEmptyPost, $limit, $categoryIds, false);
		$categories = array();

		// Retrieve child categories
		$this->getChildCategories($parent, $categories);

		return $categories;
	}

	/**
	 * Retrieves a list of child categories given the set of categories provided
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getChildCategories(&$result, &$categories, $level = 1)
	{
		$db = EB::db();

		$ordering = $this->params->get('order', 'popular');
		$sort = 'desc';
		$total = (int) $this->params->get('count', 0);
		$hideEmptyPost = $this->params->get('hideemptypost', false);
		$language = EB::getCurrentLanguage();

		foreach ($result as $row) {

			// Initialize default structure
			$category = EB::table('Category');
			$category->bind($row);
			$category->cnt = $row->cnt;

			$categories[$row->id] = $category;
			$categories[$row->id]->childs = array();

			// Find child categories
			$query = array();
			$query[] = 'SELECT a.*, COUNT(' . $db->qn('b.id') . ') AS ' . $db->qn('cnt') . ',' . $db->Quote($level) . ' AS ' . $db->qn('level');
			$query[] = 'FROM ' . $db->qn('#__easyblog_category') . ' AS a';
			$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_post_category') . ' AS pc';
			$query[] = 'ON ' . $db->qn('a.id') . '=' . $db->qn('pc.category_id');
			$query[] = 'LEFT JOIN ' . $db->qn('#__easyblog_post') . ' AS b';
			$query[] = 'ON ' . $db->qn('b.id') . '=' . $db->qn('pc.post_id');
			$query[] = 'AND ' . $db->qn('b.published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query[] = 'AND ' . $db->qn('b.state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
			$query[] = 'WHERE ' . $db->qn('a.published') . '=' . $db->Quote(1);
			$query[] = 'AND ' . $db->qn('parent_id') . '=' . $db->Quote($row->id);

			if ($language) {
				$query[] = 'AND(';
				$query[] = $db->qn('a.language') . '=' . $db->Quote($language);
				$query[] = 'OR';
				$query[] = $db->qn('a.language') . '=' . $db->Quote('');
				$query[] = 'OR';
				$query[] = $db->qn('a.language') . '=' . $db->Quote('*');
				$query[] = ')';
			}

			if (!$hideEmptyPost) {
				$query[] = 'GROUP BY ' . $db->qn('a.id');
			} else {
				$query[] = 'GROUP BY ' . $db->qn('a.id') . ' HAVING (COUNT(' . $db->qn('b.id') . ') > 0)';
			}

			if ($ordering == 'ordering') {
				$query[] = ' ORDER BY `lft` desc';
			}

			if ($ordering == 'popular') {
				$query[] = ' ORDER BY `cnt` desc';
			}

			if ($ordering == 'alphabet') {
				$query[] = ' ORDER BY a.`title` asc';
			}

			if ($ordering == 'latest') {
				$query[] = ' ORDER BY a.`created` desc';
			}


			$query = implode(' ', $query);

			$db->setQuery($query);
			$children = $db->loadObjectList();


			// Recursion happens here
			if ($children) {
				$this->getChildCategories($children, $categories[$row->id]->childs, ++$level);
			}
		}
	}

	/**
	 * Get currently viewed category if the user is viewing a particular category page
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCurrentViewedCategory()
	{
		static $category = null;

		if (is_null($category)) {
			$category = false;

			$view = $this->input->get('view');
			$layout = $this->input->get('layout');
			$id = $this->input->get('id', 0);

			if ($view == 'categories' && $layout == 'listings' && $id) {
				$category = $id;
			}
		}

		return $category;
	}

	/**
	 * Renders the tree layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTreeOutput(&$categories, $level = null)
	{
		// Simulate params variable
		$params = $this->params;
		$showCategoryAvatar = $this->params->get('showcavatar', true);
		$showCount = $this->params->get('showcount', true);
		$padding = 0;

		foreach ($categories as $category) {

			$selected = $this->getCurrentViewedCategory();

			if (is_null($level)) {
				$level 	= 0;
			}

			$css = '';

			if ($category->id == $selected) {
				$css = 'font-weight: bold;';
			}

			if ($this->params->get('layouttype') == 'tree') {
				$padding = $level * 30;
			}

			require(JModuleHelper::getLayoutPath('mod_easyblogcategories', 'default_tree_item'));

			// For child items, we need to call itself recursively
			if (isset($category->childs) && is_array($category->childs)) {

				// Only reverse the ordering when we are ordering by ordering
				$sortItems = $params->get('order', 'popular') == 'ordering';

				if ($sortItems) {
					$category->childs = array_reverse($category->childs);
				}

				$this->getTreeOutput($category->childs, $level + 1);
			}
		}
	}

	/**
	 * Renders the nested category child items
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getToggleOutput(&$categories, $isChild = false)
	{
		$params = $this->params;
		$showCount = $params->get('showcount', true);
		$showCategoryAvatar = $this->params->get('showcavatar', true);
		$helper = $this;

		// We only want to re-sort the items when the ordering is by column ordering
		$sortItems = $params->get('order', 'popular') == 'ordering';

		if ($isChild && $sortItems) {
			$categories = array_reverse($categories);
		}

		require(JModuleHelper::getLayoutPath('mod_easyblogcategories', 'default_toggle_items'));
	}
}
