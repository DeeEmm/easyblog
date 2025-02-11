<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.system.file');
jimport('joomla.system.folder');

class modLatestBlogsHelper extends EasyBlog
{
	public $lib = null;
	public $params =  [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	/**
	 * Retrieve items based on a particular set of filter type
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItems($filterType)
	{
		$method = 'get' . ucfirst((string) $filterType) . 'Posts';

		if (!method_exists($this, $method)) {
			return false;
		}

		return $this->$method();
	}

	/**
	 * Retrieves recent posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getRecentPosts()
	{
		$posts = $this->getPosts();

		return $posts;
	}

	/**
	 * Retrieves posts by the current logged in user
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getViewerPosts()
	{
		if (!$this->my->id) {
			return false;
		}

		$id = (int) $this->my->id;

		$posts = $this->getPosts($id, 'blogger');

		return $posts;
	}

	/**
	 * Retrieves recent posts by author
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getAuthorPosts()
	{
		$id = (int) $this->lib->params->get('bloggerlist', '');

		if (!$id) {
			$this->setError('MOD_LATESTBLOGS_SELECT_BLOGGER');

			return false;
		}

		$posts = $this->getPosts($id, 'blogger');

		return $posts;
	}

	/**
	 * Retrieves recent posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCategoryPosts()
	{
		$id = (int) $this->params->get('cid', '');

		if (!$id) {
			$this->setError(JText::_('MOD_LATESTBLOGS_SELECT_CATEGORY'));
			return false;
		}

		// Load up the category table
		$category = EB::table('Category');
		$category->load($id);

		// Check if the category is a private category. If it is private, ensure that the user has access
		// to view the category.
		if ($category->private != 0 && $this->my->guest) {
			$privacy = $category->checkPrivacy();

			if (!$privacy->allowed) {
				$this->setError(JText::_('MOD_LATESTBLOGS_CATEGORY_IS_CURRENTLY_SET_TO_PRIVATE'));
				return false;
			}
		}

		// Initialize the default list of categories.
		$catIds = array($category->id);

		// If configured to display subcategory items
		if ($this->lib->params->get('includesubcategory', 0)) {

			$category->childs = null;

			// Build nested category level
			EB::buildNestedCategories($category->id, $category, false, true);
			EB::accessNestedCategoriesId($category, $catIds);
		}

		// Get the list of blog posts associated with the category
		$posts = $this->getPosts($catIds, 'category');

		return $posts;
	}

	/**
	 * Retrieves recent posts by a specific tag
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTagsPosts()
	{
		$ids = $this->lib->params->get('tagid', '');

		// Legacy value perhaps as it was previously stored as a string
		if (!is_array($ids)) {
			$ids = explode(',', $ids);

			if (!is_array($ids)) {
				$ids = array($ids);
			}
		}

		// Ensure that the admin actually selected a tag
		if (empty($ids)) {
			$this->setError(JText::_('MOD_LATESTBLOGS_SELECT_TAG'));
			return;
		}

		// Get the posts that are associated with the tag
		$posts = $this->getPosts($ids, 'tag');

		return $posts;
	}

	/**
	 * Retrieves recent posts by a specific team
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTeamPosts()
	{
		$id = $this->lib->params->get('tid');

		// Ensure that the admin selected a team from the module settings
		if (!$id) {
			$this->setError(JText::_('MOD_LATESTBLOGS_SELECT_TEAM'));
			return false;
		}

		// Load up the team blog table
		$team = EB::table('TeamBlog');
		$team->load($id);

		// Determine if the current viewer is a member of the team
		$gids = EB::getUserGids();
		$team->isMember = $team->isMember($this->my->id, $gids);

		// Default set empty posts unless they can view it
		$posts = array();

		if ($team->access != 1 || $team->isMember) {
			$posts = $this->getPosts($id, 'team');
		}

		return $posts;
	}

	/**
	 * Retrieves recent posts by author of a particular article. This only works on view=entry
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getEntryPosts()
	{
		// Get the current view of the page
		$view = $this->input->get('view', '', 'cmd');

		// We need to have the id
		$id = $this->input->get('id', 0, 'int');

		// If the view is not entry, skip this
		if ($view != 'entry' || !$id) {
			return false;
		}

		// Load up the post
		$post = EB::post($id);

		// Ensure that the blog post has a proper author
		if (!$post->created_by) {
			return;
		}

		// Now we load the posts
		$posts = $this->getPosts($post->created_by, 'blogger');

		return $posts;
	}

	/**
	 * Retrieves the latest posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts($id = null, $type = 'latest')
	{
		$db = EB::db();
		$count = (int) $this->params->get('count', 0);
		if ($count < 1) {
			$count = 5;
		}
		$excludeFeatured = $this->params->get('excludefeatured', false);
		$respectFrontpage = $this->params->get('respectfrontpage', false);
		$excludeViewed = $this->params->get('excludeviewed', false);

		$excludeBlog = array();

		// Get the current view of the page
		$view = $this->input->get('view', '', 'cmd');

		// We need to have the id
		$currentPostId = $this->input->get('id', 0, 'int');

		if ($excludeViewed && $view == 'entry' && $currentPostId) {
			$excludeBlog = array($currentPostId);
		}

		$model = EB::model('Blog');
		$posts = array();

		$sort = $this->params->get('sortby', 'latest');

		if ($type == 'blogger') {
			$posts = $model->getBlogsBy('blogger', $id, array($sort, 'DESC'), $count, EBLOG_FILTER_PUBLISHED, null, $respectFrontpage, $excludeBlog, false, false, true, array(), array(), null, 'listlength', true, array(), array(), $excludeFeatured, array(), array('paginationType' => 'none'));

		}

		if ($type == 'category') {
			$posts = $model->getBlogsBy('category', $id, array($sort, 'DESC'), $count, EBLOG_FILTER_PUBLISHED, null, $respectFrontpage, $excludeBlog, false, false, true, array(), array(), null, 'listlength', true, array(), array(), $excludeFeatured, array(), array('paginationType' => 'none'));
		}

		if ($type == 'tag') {
			$posts = $model->getTaggedBlogs($id, $count, '', '', $excludeFeatured, array('excludeBlogs' => $excludeBlog, 'sort' => 'DESC'));
		}

		if ($type == 'team') {
			$posts = $model->getBlogsBy('teamblog', $id, array($sort, 'DESC'), $count, EBLOG_FILTER_PUBLISHED, null, $respectFrontpage, $excludeBlog, false, false, true, array(), array(), null, 'listlength', true, array(), array(), $excludeFeatured, array(), array('paginationType' => 'none'));
		}

		if ($type == 'latest') {

			// Determines if we should be using featured blogs only
			$featuredOnly = $this->params->get('usefeatured', false);

			$categories	= EB::getCategoryInclusion($this->params->get('catid'));
			$catIds = array();

			if (!empty($categories)) {
				if (!is_array($categories)) {
					$categories	= array($categories);
				}

				foreach ($categories as $item) {
					$category = new stdClass();
					$category->id = trim( $item );

					$catIds[] = $category->id;

					if ($this->params->get('includesubcategory', 0)) {
						$category->childs = null;
						EB::buildNestedCategories($category->id, $category , false , true );
						EB::accessNestedCategoriesId($category, $catIds);
					}
				}

				$catIds = array_unique($catIds);
			}

			if ($featuredOnly) {
				$posts = $model->getFeaturedBlog($catIds, $count);
			} else {
				$cid = $catIds;

				if (!empty($cid)) {
					$type = 'category';
				}

				$postType = null;

				if ($this->params->get('postType') != 'all') {
					$postType = $this->params->get('postType');
				}

				$posts = $model->getBlogsBy($type, $cid, array($sort, 'DESC'), $count, EBLOG_FILTER_PUBLISHED, null, $respectFrontpage, $excludeBlog, false, false, true, array(), $cid, $postType, 'listlength', true, array(), array(), $excludeFeatured, array(), array('paginationType' => 'none'));

			}
		}

		// Format the items
		$posts = $this->lib->processItems($posts);

		return $posts;
	}

	/**
	 * Normalize legacy values for post types
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function normalizeFilterType($type)
	{
		if ($type == '0') {
			return 'recent';
		}

		if ($type == '1') {
			return 'author';
		}

		if ($type == '2') {
			return 'category';
		}

		if ($type == '3') {
			return 'tags';
		}

		if ($type == '4') {
			return 'team';
		}

		if ($type == '5') {
			return 'entry';
		}

		// Default filter type
		return $type;
	}

	/**
	 * Determine the view all link based on the module filter type.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getViewAllLink($type)
	{

		$link = '';

		switch($type) {

			case 'category':
				$id = (int) $this->params->get('cid', '');
				$link = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $id);

				break;
			case 'author':
				$id = (int) $this->params->get('bloggerlist', '');
				$link = EBR::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $id);

				break;

			case 'tags':
				$ids = $this->params->get('tagid', '');

				if (!is_array($ids)) {
					$ids = explode(',', $ids);

					if (!is_array($ids)) {
						$ids = array($ids);
					}
				}

				if (count($ids) > 1) {
					$link = EBR::_('index.php?option=com_easyblog');
				} else {
					$id = $ids[0];
					$link = EBR::_('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $id);
				}

				break;

			case 'team':
				$id = (int) $this->params->get('tid', '');
				$link = EBR::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $id);
				break;

			case 'recent':
			default:
				$link = EBR::_('index.php?option=com_easyblog');
				break;
		}

		return $link;
	}

}
