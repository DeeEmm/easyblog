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

class EasyBlogViewLatest extends EasyBlogView
{
	/**
	 * This method would be invoked by the parent to set any params
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function defineParams()
	{
		// Get the current active menu's properties.
		$params = $this->getActiveMenuParams('listing');

		return $params;
	}

	/**
	 * Displays the frontpage blog listings on the site.
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function loadmore($tmpl = null)
	{
		$catInclusion = EB::getCategoryInclusion($this->params->get('inclusion'));

		if ($this->params->get('includesubcategories', 0) && !empty($catInclusion)) {
			$catInclusion = EB::category()->getAllSubcategories($catInclusion);
		}

		// Sorting for the posts
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');
		$model = EB::model('Blog');

		$tobeCached = [];

		// Retrieve a list of featured blog posts on the site.
		$featured = $model->getFeaturedBlog($catInclusion);
		$excludeIds = [];

		// Test if user also wants the featured items to be appearing in the blog listings on the front page.
		// Otherwise, we'll need to exclude the featured id's from appearing on the front page.
		if (!$this->theme->params->get('post_include_featured', true)) {
			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		if ($featured) {
			$tobeCached = array_merge($tobeCached, $featured);
		}

		$excludedCategories = [];

		if ($this->params->get('exclusion_categories', false)) {
			$excludedCategories = $this->params->get('exclusion_categories');
		} else {
			// upgrades compatibility
			$tmpExcludeCategories = $this->config->get('layout_exclude_categories', null);

			if ($tmpExcludeCategories) {
				$excludedCategories	= explode( ',' , $tmpExcludeCategories );
			}
		}

		// Determines if we should explicitly include authors
		$includeAuthors = [];

		if ($this->params->get('inclusion_authors', false)) {
			$includeAuthors = $this->params->get('inclusion_authors');
		}

		// Determines if we should explicitly exclude authors
		$excludeAuthors = [];

		if ($this->params->get('exclusion_authors', false)) {
			$excludeAuthors = $this->params->get('exclusion_authors');
		}

		// Determines if we should explicitly include tags
		$includeTags = [];

		if ($this->params->get('inclusion_tags', false)) {
			$includeTags = $this->params->get('inclusion_tags');
		}

		// Check if this is filter by custom field
		$filter = $this->input->get('filter', false);
		$fields = [];
		$options = [];

		// Check if this user has saved filter search before
		$filterSaved = EB::model('fields')->getSavedFilter();

		if ($filter == 'field') {
			$filterVars = $this->input->input->getArray();
			$filterMode = $this->input->get('filtermode', 'include');

			foreach ($filterVars as $key => $value) {

				if (strpos($key, 'field') !== false) {
					$fieldId = explode('-', $key);
					$fieldId = $fieldId[1];

					$fields[$fieldId] = $filterVars[$key];

				}
			}
			// If there is a category inclusion from the field filter,
			// We override the existing cat inclusion
			$inclusion = $this->input->get('inclusion', false);

			if ($inclusion) {
				$catInclusion = $inclusion;
			}

			$options['fieldsFilterRule'] = $filterMode;
			$options['fieldsFilter'] = $fields;
		} else if ($filterSaved) {
			$this->params = json_decode($filterSaved->params);

			foreach ($this->params as $filter) {
				if (strpos($filter->name, 'field') !== false) {
					$fieldId = explode('-', $filter->name);
					$fieldId = $fieldId[1];

					$fields[$fieldId][] = $filter->value;
				}

				if ($filter->name == 'inclusion') {
					$catInclusion = $filter->value;
				}

				if ($filter->name == 'filtermode') {
					$options['fieldsFilterRule'] = $filter->value;
				}
			}

			$options['fieldsFilter'] = $fields;
		}

		$limitstart = $this->input->get('limitstart', 0, 'int');
		$isGrid = $this->input->get('isGrid', false, 'default');
		$originalLimit = EB::getViewLimit();

		$max = $originalLimit + 1;

		$options['paginationType'] = 'loadmore';

		$frontpage = true;
		$pinFeatured = $this->params->get('post_pin_featured', false);

		// If is grid, we need to follow the way how grid gets the blog posts
		if ($isGrid == 'true') {
			$sort = '';
			$frontpage = false;
			$excludedCategories= '';
			$pinFeatured = false;
			$includeTags = array();

			$excludeIds = array();

			if ($this->params->get('enable_showcase', 1)) {
				// need to exclude the post id that is in the showcase
				$excludeIds = $this->input->get('excludeIds', array(), 'array');
			}

			// Retrieve the post categories
			$catInclusion = $this->params->get('grid_post_category', array());

			// Format postCategories if user selected all category.
			if ($catInclusion) {
				$catInclusion = array_diff($catInclusion, array('all'));
			}
		}

		// Fetch the blog entries.
		$data = $model->getBlogsBy('', '', $sort, 0, EBLOG_FILTER_PUBLISHED, null, $frontpage, $excludeIds, false, false, true, $excludedCategories, $catInclusion, null, 'listlength', $pinFeatured, $includeAuthors, $excludeAuthors, false, $includeTags, $options);

		$showLoadMore = false;

		if ($data) {

			if (count($data) == $max) {
				$showLoadMore = true;

				// Take out the last post
				array_pop($data);
			}

			$tobeCached = array_merge($tobeCached, $data);
		}

		// There is really no need to cache anything here since this is an ajax call and each request is a new request
		$options = [
			'cacheComment' => false,
			'cacheCommentCount' => false,
			'cacheRatings' => false,
			'cacheTags' => false,
			'cacheAuthors' => true,
			'loadAuthor' => true
		];

		// we will cache it here.
		if ($tobeCached) {
			EB::cache()->insert($tobeCached, $options);
		}

		$view = $isGrid == 'true' ? 'grid' : 'latest';

		// Get the pagination
		$pagination	= $model->getPagination();
		$currentPageLink = $pagination->getCurrentPageLink($view, true);

		if ($featured) {
			// Format the featured items without caching
			$featured = EB::formatter('featured', $featured, false, $options);
		}

		$options['viaAjax'] = true;

		// Perform blog formatting without caching
		$posts = EB::formatter('list', $data, false, $options);

		$postStyles = EB::getPostStyles($this->params);

		$themeNS = 'site/listing/wrapper';
		if ($isGrid == 'true') {
			$themeNS = 'site/grid/default/posts';
		}

		$theme = EB::themes();
		$theme->setParams($this->params);
		$theme->set('posts', $posts);
		$theme->set('return', $currentPageLink);
		$theme->set('currentPageLink', $currentPageLink);
		$theme->set('autoload', true);
		$theme->set('postStyles', $postStyles);

		$output = $theme->output($themeNS);

		if ($showLoadMore) {
			$limitstart = $limitstart + $originalLimit;
		}

		$data = new stdClass();
		$data->contents = $output;
		$data->limitstart = $showLoadMore ? $limitstart : '';

		$data = json_encode($data);

		return $this->ajax->resolve($data);
	}
}
