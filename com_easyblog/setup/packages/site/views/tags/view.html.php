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

class EasyBlogViewTags extends EasyBlogView
{
	/**
	 * This method would be invoked by the parent to bind any active menu params available to the view and themes library
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

		if ($layout !== 'tag') {

			// Default params that would be retrieved from the xml file
			$params = EB::getMenuParams(null, 'tags');

			// If the current menu being accessed is associated to this view, retrieve its params
			if ($segments['view'] == 'tags' && !isset($segments['layout'])) {
				$params = $this->getActiveMenuParams('tags');
			}

			return $params;
		}

		$id = $this->app->input->get('id', 0, 'int');

		if ($layout === 'tag' && $id) {
			$params = EB::getMenuParams($id, 'tag');

			return $params;
		}
	}

	/**
	 * Displays all tags on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Set breadcrumb
		$this->setViewBreadcrumb('tags');

		// Set page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_TAGS_PAGE_TITLE'));
		$this->setPageTitle($title, '', $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URL to satify Googlebot. Incase they think it's duplicated content.

		$limitstart = $this->input->get('limitstart', 0, 'int');
		$canoLink = 'index.php?option=com_easyblog&view=tags';
		$canoLink .= ($limitstart) ? '&limitstart=' . $limitstart : '';
		$this->canonical($canoLink, array('ordering', 'sorting'));

		// Retrieve search values
		$search = $this->input->get('search', '', 'string');

		// Get the model
		$model = EB::model('Tags');

		// Get other sorting and filters
		$ordering = EBString::strtolower($this->input->get('ordering', '', 'string'));
		$sorting = EBString::strtolower($this->input->get('sorting', $this->config->get('main_tags_sorting'), 'string'));

		// Get the tags
		$limit = ($this->params->get('tag_limit', 18) == 'all') ? '' : $this->params->get('tag_limit', 18);

		$result = $model->getTagCloud($limit, $ordering, $sorting, true, $search, false, true);
		$pagination = $model->getPagination();

		// Set meta tags for tags view
		EB::setMeta(META_ID_TAGS, META_TYPE_VIEW, '', $pagination);

		// Format the tags
		$tags = EB::formatter('tags', $result, true);

		// Since the ordering options is already removed, we will just hard code the ordering here. #1433
		$titleURL = 'index.php?option=com_easyblog&view=tags&ordering=title&sorting=asc';
		$postURL = 'index.php?option=com_easyblog&view=tags&ordering=postcount&sorting=desc';

		$showRss = true;

		if (!$this->config->get('main_rss') || !$this->params->get('tag_rss', true)) {
			$showRss = false;
		}

		$this->set('showRss', $showRss);
		$this->set('titleURL', $titleURL);
		$this->set('postURL', $postURL);
		$this->set('tags', $tags);
		$this->set('sorting', $sorting);
		$this->set('ordering', $ordering);
		$this->set('pagination', $pagination);

		parent::display('tags/default/default');
	}

	/**
	 * Displays blog listings by specific tags on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function tag()
	{
		// Get the tag id
		$id = $this->input->get('id', '', 'default');

		// Add noindex for tags listing by default
		if ($this->config->get('main_tag_noindex', true)) {
			$this->doc->setMetadata('robots', 'noindex,follow');
		}

		// Load the tag object
		$tag = EB::table('Tag');
		$tag->load($id);

		// The tag could be a permalink
		if (!$tag->id) {
			$tag->load($id, true);
		}

		if ($tag->id) {
			EB::cache()->set($tag, 'tag');
		}

		$pageTitle = EB::getPageTitle($tag->getTitle());

		// Set page title
		$this->setPageTitle($pageTitle, '' , $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URL
		$limitstart = $this->input->get('limitstart', 0, 'int');
		$canoLink = 'index.php?option=com_easyblog&view=tags&layout=tag&id=' . $id;
		$canoLink .= ($limitstart) ? '&limitstart=' . $limitstart : '';
		$this->canonical($canoLink);

		// Set breadcrumb
		if (!EBR::isCurrentActiveMenu('tags')) {
			$this->setPathway(JText::_('COM_EASYBLOG_TAGS_BREADCRUMB'), EBR::_('index.php?option=com_easyblog&view=tags'));
		}

		$isCurrentActiveSingleTagMenu = EBR::isCurrentActiveMenu('tags', $tag->id);

		// Only show this tag name if the current active menu is not single tag menu item type
		if (!$isCurrentActiveSingleTagMenu) {
			$this->setPathway($tag->getTitle());
		}

		// Get the blogs model
		$blogModel = EB::model('Blog');
		$tagModel = EB::model('Tags');

		$limit = EB::getViewLimit('tag_posts_limit', 'tag');

		$options = array();
		$params = $this->theme->params;

		$options['order'] = $params->get('layout_postorder');
		$options['sort'] = $params->get('layout_postsort');

		// Get the blog posts now
		$rows = $blogModel->getTaggedBlogs($tag->id, $limit, '', '', false, $options);

		// Get the pagination
		$pagination	= $blogModel->getPagination();

		// set meta tags for single tag view
		EB::setMeta($tag->id, META_TYPE_TAG, $tag->getDescription(), $pagination);

		if (is_object($pagination) && method_exists($pagination, 'setAdditionalUrlParam')) {
			$pagination->setAdditionalUrlParam('id', $tag->id);
		}

		// Get total number of private blog posts
		$privateCount = 0;

		// Get total number of team blog count
		$teamblogCount = 0;

		if ($this->my->guest) {
			$privateCount = $tagModel->getTagPrivateBlogCount($id);
		}

		// Determines if we should get the team blog count
		if (!$this->config->get('main_includeteamblogpost')) {
			$teamblogCount = $tagModel->getTeamBlogCount($id);
		}


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

		// Format the blog posts using the standard list formatter
		$posts = EB::formatter('list', $rows, true, $options);
		$return = base64_encode($tag->getPermalink());

		$postStyles = $this->getRowStyles();

		$this->set('postStyles', $postStyles);
		$this->set('return', $return);
		$this->set('tag', $tag);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('private', $privateCount);
		$this->set('team', $teamblogCount);

		parent::display('tags/item/default');
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
}
