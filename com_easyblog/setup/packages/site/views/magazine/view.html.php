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

class EasyBlogViewMagazine extends EasyBlogView
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

		// Add breadcrumbs on the site menu.
		$this->setPathway('COM_EASYBLOG_LATEST_BREADCRUMB');

		// Retrieve the leading article type.
		$leadingArticleType = $this->params->get('magazine_leading_article_type', 'latestPost');

		// Retrieve the leading article categories. Fallback if the category is not set.
		$leadingArticleCategories = $this->params->get('magazine_leading_article_category', array());

		// Clear leadingArticleCategory if user selected all categories
		if ($leadingArticleCategories) {
			$leadingArticleCategories = array_diff($leadingArticleCategories, array('all'));
		}

		$model = EB::model('Blog');
		$leadingArticle = "";
		$excludeBlogs = [];

		// Latest post
		if ($leadingArticleType == 'latestPost') {
			$latestPost = $model->getBlogsby('', '', '', '1', EBLOG_FILTER_PUBLISHED, false, false, '', false, false, true, '', $leadingArticleCategories, null, 'listlength', 
				false, '', '', '');

			if ($latestPost) {
				// Formatting the leading article
				$latestPost = EB::formatter('list', $latestPost, false);

				$leadingArticle = $latestPost[0];
				$excludeBlogs[] = $leadingArticle->id;
			}
		}

		// Latest featured
		if ($leadingArticleType == 'latestFeatured') {
			$featured = $model->getFeaturedBlog($leadingArticleCategories, '1');

			// Format leadingArticle
			$featured = EB::formatter('featured', $featured, false);

			if (!empty($featured)) {
				$leadingArticle = $featured[0];
			}
		}

		// Single post
		if ($leadingArticleType == 'singlePost') {
			// Retrieve the post entered by user.
			$leadingArticleId = $this->params->get('magazine_leading_article', false);

			if ($leadingArticleId) {
				$excludeBlogs[] = $leadingArticleId;
				$leadingArticle = EB::post($leadingArticleId);
			}
		}

		// Determine if we should explicitly include authors.
		$includeAuthors = [];

		if ($this->params->get('magazine_inclusion_authors', false)) {
			$includeAuthors = $this->params->get('magazine_inclusion_authors');
		}

		// Determine if we should explicitly exclude authors.
		$excludeAuthors = [];

		if ($this->params->get('magazine_exclusion_authors', false)) {
			$excludeAuthors = $this->params->get('magazine_exclusion_authors');
		}

		// Determine if we should exclude featured post from list.
		$excludeFeatured = $this->params->get('magazine_exclude_featured', false);

		// Determine the list limit for the list article
		$listLimit = (int) $this->params->get('listLimit', '9');
		if ($listLimit < 0) {
			$listLimit = 9;
		}

		// Retrieve the list article categories.
		$listArticleCategories = $this->params->get('magazine_list_article_category', array());

		// Fetch all blog entries based on the defined information above.
		$data = $model->getBlogsby('', '', '', $listLimit, EBLOG_FILTER_PUBLISHED, false, false, $excludeBlogs, false, false, true, '', $listArticleCategories, null, 'listlength', 
			false, $includeAuthors, $excludeAuthors, $excludeFeatured);

		// Format blog items without caching.
		$posts = EB::formatter('list', $data, false);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_MAGAZINE_PAGE_TITLE'));

		// Set the page title
		$this->setPageTitle($title, '', $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URLs.
		$this->canonical('index.php?option=com_easyblog');

		// Set the meta tags for this page
		EB::setMeta(0, META_TYPE_VIEW);

		$style = $this->params->get('magazine_style', 'column_news');
		$oldLayoutNames = [
			'layout_1' => 'column_news',
			'layout_2' => 'side_news',
			'layout_3' => 'rows'
		];

		// Backward compatibility
		if (in_array($style, array_keys($oldLayoutNames))) {
			$style = $oldLayoutNames[$style];
		}

		// The return URL
		$return = EBR::_('index.php?option=com_easyblog', false);

		// The view all link
		$viewAll = EBR::_('index.php?option=com_easyblog&view=latest');

		$leadingArticleReadmore = $this->params->get('magazine_leading_article_readmore', true);
		$leadingArticleShowDate = $this->params->get('magazine_lading_show_date', true);
		$isImageForLeadingArticle = EB::image()->isImage($leadingArticle->getImage());

		$leadingArticle->video = !$isImageForLeadingArticle ? EB::media()->renderVideoPlayer($leadingArticle->getImage(), [
									'width' => '260',
									'height' => '200',
									'ratio' => '',
									'muted' => false,
									'autoplay' => false,
									'loop' => false
								], false) : '';

		$hideCover = $this->params->get('magazine_hide_cover', true);
		$articleReadmore = $this->params->get('list_article_readmore', true);
		$showDate = $this->params->get('magazine_show_date', true);

		$this->set('return', $return);
		$this->set('viewAll', $viewAll);
		$this->set('leadingArticle', $leadingArticle);
		$this->set('leadingArticleReadmore', $leadingArticleReadmore);
		$this->set('leadingArticleShowDate', $leadingArticleShowDate);
		$this->set('isImageForLeadingArticle', $isImageForLeadingArticle);
		$this->set('hideCover', $hideCover);
		$this->set('articleReadmore', $articleReadmore);
		$this->set('showDate', $showDate);
		$this->set('posts', $posts);
		$this->set('style', $style);

		parent::display('magazine/default');
	}
}
