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

use Joomla\CMS\HTML\HTMLHelper;

class EasyBlogThemesHelperForm
{
	/**
	 * Renders bloggers form like tags picker
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function usertags($name, $value, $id = null, $attributes = [])
	{
		if (is_null($id)) {
			$id = $name;
		}

		$category = EB::normalize($attributes, 'category', '');
		$maxUsers = EB::normalize($attributes, 'maxUsers', EB::getLimit());
		$userTagCount = EB::normalize($attributes, 'userTagCount', 0);

		$theme = EB::themes();
		$theme->set('name', $name);
		$theme->set('usertags', $value);
		$theme->set('category', $category);
		$theme->set('maxUsers', $maxUsers);
		$theme->set('id', $id);
		$theme->set('userTagCount', $userTagCount);

		return $theme->output('site/helpers/form/usertags');
	}

	/**
	 * Renders a custom field group browser form
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function fieldGroups($name, $value, $id = null, $attributes = [])
	{
		if (is_null($id)) {
			$id = $name;
		}

		$groupTitle = '';

		if ($value) {
			// Load the custom field group
			$group = EB::table('FieldGroup');
			$group->load($value);

			$groupTitle = $group->getTitle();
		}

		$attributes = implode(' ', $attributes);

		$theme = EB::themes();
		$theme->set('groupTitle', $groupTitle);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		return $theme->output('site/helpers/form/fieldgroups');
	}

	/**
	 * Renders a team browser form
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function team($name, $value, $id = null)
	{
		if (is_null($id)) {
			$id = $name;
		}

		$teamTitle = '';

		if ($value) {
			$team = EB::table('Teamblog');
			$team->load($value);
			$teamTitle = $team->title;
		}

		$theme = EB::themes();
		$theme->set('teamTitle', $teamTitle);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);

		return $theme->output('site/helpers/form/team');
	}

	/**
	 * Renders the team access dropdown
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function teamAccess($name, $value)
	{
		$options = [
			'1' => 'COM_EASYBLOG_TEAM_MEMBER_ONLY',
			'2' => 'COM_EASYBLOG_ALL_REGISTERED_USERS',
			'3' => 'COM_EASYBLOG_EVERYONE'
		];

		$theme = EB::themes();
		$theme->set('options', $options);
		$theme->set('name', $name);
		$theme->set('value', $value);

		return $theme->output('site/helpers/form/teamaccess');
	}

	/**
	 * Renders a tag browser form
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function tag($name, $value, $id = null)
	{
		if (is_null($id)) {
			$id = $name;
		}

		$title = '';

		if ($value) {
			$tag = EB::table('Tag');
			$tag->load($value);

			$title = JText::_($tag->title);
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);

		return $theme->output('site/helpers/form/tag');
	}

	/**
	 * Renders a category browser form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function browseCategory($name, $value, $id = null, $options = [])
	{
		if (is_null($id)) {
			$id = $name;
		}

		$categoryTitle = '';

		if ($value) {
			$category = EB::table('Category');
			$category->load($value);

			$categoryTitle = JText::_($category->title);
		}

		$columns = FH::normalize($options, 'columns', 10);

		$theme = EB::themes();
		$theme->set('columns', $columns);
		$theme->set('categoryTitle', $categoryTitle);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);

		return $theme->output('site/helpers/form/browse.category');
	}

	/**
	 * Renders a blog post browser form
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function browseBlog($name, $value, $id = null, $attributes = [])
	{
		if (is_null($id)) {
			$id = $name;
		}

		$blogTitle = '';

		if ($value) {
			$blog = EB::table('Post');
			$blog->load($value);

			$blogTitle = JText::_($blog->title);
		}

		$attributes = implode(' ', $attributes);

		$theme = EB::themes();
		$theme->set('blogTitle', $blogTitle);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		return $theme->output('site/helpers/form/blog');
	}

	/**
	 * Generates a dropdown list of categories on the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function category($element, $id = '', $selected = '', $attributes = '', $parentId = null, $exclusion = [])
	{
		// Get the model
		$model = EB::model('Category');

		// Default to filter all categories
		$filter = 'all';

		if (!is_null($parentId)) {
			$filter = 'category';
		}

		// Get list of parent categories
		$categories = $model->getParentCategories($parentId, $filter, true, true, $exclusion);

		// Perform recursive operation to get the child items
		if (!empty($categories)) {

			foreach ($categories as $category) {

				$category->childs = null;

				self::buildNestedCategories($category);
			}
		}

		// Get the selected category
		$selected = $selected ? $selected : $model->getDefaultCategoryId();
		$categoryOptions = [
			'0' => 'COM_EASYBLOG_SELECT_A_CATEGORY'
		];

		foreach ($categories as $category) {
			$categoryOptions[$category->id] = $category->title;

			self::generateNestedCategoriesOutput($category, $categoryOptions, $selected);
		}

		// Now we need to build the select output
		$themes = EB::themes();
		$themes->set('element', $element);
		$themes->set('attributes', $attributes);
		$themes->set('id', $id);
		$themes->set('categoryOptions', $categoryOptions);
		$themes->set('selected', $selected);

		$html = $themes->output('site/helpers/form/category');

		return $html;
	}

	public static function buildNestedCategories($category, $exclusion = array(), $writeOnly = true, $publishedOnly = true)
	{
		$model = EB::model('Category');
		$categories = $model->getChildCategories($category->id, $publishedOnly, $writeOnly, $exclusion);

		// Get accessible categories
		// $accessibleCategories = EB::getAccessibleCategories($category->id);

		if (!empty($categories)) {

			foreach ($categories as &$row) {

				$row->childs = null;

				if (!self::buildNestedCategories($row)) {
					$category->childs[] = $row;
				}
			}
		}

		return false;
	}

	/**
	 * Generates a list of category options in a select list
	 *
	 * @since   4.0
	 * @access  public
	 */
	public static function generateNestedCategoriesOutput($category, &$categoryOptions, $selected = 0, $depth = 0)
	{
		if (!is_array($category->childs)) {
			return false;
		}

		// Increment the depth
		$depth++;

		$prefix = '';

		for ($i = 0; $i < $depth; $i++) {
			$prefix .= '&nbsp;&nbsp;&nbsp;';
		}

		foreach ($category->childs as $child) {

			$categoryOptions[$child->id] = $prefix . '<sup>|_</sup>' . JText::_($child->title);

			// Try to build the nested items
			self::generateNestedCategoriesOutput($child, $categoryOptions, $selected, $depth);
		}
	}

	/**
	 * Displays dropdown list for the Facebook scopes permission
	 *
	 * @since	5.2.10
	 * @access	public
	 */
	public function scopes($name, $id, $selected = null, $autopostType = 'facebook')
	{
		$defaultScopes = [];

		// Get the list of Facebook scope permission
		$scopes = [
			'publish_pages' => 'publish_pages',
			'manage_pages' => 'manage_pages',
			'pages_manage_posts' => 'pages_manage_posts',
			'pages_read_engagement' => 'pages_read_engagement',
			'publish_to_groups' => 'publish_to_groups'
		];

		if ($autopostType === 'linkedin') {
			$scopes = array(
						'r_liteprofile' => 'r_liteprofile',
						'r_emailaddress' => 'r_emailaddress',
						'w_member_social' => 'w_member_social',
						'rw_organization_admin' => 'rw_organization_admin',
						'w_organization_social' => 'w_organization_social',
						'r_organization_social' => 'r_organization_social'
					);

			$defaultScopes = ['r_liteprofile', 'r_emailaddress', 'w_member_social'];
		}

		$theme = EB::themes();
		$theme->set('name', $name);
		$theme->set('scopes', $scopes);
		$theme->set('id', $id);
		$theme->set('selected', $selected);
		$theme->set('defaultScopes', $defaultScopes);

		$output = $theme->output('site/helpers/form/scopes');

		return $output;
	}
}
