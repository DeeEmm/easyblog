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

require_once(EB_ROOT . '/views/views.php');

class EasyBlogViewCategories extends EasyBlogView
{
	/**
	 * Retrieve categories used in the search filter
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCategoriesForFilters()
	{
		// Check for tokens
		FH::checkToken();
		
		$parentId = $this->input->get('parentId', 0, 'int');
		$activeId = $this->input->get('activeId', 0, 'int');

		$categories = EB::getCategoriesForFilters($parentId);

		$theme = EB::themes();
		$theme->set('rootLevel', false);
		$theme->set('activeCategoryId', $activeId);
		$theme->set('categories', $categories);

		$output = $theme->output('site/search/category.nested');
		return $this->ajax->resolve($output);
	}

	/**
	 * Retrieve custom fields based on the category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCustomFields()
	{
		// Get the category id
		$id = $this->input->get('id', 0, 'int');
		$postId = $this->input->get('postId', 0, 'int');

		$post = EB::post($postId);

		// Load up the model
		$model = EB::model('Categories');

		// Retrieve the custom field group since each category can only have 1 group
		$group = $model->getCustomFieldGroup($id);

		$groupId = false;

		if ($group) {
			$groupId = $group->id;
		}

		// Retrieve the custom fields forms
		$fields = $model->getCustomFields($id);

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('id', $id);
		$theme->set('group', $group);
		$theme->set('fields', $fields);

		$output = $theme->output('site/composer/panels/fields/form');

		return $this->ajax->resolve($output, $groupId);
	}

	/**
	 * Retrieve a list of categories a user is allowed to post into
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCategories()
	{
		// Get the id to lookup for
		$id = $this->input->get('id', 0, 'int');

		$model  = EB::model('Categories');
		$result = $model->getCategoriesHierarchy();
		$default = '';

		if ($result) {
			foreach ($result as $row) {

				$category = new stdClass();
				$category->id = (int) $row->id;
				$category->title = $row->title;
				$category->parent_id = (int) $row->parent_id;

				$params = new JRegistry($row->params);

				$category->tags = $params->get('tags');

				if ($row->default) {
					$default = $row->id;
				}

				$categories[] = $category;
			}
		}


		return $this->ajax->resolve($categories, $default);
	}

	/**
	 * Retrieve a list of active authors of a category
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getAuthors()
	{
		// Get the id to lookup for
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->resolve('');
		}

		$model = EB::model('Category');
		$results = $model->getActiveAuthors($id);

		if (!$results) {
			return $this->ajax->resolve('');
		}

		// Get the default pagination limit for authors
		$limitAuthor = EB::getViewLimit('categories_author_limit', 'categories');
		$limitAuthor = $limitAuthor == 0 ? 5 : $limitAuthor;

		$theme = EB::themes();
		$theme->set('authors', $results);
		$theme->set('limitAuthor', $limitAuthor);

		$output = $theme->output('site/categories/default/authors');

		return $this->ajax->resolve($output);
	}


	/**
	 * Retrieve a list of categories a user is allowed to post into
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getChildCategories()
	{
		// Get the id to lookup for
		$id = $this->input->get('id', 0, 'int');
		$postId = $this->input->get('postId', 0, 'int');


		$categories = array();

		if ($id && $postId) {

			$post = EB::post($postId);

			// Get the default category.
			$defaultCategoryId = EB::model('Category')->getDefaultCategoryId();

			// Allow caller to alter default category
			$primaryCategory = null;

			if ($post->isBlank() && $defaultCategoryId) {
				$primaryCategory = EB::table('Category');
				$primaryCategory->load($defaultCategoryId);
			} else {
				$primaryCategory = $post->getPrimaryCategory();
			}

			// If the menu has a default category, the primary category should be the pre-selected one.
			// And this shouldn't happening on draft post because user no need to re-configure which category should set as primary again
			if (!$primaryCategory && !$post->isFromFeed() && $post->isNew() && !$post->isDraft() && $defaultCategoryId && !$post->isScheduled()) {
				$primaryCategory = EB::table('Category');
				$primaryCategory->load($defaultCategoryId);
			}

			// Get a list of categories
			// Prepare selected category
			$selectedCategories = array();
			$selectedCategoriesId = array();


			foreach ($post->getCategories() as $row) {

				$cat = new stdClass();

				$cat->id = $row->id;
				$cat->lft = $row->lft;
				$cat->rgt = $row->rgt;

				$selectedCategories[] = $cat;
				$selectedCategoriesId[] = $row->id;
			}

			// if there is no category selected, or this is a new blog post, lets use the default category id.
			if (!$selectedCategories && $defaultCategoryId) {

				$defaultCategory = EB::table('Category');
				$defaultCategory->load($defaultCategoryId);

				$cat = new stdClass();

				$cat->id = $defaultCategory->id;
				$cat->lft = $defaultCategory->lft;
				$cat->rgt = $defaultCategory->rgt;

				$selectedCategories[] = $cat;
				$selectedCategoriesId[] = $defaultCategory->id;
			}

			$model  = EB::model('Categories');
			$result = $model->getImmediateChildCategories($id);
			$default = '';

			if ($result) {
				foreach ($result as $row) {

					$category = new stdClass();
					$category->id = (int) $row->id;
					$category->title = JText::_($row->title);
					$category->parent_id = (int) $row->parent_id;
					$category->childs = (int) $row->childs;

					$category->lft = $row->lft;
					$category->rgt = $row->rgt;

					$params = new JRegistry($row->params);
					$category->tags = $params->get('tags');

					if (!$category->tags) {
						$category->tags = array();
					} else {
						$tags = explode(',', $category->tags);
						for ($i = 0; $i < count($tags); $i++) {
							$tags[$i] = EBString::trim($tags[$i]);
						}
						$category->tags = implode(',', $tags);
					}

					// Cross check if this category is selected
					$category->selected = in_array($category->id, $selectedCategoriesId);

					// check if this is a primary category or not
					$category->isprimary = $category->id == $primaryCategory->id;

					$category->hadChildSelectedCount = 0;

					foreach ($selectedCategories as $selected) {
						if ($selected->lft > $category->lft && $selected->rgt < $category->rgt) {
							$category->hadChildSelectedCount++;
						}
					}


					$categories[] = $category;
				}
			}
		}

		return $this->ajax->resolve($categories);
	}

	/**
	 * Retrieve a list of categories based in the keyword
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function search()
	{
		$keyword = $this->input->get('keyword', '', 'default');
		$postId = $this->input->get('postId', 0, 'int');

		if (! $keyword) {
			return $this->ajax->resolve('');
		}

		$post = EB::post($postId);

		// Get the default category.
		$defaultCategoryId = EB::model('Category')->getDefaultCategoryId();

		// Allow caller to alter default category
		$primaryCategory = null;

		if ($post->isBlank() && $defaultCategoryId) {
			$primaryCategory = EB::table('Category');
			$primaryCategory->load($defaultCategoryId);
		} else {
			$primaryCategory = $post->getPrimaryCategory();
		}

		// If the menu has a default category, the primary category should be the pre-selected one.
		// And this shouldn't happening on draft post because user no need to re-configure which category should set as primary again
		if (!$primaryCategory && !$post->isFromFeed() && $post->isNew() && !$post->isDraft() && $defaultCategoryId && !$post->isScheduled()) {
			$primaryCategory = EB::table('Category');
			$primaryCategory->load($defaultCategoryId);
		}

		// Get a list of categories
		// Prepare selected category
		$selectedCategories = array();
		$selectedCategoriesId = array();


		foreach ($post->getCategories() as $row) {

			$cat = new stdClass();

			$cat->id = $row->id;
			$cat->lft = $row->lft;
			$cat->rgt = $row->rgt;

			$selectedCategories[] = $cat;
			$selectedCategoriesId[] = $row->id;
		}

		// if there is no category selected, or this is a new blog post, lets use the default category id.
		if (!$selectedCategories && $defaultCategoryId) {

			$defaultCategory = EB::table('Category');
			$defaultCategory->load($defaultCategoryId);

			$cat = new stdClass();

			$cat->id = $defaultCategory->id;
			$cat->lft = $defaultCategory->lft;
			$cat->rgt = $defaultCategory->rgt;

			$selectedCategories[] = $cat;
			$selectedCategoriesId[] = $defaultCategory->id;
		}

		$categories = array();
		$model = EB::model('Categories');
		$results = $model->searchCategories($keyword, array('ignoreParent' => true));

		if ($results) {
			foreach ($results as $row) {

				$category = new stdClass();
				$category->id = (int) $row->id;
				$category->title = $row->title;
				$category->parent_id = (int) $row->parent_id;
				$category->childs = (int) $row->childs;

				$category->lft = $row->lft;
				$category->rgt = $row->rgt;

				$params = new JRegistry($row->params);
				$category->tags = $params->get('tags');

				if (!$category->tags) {
					$category->tags = array();
				} else {
					$tags = explode(',', $category->tags);
					for ($i = 0; $i < count($tags); $i++) {
						$tags[$i] = EBString::trim($tags[$i]);
					}
					$category->tags = implode(',', $tags);
				}

				// Cross check if this category is selected
				$category->selected = in_array($category->id, $selectedCategoriesId);

				// check if this is a primary category or not
				$category->isprimary = $category->id == $primaryCategory->id;

				$category->hadChildSelectedCount = 0;

				foreach ($selectedCategories as $selected) {
					if ($selected->lft > $category->lft && $selected->rgt < $category->rgt) {
						$category->hadChildSelectedCount++;
					}
				}


				$categories[] = $category;
			}
		}

		return $this->ajax->resolve($categories);
	}

	/**
	 * Confirmation dialog to publish the category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmPublishCategory()
	{
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/categories/dialogs/publish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation dialog to unpublish the category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmUnpublishCategory()
	{
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/categories/dialogs/unpublish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation dialog to delete the category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDeleteCategory()
	{
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$output = $theme->output('site/dashboard/categories/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation box to toggle default state of the category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmSetDefault()
	{
		$ids = $this->input->get('ids', array(), 'array');

		// Get the first id
		$id = $ids[0];

		$theme = EB::themes();
		$theme->set('id', $id);
		$output = $theme->output('site/dashboard/categories/dialogs/default');

		return $this->ajax->resolve($output);
	}
}
