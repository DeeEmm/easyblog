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

class EasyBlogViewGrid extends EasyBlogView
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
		$params = $this->getActiveMenuParams();

		return $params;
	}

	public function display($tmpl = null)
	{
		// Add the RSS headers on the page.
		EB::feeds()->addHeaders('index.php?option=com_easyblog');

		$this->setPathway('COM_EASYBLOG_LATEST_BREADCRUMB');

		$this->updateParams();

		$showcasePostType = $this->params->get('showcase_grid_post_type');
		$showcasePostCategories = $this->params->get('showcase_grid_showcase_category', array());

		// Retrieving the showcase limit.
		$showcasePostLimit = $this->params->get('showcase_grid_limit');
		if ($showcasePostLimit < 1) {
			$showcasePostLimit = 5;
		}

		// Format showcasePostCategories if user selected all category.
		if ($showcasePostCategories) {
			$showcasePostCategories = array_diff($showcasePostCategories, array('all'));
		}

		$model = EB::model('Blog');
		$featured = [];
		$excludeBlogs = [];

		$tobeCached = [];

		// If showcase is disabled, then no need to get the posts.
		if ($this->params->get('enable_showcase', 1)) {
			// Showcase type latest post.
			if ($showcasePostType == 'latest') {

				$latestPost = $model->getBlogsby('', '', '', $showcasePostLimit, EBLOG_FILTER_PUBLISHED, false, false, '', false, false, true, '', $showcasePostCategories, null, 'listlength',
					false, '', '', '');

				if ($latestPost) {

					$tobeCached = array_merge($tobeCached, $latestPost);

					// Format the showcase posts.
					// $latestPost = EB::formatter('list', $latestPost, false);

					// Exclude all post which already displayed on the showcase.
					foreach ($latestPost as $post) {
						$excludeBlogs[] = $post->id;
					}
				}

				$featured = $latestPost;
			}

			// Showcase type featured post.
			if ($showcasePostType == 'featured') {
				$featured = $model->getFeaturedBlog($showcasePostCategories, $showcasePostLimit);

				if ($featured) {

					$tobeCached = array_merge($tobeCached, $featured);

					// $featured = EB::formatter('featured', $featured, false);
				}
			}
		}

		// Determine if we should explicitly include authors.
		$includeAuthors = [];

		if ($this->params->get('grid_inclusion_authors', false)) {
			$includeAuthors = $this->params->get('grid_inclusion_authors');
		}

		// Determine if we should explicitly exclude authors.
		$excludeAuthors = [];

		if ($this->params->get('grid_exclusion_authors', false)) {
			$excludeAuthors = $this->params->get('grid_exclusion_authors');
		}

		// Determine if we should exclude featured post from the list.
		$excludeFeatured = $this->params->get('grid_exclude_featured', false);

		// Retrieve the post categories
		$postCategories = $this->params->get('grid_post_category', []);

		// Format postCategories if user selected all category.
		if ($postCategories) {
			$postCategories = array_diff($postCategories, ['all']);
		}

		$limitstart = $this->input->get('limitstart', 0, 'int');

		$max = 0;
		$options = array();
		$showLoadMore = false;

		// Get the limit
		$limit = EB::getViewLimit();

		$paginationStyle = $this->params->get('grid_pagination_style', 'normal');

		if ($paginationStyle == 'autoload') {
			$max = $limit + 1;
			$options['paginationType'] = 'loadmore';
		}

		// Fetch all blog entries based on the defined information above.
		$data = $model->getBlogsby('', '', '', 0, EBLOG_FILTER_PUBLISHED, false, false, $excludeBlogs, false, false, true, '', $postCategories, null, 'listlength',
			false, $includeAuthors, $excludeAuthors, $excludeFeatured, array(),	$options);

		$posts = array();

		if ($data) {
			if ($paginationStyle == 'autoload' && count($data) == $max) {
				$showLoadMore = true;

				// Take out the last post
				array_pop($data);
			}

			$tobeCached = array_merge($tobeCached, $data);

			// $posts = EB::formatter('list', $data, false);
		}

		// we will cache it here.
		if ($tobeCached) {

			// Format the blog posts
			$options = [
				'cacheComment' => false,
				'cacheCommentCount' => false,
				'cacheRatings' => false,
				'cacheTags' => false,
				'cacheAuthors' => false,
				'loadAuthor' => false
			];

			EB::cache()->insert($tobeCached, $options);
		}

		if ($featured) {
			$featured = EB::formatter('featured', $featured, false);
		}

		if ($data) {
			$posts = EB::formatter('list', $data, false);
		}


		$showcaseTruncation = $this->params->get('showcase_content_limit', 350) > 0 ? true : false;
		$gridTruncation = $this->params->get('grid_content_limit', 350) > 0 ? true : false;

		// Get the pagination
		$pagination = $model->getPagination();
		$currentPageLink = $pagination->getCurrentPageLink('grid', true);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_GRID_PAGE_TITLE'));

		// Set the page title
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Set the meta tags for this page
		EB::setMeta(0, META_TYPE_VIEW, '', $pagination);

		// Retrieve the pagination for the latest view
		$pagination = $pagination->getPagesLinks();

		// Add canonical URLs.
		$this->canonical('index.php?option=com_easyblog');

		$limitstart = $limitstart + $limit;

		// Get the current url
		$return = EBR::_('index.php?option=com_easyblog', false);

		// Fix the mess created by the settings
		$columns = $this->getColumns();

		$this->set('return', $return);
		$this->set('featured', $featured);
		$this->set('excludeBlogs', $excludeBlogs);
		$this->set('posts', $posts);
		$this->set('columns', $columns);
		$this->set('showcaseTruncation', $showcaseTruncation);
		$this->set('pagination', $pagination);
		$this->set('showLoadMore', $showLoadMore);
		$this->set('limitstart', $limitstart);
		$this->set('paginationStyle', $paginationStyle);
		$this->set('currentPageLink', $currentPageLink);

		parent::display('grid/default/default');
	}

	/**
	 * This method is to remap grid options into post options similar to the latest view.
	 * Since v6.0, we will try to get rid of this grid view and allow user to configure grid layouts from view=frontpage
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	private function updateParams()
	{
		// Backward compatibility
		$mapping = [
			'post_image' => [
				'key' => 'grid_show_cover',
				'default' => true
			],
			'post_author' => [
				'key' => 'grid_show_author',
				'default' => true
			],
			'post_author_avatar' => [
				'key' => 'grid_show_author_avatar',
				'default' => false
			],
			'post_category' => [
				'key' => 'grid_show_category',
				'default' => true
			],
			'post_date' => [
				'key' => 'grid_show_date',
				'default' => true
			],
			'post_readmore' => [
				'key' => 'grid_show_readmore',
				'default' => false
			],
			'post_content_limit' => [
				'key' => 'grid_content_limit',
				'default' => 350
			]
		];

		foreach ($mapping as $newKey => $oldProperty) {

			if (is_null($this->params->get($newKey, null))) {
				$data = (object) $oldProperty;

				$this->params->set($newKey, $this->params->get($data->key, $data->default));
			}
		}

		$this->theme->params = $this->params;

		return $this->params;
	}

	/**
	 * Becuse of the messed up way we store values prior to 6.0.0, we need to decipher the value of columsn
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	private function getColumns()
	{
		$defaultColumns = 3;

		// If the user set the new columns value, we'll just use that
		$columns = $this->params->get('grid_columns', null);

		if (!is_null($columns)) {
			return $columns;
		}

		// Fix the mess created by the settings
		$legacyColumns = $this->params->get('grid_layout', null);

		if (!is_null($legacyColumns)) {
			$legacyColumnsMapping = [
				"2" => 6,
				"4" => 3,
				"3" => 4,
				"6" => 2
			];

			if (isset($legacyColumnsMapping[$legacyColumns])) {
				return $legacyColumnsMapping[$legacyColumns];
			}
		}

		return $defaultColumns;
	}
}
