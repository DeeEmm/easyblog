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

class EasyBlogViewBlogger extends EasyBlogView
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

		if ($layout !== 'listings') {
			// Default params that would be retrieved from the xml file
			$params = EB::getMenuParams(0, 'bloggers', true);

			// If the current menu being accessed is associated to this view, retrieve its params
			if ($segments['view'] == 'blogger' && !isset($segments['layout'])) {
				$params = $this->getActiveMenuParams('bloggers');
			}

			return $params;
		}

		$id = $this->app->input->get('id', 0, 'int');

		if ($layout === 'listings' && $id) {
			$params = EB::getMenuParams($id, 'blogger', true);

			return $params;
		}
	}

	/**
	 * Displays the all bloggers
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Set the breadcrumbs only when necessary
		if (!EBR::isCurrentActiveMenu('blogger')) {
			$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB') , '' );
		}

		// Retrieve the current sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_bloggerorder', 'latest'), 'cmd');

		// Check if there's any sorting setting set from the menu item
		$menu = $this->app->getMenu()->getActive();

		if (is_object($menu) && stristr($menu->link, 'view=blogger') !== false) {
			$menuParams = $menu->getParams();

			// Ensure the sorting setting did set from the menu item
			if ($menuParams->get('sorting') && $menuParams->get('sorting') != '-2') {
				$sort = $menuParams->get('sorting');
			}
		}

		// Retrieve the current filtering options.
		$filter = $this->input->get('filter', 'showallblogger', 'cmd');

		if ($this->config->get('main_bloggerlistingoption')) {
			$filter = $this->input->get('filter', 'showbloggerwithpost', 'cmd');
		}

		// Retrieve search values
		$search = $this->input->get('search', '', 'string');
		$badchars = array('#', '>', '<', '\\', '=', '(', ')', '*', ',', '.', '%', '\'');
		$search = trim(str_replace($badchars, '', $search));

		// Retrieve the models.
		$bloggerModel = EB::model('Blogger');
		$blogModel = EB::model('Blog');
		$postTagModel = EB::model('PostTag');

		// Get limit
		$limit = EB::getViewLimit('author_limit', 'bloggers');

		// Retrieve the bloggers to show on the page.
		$results = $bloggerModel->getBloggers($sort, $limit, $filter , $search);

		$pagination = $bloggerModel->getPagination();

		// Set meta tags for bloggers
		EB::setMeta(META_ID_BLOGGERS, META_TYPE_VIEW, '', $pagination);

		// Determine the current page if there's pagination
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// Set the title of the page
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_BLOGGERS_PAGE_TITLE'));
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Add canonical urls
		$canoLink = 'index.php?option=com_easyblog&view=blogger';
		$canoLink .= ($limitstart) ? '&limitstart=' . $limitstart : '';
		$this->canonical($canoLink);

		// Determine the default ordering for the posts
		$postsOrdering = $this->config->get('layout_postorder');
		$postsLimit = EB::getViewLimit('author_posts_limit', 'bloggers');

		// Format the blogger listing.
		$authors = [];

		if (!empty($results)) {

			//preload users
			$ids = array();
			foreach ($results as $row) {
				$ids[] = $row->id;
			}

			EB::user($ids);

			$options = [
				'cachePosts' => $this->params->get('author_posts', false),
				'cacheCategories' => false,
				'cacheCategoriesCount' => $this->params->get('author_categories', false),
				'cacheTags' => false,
				'cacheTagsCount' => $this->params->get('author_tags', false),
			];

			// lets cache the bloggers
			EB::cache()->insertBloggers($results, $options);

			// lets group the posts for posts caching first
			$tobeCached = [];
			$bloggerPosts = [];


			if ($this->params->get('author_posts', 0)) {

				foreach ($results as $row) {
					$bloggerId = $row->id;

					$items = array();

					// try to get from cache
					if (EB::cache()->exists($bloggerId, 'bloggers')) {
						$data = EB::cache()->get($bloggerId, 'bloggers');

						if (isset($data['post'])) {
							$items = $data['post'];
						}
					} else {
						$items = $blogModel->getBlogsBy('blogger', $row->id, $postsOrdering, $postsLimit, EBLOG_FILTER_PUBLISHED);
					}

					$bloggerPosts[$bloggerId] = $items;

					if ($items) {
						$tobeCached = array_merge($tobeCached, $items);
					}
				}

				// // Format the blog posts
				$cacheOptions = [
					'cacheComment' => false,
					'cacheCommentCount' => false,
					'cacheRatings' => false,
					'cacheVoted' => false,
					'cacheTags' => false,
					'cacheAuthors' => false,
					'loadAuthor' => false,
					'loadFields' => false
				];

				// now we can cache the posts.
				if ($tobeCached) {
					EB::cache()->insert($tobeCached, $cacheOptions);
				}
			}

			foreach ($results as $row) {
				// Load the author object
				$author = EB::user($row->id);

				$author->blogs = [];
				$author->categories = [];
				$author->tags = [];

				if (EB::cache()->exists($author->id, 'bloggers')) {
					$data = EB::cache()->get($author->id, 'bloggers');
				}

				if ($this->params->get('author_posts', 0)) {
					// Retrieve blog posts from this user.
					$posts = $bloggerPosts[$row->id];
					$author->blogs = EB::formatter('list', $posts, false, $cacheOptions);
				}

				$author->featured = ($row->featured) ? 1 : 0;
				$author->isBloggerSubscribed = $bloggerModel->isBloggerSubscribedEmail($author->id, $this->my->email);

				$authors[]	= $author;
			}
		}


		// Format the pagination
		$pagination = $pagination->getPagesLinks();
		$this->set('authors', $authors);
		$this->set('search', $search);
		$this->set('sort', $sort);
		$this->set('limitPreviewPost', $postsLimit);
		$this->set('pagination', $pagination);

		parent::display('authors/default/default');
	}

	/**
	 * Displays blog posts created by specific users
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function listings()
	{
		// Get sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');
		$id = $this->input->get('id', 0, 'int');

		// Load the author object
		$author = EB::user($id);

		// Disallow all users from being viewed
		if (!EB::isBlogger($author->id) || !$author->id) {
			throw EB::exception(JText::_('COM_EASYBLOG_INVALID_AUTHOR_ID_PROVIDED'), 404);
		}

		// Set the breadcrumbs
		if (!EBR::isCurrentActiveMenu('blogger', $author->id) && !EBR::isCurrentActiveMenu('blogger')) {
			$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB') , EB::_('index.php?option=com_easyblog&view=blogger') );

			$this->setPathway($author->getName());
		}

		// Excluded categories
		$excludeCats = $this->params->get('exclusion', []);

		// Ensure that this is an array
		if (!is_array($excludeCats) && $excludeCats) {
			$excludeCats = [$excludeCats];
		}

		// Get the blogs model now to retrieve our blog posts
		$model = EB::model('Blog');

		// Get the limit
		$limit = EB::getViewLimit('author_posts_limit', 'blogger');

		// Get blog posts
		$posts = $model->getBlogsBy('blogger', $author->id, $sort, $limit, '', false, false, '', false, false, true, $excludeCats);
		$pagination	= $model->getPagination();

		EB::facebook()->addOpenGraphTags($author);

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

		// Format the blogs with our standard formatter
		$posts = EB::formatter('list', $posts, true, $options);

		// Add canonical urls
		$limitstart = $this->input->get('limitstart', 0, 'int');
		$canoLink = 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $author->id;
		$canoLink .= ($limitstart) ? '&limitstart=' . $limitstart : '';
		$this->canonical($canoLink);

		// Add authors rss links on the header
		if ($this->config->get('main_rss')) {
			if ($this->config->get('main_feedburner') && $this->config->get('main_feedburnerblogger')) {
				$this->doc->addHeadLink(EB::string()->escape($author->getRssLink()), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
			} else {

				// Add rss feed link
				$this->doc->addHeadLink($author->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
				$this->doc->addHeadLink($author->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
			}
		}

		// Set the title of the page
		$title 	= EB::getPageTitle($author->getName());
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Get the authors acl
		$acl = EB::acl($author->id);

		// Set meta tags for the author if allowed to
		if ($acl->get('allow_seo')) {
			EB::setMeta($author->id, META_TYPE_BLOGGER, true, $pagination);
		}


		$return = $author->getPermalink();
		$pagination = $pagination->getPagesLinks();

		$showIntegration = false;

		if (EB::followers()->hasIntegrations($author) || EB::friends()->hasIntegrations($author) || EB::messaging()->hasMessaging($author->id)) {
			$showIntegration = true;
		}

		// To allow the use of headers.author, simulate the $author->isBloggerSubscribed
		$bloggerModel = EB::model('Blogger');
		$author->isBloggerSubscribed = $bloggerModel->isBloggerSubscribedEmail($author->id, $this->my->email);

		$postStyles = $this->getRowStyles();

		$this->set('postStyles', $postStyles);
		$this->set('pagination', $pagination);
		$this->set('return', $return);
		$this->set('author', $author);
		$this->set('posts', $posts);
		$this->set('sort', $sort);
		$this->set('showIntegration', $showIntegration);

		parent::display('authors/item/default');
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
