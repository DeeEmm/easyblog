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

class EasyBlogViewCategories extends EasyBlogView
{
	/**
	 * This method would be invoked by the parent to set any params
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function defineParams()
	{
		$layout = $this->app->input->get('layout', '', 'cmd');
		$activeMenu = $this->getActiveMenu();

		// No active menu, we just return the default
		if (!$activeMenu) {
			return;
		}

		$segments = $activeMenu->query;

		if ($layout !== 'listings') {

			// Default params that would be retrieved from the xml file
			$params = EB::getMenuParams(null, 'categories');

			// If the current menu being accessed is associated to this view, retrieve its params
			if ($segments['view'] == 'categories' && !isset($segments['layout'])) {
				$params = $this->getActiveMenuParams('categories');
			}

			return $params;
		}

		$id = $this->app->input->get('id', 0, 'int');

		if ($layout === 'listings' && $id) {
			$params = EB::getMenuParams($id, 'category');

			return $params;
		}
	}

	/**
	 * Renders the all categories page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// If the active menu is this view, we should not make the breadcrumb linkable.
		if (!EBR::isCurrentActiveMenu('categories')) {
			$this->setPathway(JText::_('COM_EASYBLOG_CATEGORIES_BREADCRUMB'), '');
		}

		// Sorting options
		$defaultSorting = $this->config->get('layout_sorting_category', 'latest');
		$sort = $this->input->get('sort', $defaultSorting, 'cmd');

		$model = EB::model('Category');

		$inclusion = EB::getCategoryInclusion($this->params->get('inclusion'));
		$limit = EB::getViewLimit('categories_limit', 'categories');
		$categories = $model->getCategories($sort, $this->config->get('main_categories_hideempty'), $limit, $inclusion);
		$pagination	= $model->getPagination();

		// Set meta tags for bloggers
		EB::setMeta(META_ID_GATEGORIES, META_TYPE_VIEW, '', $pagination);

		$pagination = $pagination->getPagesLinks();
		$showPosts = $this->params->get('category_posts');
		$showAuthors = $this->params->get('category_authors');

		// Format the categories
		$options = [
			'cachePosts' => $showPosts,
			'cacheAuthors' => false,
			'cacheAuthorsCount' => $showAuthors,
			'params' => $this->params
		];

		$categories = EB::formatter('categories', $categories, true, $options);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_CATEGORIES_PAGE_TITLE'));
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URLs.
		$limitstart = $this->input->get('limitstart', 0, 'int');
		$canoLink = 'index.php?option=com_easyblog&view=categories';
		$canoLink .= ($limitstart) ? '&limitstart=' . $limitstart : '';
		$this->canonical($canoLink);

		// Get the default pagination limit for authors
		$limitPreviewAuthor = EB::getViewLimit('categories_author_limit', 'categories');
		$limitPreviewAuthor = $limitPreviewAuthor == 0 ? 5 : $limitPreviewAuthor;

		// Get the post preview title limit
		$limitPreviewPost = EB::getViewLimit('categories_post_limit', 'categories');
		$limitPreviewPost = $limitPreviewPost == 0 ? 5 : $limitPreviewPost;

		$totalTabs = 0;

		if ($this->params->get('category_posts', true)) {
			$totalTabs += 1;
		}

		if ($this->params->get('category_authors', true)) {
			$totalTabs += 1;
		}

		$this->set('totalTabs', $totalTabs);
		$this->set('limit', $limit);
		$this->set('limitPreviewPost', $limitPreviewPost);
		$this->set('limitPreviewAuthor', $limitPreviewAuthor);
		$this->set('categories', $categories);
		$this->set('sort', $sort);
		$this->set('pagination', $pagination);

		parent::display('categories/default/default');
	}

	/**
	 * Displays a list of blog posts on the site filtered by a category.
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function listings()
	{
		$id = $this->input->get('id', 0, 'int');

		// Try to load the category
		$category = EB::table('Category');
		$category->load($id);

		// If the category isn't found on the site throw an error.
		if (!$id || !$category->id || !$category->isPublished()) {
			throw EB::exception(JText::_('COM_EASYBLOG_CATEGORY_NOT_FOUND'), 404);
		}

		// Validate whether the current viewer can able to access this category page under current site language
		$this->validateMultilingualCategoryAccess($category);

		EB::cache()->set($category, 'category');

		// Set a canonical link for the category page.
		$this->canonical($category->getExternalPermalink(null, true), false);

		// Get the privacy
		$privacy = $category->checkPrivacy();

		if ($this->config->get('main_rss') && ($privacy->allowed || FH::isSiteAdmin() || (!$this->my->guest && $this->config->get('main_allowguestsubscribe')))) {
			$this->doc->addHeadLink($category->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			$this->doc->addHeadLink($category->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
		}

		// Get the category model
		$model = EB::model('Category');

		$isCurrentActiveSingleCatMenu = EBR::isCurrentActiveMenu('categories', $category->id);

		// Set the breadcrumb for this category
		if (!$isCurrentActiveSingleCatMenu) {

			$hasChildParentCategories = $model->getChildParentCategories($category->parent_id);

			if ($hasChildParentCategories) {

				// reverse category order here
				$hasChildParentCategories = array_reverse($hasChildParentCategories);

				foreach ($hasChildParentCategories as $childParentCat) {

					$url = EB::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $childParentCat->id, true);
					$this->setPathway($childParentCat->title, $url);
				}
			}

			$this->setPathway($category->title, '');
		}

		// Check if the current active menu
		$activeMenu = $this->getActiveMenu();
		$isCurrentActiveAllCatMenu = ($activeMenu && $activeMenu->link === 'index.php?option=com_easyblog&view=categories') ? true : false;

		// Check if the current active menu is coming from all category menu item
		if ($isCurrentActiveAllCatMenu) {
			$this->setPathway($category->title, '');
		}

		//get the nested categories
		$category->childs = null;

		// Build nested childsets
		EB::buildNestedCategories($category->id, $category, false, true);

		// Parameterize initial subcategories to display. Ability to configure from backend.
		$nestedLinks = '';
		$initialLimit = ($this->app->getCfg('list_limit') == 0) ? 5 : $this->app->getCfg('list_limit');

		if ($category->childs && (count($category->childs) > $initialLimit)) {
			$initialNestedLinks = '';
			$initialRow = new stdClass();
			$initialRow->childs = array_slice($category->childs, 0, $initialLimit);

			EB::accessNestedCategories($initialRow, $initialNestedLinks, '0', '', 'link', ', ');

			$moreNestedLinks = '';
			$moreRow = new stdClass();
			$moreRow->childs = array_slice($category->childs, $initialLimit);

			EB::accessNestedCategories($moreRow, $moreNestedLinks, '0', '', 'link', ', ');

			// Hide more nested links until triggered
			$nestedLinks .= $initialNestedLinks;

			$nestedLinks .= '<span class="more-subcategories-toggle" data-more-categories-link> ' . JText::_('COM_EASYBLOG_AND') . ' <a href="javascript:void(0);">' . JText::sprintf('COM_EASYBLOG_OTHER_SUBCATEGORIES', count($category->childs) - $initialLimit) . '</a></span>';
			$nestedLinks .= '<span class="more-subcategories" style="display: none;" data-more-categories>, ' . $moreNestedLinks . '</span>';

		} else {
			EB::accessNestedCategories($category, $nestedLinks, '0', '', 'link', ', ');
		}

		$catIds = array();
		$catIds[] = $category->id;

		// If user decided not to show posts from subcategories, we can skip this part.
		if ($this->params && $this->params->get('category_subcategories_posts', true)) {
			EB::accessNestedCategoriesId($category, $catIds);
		}

		$category->nestedLink = $nestedLinks;

		// Get total posts in this category
		$category->cnt = $model->getTotalPostCount($category->id);

		$limit = EB::getViewLimit('category_posts_limit', 'category');

		// Check if this is filter by custom field
		$filter = $this->input->get('filter', false);
		$fields = [];
		$options = [];

		// Check if this user has saved filter search before
		$filterSaved = EB::model('fields')->getSavedFilter($category->id);

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
			$options['fieldsFilter'] = $fields;
			$options['fieldsFilterRule'] = $filterMode;
			$options['strictMode'] = $strictMode;

		} else if ($filterSaved) {
			$params = json_decode($filterSaved->params);

			foreach ($params as $filter) {
				if (strpos($filter->name, 'field') !== false) {
					$fieldId = explode('-', $filter->name);
					$fieldId = $fieldId[1];

					$fields[$fieldId][] = $filter->value;
				}

				if ($filter->name == 'filtermode') {
					$options['fieldsFilterRule'] = $filter->value;
				}
			}

			$options['fieldsFilter'] = $fields;
		}


		// Default sorting behavior
		$options['ordering'] = $this->params->get('ordering');
		$options['sort'] = $this->params->get('ordering_direction');

		if (is_null($options['sort'])) {
			unset($options['sort']);
		}

		// Custom sorting behavior via url
		$customOrdering = $this->input->get('ordering', '', 'cmd');
		$customSortingDirection = $this->input->get('sorting', '', 'cmd');

		if ($customOrdering) {
			$allowedOrdering = array(
				'modified',
				'created',
				'title',
				'published',
				'hits'
			);

			if (in_array($customOrdering, $allowedOrdering)) {
				$options['ordering'] = $customOrdering;
			}
		}

		if ($customSortingDirection) {
			$allowedSortingDirection = ['asc', 'desc'];

			if (in_array(strtolower($customSortingDirection), $allowedSortingDirection)) {
				$options['sort'] = $customSortingDirection;
			}
		}

		// Get the posts in the category
		$data = $model->getPosts($catIds, $limit, [], [], $options);

		// Get the pagination
		$pagination = $model->getPagination();

		// Get allowed categories
		$allowCat = $model->allowAclCategory($category->id);

		// Format the data that we need
		$posts = [];

		// Ensure that the user is really allowed to view the blogs
		if (!empty($data)) {

			// Format the blog posts
			$options = [
				'cacheComment' => false,
				'cacheCommentCount' => false,
				'cacheRatings' => false,
				'cacheTags' => false,
				'cacheAuthors' => false,
				'loadAuthor' => false
			];

			if ($this->params->get('post_comment_counter', 0)) {
				$options['cacheCommentCount'] = true;
			}

			// Disabled #2627
			// if ($this->params->get('post_comment_preview', 0)) {
			// 	$options['cacheComment'] = true;
			// }

			if ($this->params->get('post_tags', 0)) {
				$options['cacheTags'] = true;
			}

			if ($this->params->get('post_ratings', 0)) {
				$options['cacheRatings'] = true;
			}

			if ($this->params->get('post_author', 0) || $this->params->get('post_author_avatar', 0)) {
				$options['cacheAuthors'] = true;
				$options['loadAuthor'] = true;
			}

			$posts = EB::formatter('list', $data, true, $options);
		}

		// Check isCategorySubscribed
		$isCategorySubscribed = $model->isCategorySubscribedEmail($category->id, $this->my->email);
		$subscriptionId = '';

		if ($isCategorySubscribed) {
			$subscriptionModel = EB::model('Subscription');
			$subscriptionId = $subscriptionModel->getSubscriptionId($this->my->email, $category->id, EBLOG_SUBSCRIPTION_CATEGORY);
		}

		// If this category has a different theme, we need to output it differently
		if (!empty($category->theme)) {
			$this->setTheme($category->theme);
		}

		$useMenuForTitle = ($activeMenu && $activeMenu->link == 'index.php?option=com_easyblog&view=categories') ? false : true;

		// Set the page title
		$title = EB::getPageTitle(JText::_($category->title), $useMenuForTitle);
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Set the meta description for the category
		EB::setMeta($category->id, META_TYPE_CATEGORY, '', $pagination);

		// Set the return url
		$return = $category->getExternalPermalink();

		// Get the pagination
		$pagination = $pagination->getPagesLinks();

		// To be able to standardize the category headers we need to declare properties available on the table
		$category->isCategorySubscribed = $isCategorySubscribed;

		$postStyles = $this->getRowStyles();

		EB::facebook()->addOpenGraphTags($category);

		$this->set('postStyles', $postStyles);
		$this->set('subscriptionId', $subscriptionId);
		$this->set('allowCat', $allowCat);
		$this->set('category', $category);
		$this->set('posts', $posts);
		$this->set('return', $return);
		$this->set('pagination', $pagination);
		$this->set('privacy', $privacy);
		$this->set('isCategorySubscribed', $isCategorySubscribed);

		parent::display('categories/item/default');
	}

	/**
	 * Maintain backward compatibility with grid view and grid layout
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function getRowStyles()
	{
		$style = EB::getPostStyles($this->params);

		// Backward compatibility for legacy properties #2645
		$gridView = $this->params->get('grid_view', null);
		$gridLayout = $this->params->get('grid_layout', null);

		if (!is_null($gridView) && $gridView) {
			$style->row = 'column';
		}

		if (!is_null($gridLayout) && $gridLayout) {
			$style->columns = $gridLayout;
		}

		return $style;
	}

	/**
	 * Validate whether the current viewer can able to access this single category page under current site language
	 *
	 * @since	5.3.3
	 * @access	public
	 */
	public function validateMultilingualCategoryAccess($category)
	{
		// check for the current blog post language
		$categoryLang = $category->language;

		// Skip this if the post language is set to all
		if (!$categoryLang || $categoryLang == '*') {
			return true;
		}

		$isSiteMultilingualEnabled = FH::isMultiLingual();

		// The reason why need to check this is because this JoomSEF extension have their own language management
		// In order to use their own language management, the site have to turn off language filter plugin
		$isJoomSEFLanguageEnabled = EBR::isJoomSEFLanguageEnabled();

		// Skip this if site language filter plugin is not enabled
		if (!$isSiteMultilingualEnabled && !$isJoomSEFLanguageEnabled) {
			return true;
		}

		// check for the current active menu language
		$activeMenu = $this->app->getMenu()->getActive();
		$activeMenuLang = $activeMenu->language;

		// Determine for the current site language
		$currentSiteLang = JFactory::getLanguage()->getTag();

		if ($categoryLang == $currentSiteLang || $activeMenuLang == $categoryLang) {
			return true;
		}

		if ($activeMenuLang == '*' && ($categoryLang == $currentSiteLang)) {
			return true;
		}

		// Throw an error if the blog posted under different language which not match with the current active menu + site language
		throw EB::exception(JText::_('COM_EASYBLOG_CATEGORY_NOT_FOUND'), 404);
	}
}
