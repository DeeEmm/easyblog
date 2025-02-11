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

	public function display($tmpl = null)
	{
		$this->setRssFeed('index.php?option=com_easyblog');
		$this->setPathway('COM_EASYBLOG_LATEST_BREADCRUMB');

		// Inclusion of categories
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
		$excludeFeaturedPosts = false;

		// Test if user also wants the featured items to be appearing in the blog listings on the front page.
		// Otherwise, we'll need to exclude the featured id's from appearing on the front page.
		$includeFeaturedPosts = $this->params->get('post_include_featured', false);

		if (!$includeFeaturedPosts) {
			$excludeFeaturedPosts = true;
		}

		// Admin might want to display the featured blogs on all pages.
		$start = $this->input->get('start', 0, 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		if (!$this->params->get('featured_slider_all_pages') && ($start != 0 || $limitstart != 0)) {
			$featured = array();
		}

		if ($featured) {
			$tobeCached = array_merge($tobeCached, $featured);
		}

		// Backward compatibility for EB5
		$excludedCategories = EB::getExcludedLegacyCategories();

		if ($this->params->get('exclusion_categories', false)) {
			$excludedCategories = $this->params->get('exclusion_categories');
		}

		// Determines if we should explicitly include authors
		$includeAuthors = $this->params->get('inclusion_authors', []);

		// Determines if we should explicitly exclude authors
		$excludeAuthors = $this->params->get('exclusion_authors', []);

		// Determines if we should explicitly include tags
		$includeTags = $this->params->get('inclusion_tags', []);

		// Check if this is filter by custom field
		$filter = $this->input->get('filter', false);
		$fields = [];
		$options = [];

		// Check if this user has saved filter search before
		$filterSaved = EB::model('fields')->getSavedFilter();

		if ($filter == 'field') {
			$filterVars = $this->input->input->getArray();
			$filterMode = $this->input->get('filtermode', 'include');
			$strictMode = $this->input->get('strictmode', false, 'bool');

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
			$options['strictMode'] = $strictMode;
		}

		if ($filter !== 'field' && $filterSaved) {
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

		$max = 0;
		$limit = EB::getViewLimit();

		// Determine if loadmore should be shown by adding one extra limit into the query
		$paginationStyle = $this->params->get('pagination_style', 'normal');

		if ($paginationStyle == 'autoload') {
			$max = $limit + 1;
			$options['paginationType'] = 'loadmore';
		}

		// Fetch the blog entries.
		$data = $model->getBlogsBy('', '', $sort, 0, EBLOG_FILTER_PUBLISHED, null, true, $excludeIds, false, false, true, $excludedCategories, $catInclusion, null, 'listlength', $this->params->get('post_pin_featured', false),
					$includeAuthors, $excludeAuthors, $excludeFeaturedPosts, $includeTags, $options);

		$showLoadMore = false;

		if ($data) {

			if ($paginationStyle == 'autoload' && count($data) == $max) {
				$showLoadMore = true;

				// Take out the last post
				array_pop($data);
			}

			$tobeCached = array_merge($tobeCached, $data);
		}

		// Format the blog posts
		$options = [
			'cacheComment' => (bool) $this->params->get('post_comment_preview', 0),
			'cacheCommentCount' => (bool) $this->params->get('post_comment_counter', 0),
			'cacheRatings' => (bool) $this->params->get('post_ratings', 0),
			'cacheTags' => (bool) $this->params->get('post_tags', 0),
			'cacheAuthors' => false,
			'loadAuthor' => false
		];

		if ($this->params->get('post_author', 0) || $this->params->get('post_author_avatar', 0)) {
			$options['cacheAuthors'] = true;
			$options['loadAuthor'] = true;
		}

		// we will cache it here.
		if ($tobeCached) {
			EB::cache()->insert($tobeCached, $options);
		}

		// Get the pagination
		$pagination	= $model->getPagination();
		$paginationLink = $pagination->getPagesLinks();
		$currentPageLink = $pagination->getCurrentPageLink('latest', true);

		// Format the featured items without caching
		if ($featured) {
			$featured = EB::formatter('featured', $featured, false, $options);
		}

		// Perform blog formatting without caching
		$posts = EB::formatter('list', $data, false, $options);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_LATEST_PAGE_TITLE'));

		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		$this->setCanonicalLink($limitstart);

		// Meta should be set later because formatter would have cached the post already.
		EB::setMeta(META_ID_LATEST, META_TYPE_VIEW, '', $pagination);

		$return = EBR::current();
		$limitstart = $limitstart + $limit;

		$postStyles = EB::getPostStyles($this->params);

		$this->set('postStyles', $postStyles);
		$this->set('return', $return);
		$this->set('posts', $posts);
		$this->set('featured', $featured);
		$this->set('showLoadMore', $showLoadMore);
		$this->set('limitstart', $limitstart);
		$this->set('pagination', $paginationLink);
		$this->set('currentPageLink', $currentPageLink);

		parent::display('latest/default/default');
	}

	/**
	 * Prepares the canonical link to be generated for the view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function setCanonicalLink($limitstart)
	{
		// Add canonical URLs.
		$route = true;
		$joiner = '&';
		$link = 'index.php?option=com_easyblog&view=latest';

		// Check if we have custom canonical link for this page or not.
		$custom = $this->params->get('seo_custom_canonical', '');

		if ($custom) {
			$route = false;
			$link = $custom;

			if (strpos('?', $link) === false) {
				$joiner = '?';
			}
		}

		$link .= ($limitstart) ? $joiner . 'limitstart=' . $limitstart : '';

		$this->canonical($link, $route);
	}
}
