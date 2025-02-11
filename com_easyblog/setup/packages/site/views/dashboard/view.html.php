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

class EasyBlogViewDashboard extends EasyBlogView
{
	/**
	 * Default display for dashboard
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());

		$user = EB::user($this->my->id);

		// Get the page title for this page.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Retrieve a list of blog posts ordered by the popularity
		$blogModel = EB::model('Blog');
		$posts = $blogModel->getBlogsBy('blogger', $this->my->id, 'popular', 5);
		$posts = EB::formatter('list', $posts);

		$totalPosts = $blogModel->getTotalBlogs($this->my->id);
		$latest = $blogModel->getLatestPostByAuthor($this->my->id);

		// Retrieve a list of categories created by the user
		$categoriesModel = EB::model('Categories');

		//(We need to initially retrieve the categories to utilize FOUND_ROWS()
		$categories = $categoriesModel->getCategoriesByBlogger($this->my->id);
		$totalCategories = $categoriesModel->getTotal();

		// Get total pending entries
		$pending = 0;

		// Retrieve the total number of pending posts
		if ($this->acl->get('manage_pending')) {

			// Get total pending blog posts
			$model = EB::model('Blogs');
			$pending = $model->getTotalPending();
		}

		// Get most commented post from this author
		$mostCommentedPosts = $blogModel->getMostCommentedPostByAuthor($this->my->id, 5);

		// Get a list of recent comments made on the author's post
		$commentsModel = EB::model('Comments');
		$recentComments = $commentsModel->getRecentCommentsOnAuthor($this->my->id, 5);

		// Get a list of top commenters on the person's blog
		$totalHits = $blogModel->getTotalHits($this->my->id);

		$this->set('recentComments', $recentComments);
		$this->set('mostCommentedPosts', $mostCommentedPosts);
		$this->set('pending', $pending);
		$this->set('latest', $latest);
		$this->set('posts', $posts);
		$this->set('totalPosts', $totalPosts);
		$this->set('totalCategories', $totalCategories);
		$this->set('totalHits', $totalHits);

		parent::display('dashboard/stats/default');
	}

	/**
	 * Retrieves the dropbox data for the current user
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function getFlickrData()
	{
		// Test if the user is already associated with dropbox
		$oauth  = EB::table('OAuth');

		// Test if the user is associated with flickr
		$state	= $oauth->loadByUser($this->my->id, EBLOG_OAUTH_FLICKR);

		$data   = new stdClass();
		$data->associated	= $state;
		$data->callback  = 'flickr' . rand();
		$data->redirect  = base64_encode(rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=media&layout=flickrLogin&tmpl=component&callback=' . $data->callback);

		// Default login to the site
		$data->login = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&controller=oauth&task=request&type=' . EBLOG_OAUTH_FLICKR . '&tmpl=component&redirect=' . $data->redirect;


		if (EB::isFromAdmin()) {
			$data->login = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&c=oauth&task=request&type=' . EBLOG_OAUTH_FLICKR . '&tmpl=component&redirect=' . $data->redirect . '&id=' . $this->my->id;
		}

		return $data;
	}

	/**
	 * Displays the manage autoposting screen
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function autoposting()
	{
		if (!$this->config->get('integrations_twitter') && !$this->config->get('integrations_linkedin')) {
			throw EB::exception(JText::_('COM_EASYBLOG_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW'), 500);
		}

		if (!$this->config->get('integrations_twitter_centralized_and_own') && !$this->config->get('integrations_linkedin_centralized_and_own')) {
			throw EB::exception(JText::_('COM_EASYBLOG_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW'), 500);
		}

		// Ensure that the user is logged in
		EB::requireLogin();

		$user = EB::user($this->my->id);

		// Get the page title for this page.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_AUTOPOSTING_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_AUTOPOSTING_BREADCRUMB'), '');

		// Load twitter data for this user
		$twitter = EB::table('Oauth');
		$twitter->load(array('user_id' => $this->my->id, 'type' => EBLOG_OAUTH_TWITTER, 'system' => 0));

		// Load linkedin data for this user
		$linkedin = EB::table('Oauth');
		$linkedin->load(array('user_id' => $this->my->id, 'type' => EBLOG_OAUTH_LINKEDIN, 'system' => 0));

		$this->set('twitter', $twitter);
		$this->set('linkedin', $linkedin);

		parent::display('dashboard/autoposting/default');
	}

	/**
	 * Displays the edit profile screen
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function profile()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Get the page title for this page.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_SETTINGS_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_SETTINGS_BREADCRUMB'), '');

		// Get editor
		$editor = EB::getEditor();

		// Load the user's profile
		$profile = EB::user($this->my->id);

		// Get feedburner data
		$feedburner	= EB::table('Feedburner');
		$feedburner->load($this->my->id);

		// Get user's adsense code
		$adsense = EB::table('Adsense');
		$adsense->load($this->my->id);

		// Get meta info for this blogger
		$metasModel = EB::model('Metas');
		$meta = $metasModel->getMetaInfo(META_TYPE_BLOGGER, $this->my->id);

		// Remove duplicate meta. #1865
		if ($meta->id) {
			$metasModel->deleteMetas($this->my->id, META_TYPE_BLOGGER, $meta->id);
		}

		// Load facebook data for this user
		$facebook = EB::table('Oauth');
		$facebook->load(array('user_id' => $this->my->id, 'type' => EBLOG_OAUTH_FACEBOOK, 'system' => 0));

		// Load users params
		$params = $profile->getParam();

		// Load language from com_users
		$language = JFactory::getLanguage();
		$language->load('com_users');

		$otpConfig = EB::getOtpConfig();
		$twoFactorMethods = FH::getTwoFactorMethods();
		$twoFactorForms = EB::getTwoFactorForms($otpConfig);


		$this->set('otpConfig', $otpConfig);
		$this->set('twoFactorMethods', $twoFactorMethods);
		$this->set('twoFactorForms', $twoFactorForms);
		$this->set('params', $params);
		$this->set('editor', $editor);
		$this->set('feedburner', $feedburner);
		$this->set('adsense', $adsense);
		$this->set('profile', $profile);
		$this->set('meta', $meta);
		$this->set('facebook', $facebook);

		parent::display('dashboard/account/default');
	}

	/**
	 * Displays a list of blog posts created on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function entries()
	{
		// Only allow logged in users on this page
		EB::requireLogin();

		// Ensure that the user has access to this section
		$this->checkAcl('add_entry');

		// Get the user group acl
		$aclLib = EB::acl();

		//check if this is coming from write layout or not.
		$isWrite = $this->getLayout() == 'write' ? 1 : 0;
		$defaultCategory = '';

		if ($isWrite) {
			$defaultCategory = $this->input->get('category', '', 'int');

			if ($defaultCategory) {
				$defaultCategory = '&category=' . $defaultCategory;
			} else {
				$defaultCategory = '';
			}
		}

		// Get the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_BREADCRUMB'), '');

		$activeMenu = $this->app->getMenu()->getActive();
		$defaultFilter = 'all';

		if ($activeMenu) {
			$params = $activeMenu->getParams();
			$defaultFilter = $params->get('filter', $defaultFilter);
		}

		$state = $this->input->get('filter', $defaultFilter, 'default');
		$isPendingState = false;

		// if that is pending filter state
		if ($state == 'pending') {
			$isPendingState = true;
		}

		// Available status options for blog post
		$stateOptions = [
			'publish' => EASYBLOG_POST_PUBLISHED, 
			'unpublish' => EASYBLOG_POST_UNPUBLISHED, 
			'pending' => EASYBLOG_POST_PENDING, 
			'scheduled' => EASYBLOG_POST_SCHEDULED, 
			'drafts' => EASYBLOG_POST_DRAFT, 
			'trash' => EASYBLOG_DASHBOARD_TRASHED
		];

		// Get value of selected state
		$entryState = 'all';

		if (in_array($state, array_keys($stateOptions))) {
			$entryState = $stateOptions[$state];
		}

		// Backward compatibility
		if (is_numeric($state) && in_array($state, array_values($stateOptions))) {
			$entryState = $state;
		}

		// Determines if the user is searching for post
		$search = $this->input->get('post-search', '', 'string');

		// Determines if the blog posts should be filtered by specific category
		$categoryFilter = $this->input->get('category', 0, 'int');

		// Get limit
		$limit = EB::getLimit();

		// Retrieve the posts
		$model = EB::model('Dashboard');

		$userId = $this->my->id;
		$isModerator = false;

		// if the user have moderation entry permission, so it will show all the blog post on blog entries dashboard page.
		if ($aclLib->get('moderate_entry')) {
			$userId = '';
			$isModerator = true;
		}

		//sorting
		$sort = $this->input->get('sort', '', 'string');
		$ordering = $this->input->get('ordering', '', 'string');

		$options = ['category' => $categoryFilter, 'state' => $entryState, 'search' => $search, 'limit' => $limit];

		if ($sort) {
			$options['sort'] = $sort;

			if ($ordering) {
				$options['ordering'] = $ordering;
			}
		}

		$result = $model->getEntries($userId, $options);

		// Get pagination
		$pagination = $model->getPagination();

		$pagination->setAdditionalUrlParam('view', 'dashboard');
		$pagination->setAdditionalUrlParam('layout', 'entries');

		if ($categoryFilter) {
			$pagination->setAdditionalUrlParam('category', $categoryFilter);
		}

		if ($state !== '') {
			$pagination->setAdditionalUrlParam('filter', $state);
		}

		if ($search) {
			$pagination->setAdditionalUrlParam('post-search', $search);
		}

		if ($sort) {
			$pagination->setAdditionalUrlParam('sort', $sort);
		}

		if ($ordering) {
			$pagination->setAdditionalUrlParam('ordering', $ordering);
		}

		// Format the posts
		$posts = EB::formatter('list', $result);

		// Get oauth clients
		$clients = array('twitter', 'facebook', 'linkedin');
		$oauthClients = array();

		foreach ($clients as $client) {
			$oauth 	= EB::table('OAuth');
			$exists = $oauth->load(array('user_id' => $userId, 'type' => $client));

			if ($exists && $this->acl->get("update_" . $client) && $this->config->get('integrations_' . $client . '_centralized_and_own')) {
				$oauthClients[]	= $oauth;
			}
		}

		$categoryDropdown = EB::populateCategories('', '', 'select', 'category', $categoryFilter, false, true, true, array(), 'data-eb-filter-dropdown', 'COM_EASYBLOG_FILTER_SELECT_CATEGORY', 'form-control pull-right');

		$revisionModel = EB::model('Revisions');

		// lets preload the revisions count.
		if ($posts) {
			$pIds = [];

			foreach($posts as $post) {
				$pIds[] = $post->id;
			}

			$revisionModel->getRevisionCount($pIds, 'cache');
		}

		// Get revisions for the post
		foreach ($posts as $post) {
			$versions = $revisionModel->getAllRevisions($post->id);
			$post->versions = $versions;
		}

		$rejectModel = EB::model('postreject');

		// Identify if the post gets rejected
		foreach ($posts as $post) {
			$rejected = $rejectModel->isRejected($post->id);
			$post->isRejected = $rejected;
		}

		$this->set('pagination', $pagination);
		$this->set('posts', $posts);
		$this->set('search', $search);
		$this->set('categoryFilter', $categoryFilter);
		$this->set('oauthClients', $oauthClients);
		$this->set('state', $state);
		$this->set('isWrite', $isWrite);
		$this->set('defaultCategory', $defaultCategory);
		$this->set('categoryDropdown', $categoryDropdown);
		$this->set('sort', $sort);
		$this->set('ordering', $ordering);
		$this->set('isModerator', $isModerator);
		$this->set('isPendingState', $isPendingState);

		if ($state == 'trash') {
			$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=' . EASYBLOG_DASHBOARD_TRASHED, false);
			$this->set('return', base64_encode($return));

			echo parent::display('dashboard/trash/default');
			return;
		}

		echo parent::display('dashboard/entries/default');
	}

	/**
	 * Displays a list of blog posts created on the site
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function favourites()
	{
		EB::requireLogin();

		// User must have access to view pending blog posts
		if (!$this->config->get('main_favourite_post')) {
			throw EB::exception(JText::_('COM_EB_ERROR_FEATURE_NOT_ENABLE'), 500);
		}

		$model = EB::model('Favourites');

		$search = $this->input->get('post-search', '', 'string');

		//sorting
		$sort = $this->input->get('sort', '', 'string');
		$ordering = $this->input->get('ordering', '', 'string');

		// Get limit
		$limit = EB::getLimit();

		$options = array();
		$options['userId'] = $this->my->id;
		$options['limit'] = $limit;
		$options['search'] = $search;

		$posts = $model->getFavouritesPost($options);
		$posts = EB::formatter('list', $posts);

		$pagination = $model->getPagination();

		if ($search) {
			$pagination->setAdditionalUrlParam('post-search', $search);
		}

		$this->set('posts', $posts);
		$this->set('search', $search);
		$this->set('pagination', $pagination);
		$this->set('sort', $sort);
		$this->set('ordering', $ordering);

		parent::display('dashboard/favourites/default');
	}

	/**
	 * Renders a list of post templates for the user
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function templates()
	{
		EB::requireLogin();

		$model = EB::model('Templates');

		if (!$this->acl->get('create_post_templates')) {
			return $this->app->redirect(EB::_('index.php?option=com_easyblog&view=dashboard', false));
		}

		// Get editor type
		$editor = $this->config->get('layout_editor');
		$type = 'legacy';

		if ($editor == 'composer' || $editor == 'redactorjs') {
			$type = 'ebd';
		}

		$limit = EB::getLimit();

		$templates = $model->getPostTemplates($this->my->id, true, false, $type, $limit);
		$user = EB::user($this->my->id);

		$pagination = $model->getPagination();

		if ($pagination) {
			$pagination->setAdditionalUrlParam('view', 'dashboard');
			$pagination->setAdditionalUrlParam('layout', 'templates');
		}

		$search = $this->input->get('search', '', 'default');

		$disabled = false;

		if (!$this->acl->get('create_post_templates') && !FH::isSiteAdmin()) {
			$disabled = true;
		}

		$this->set('pagination', $pagination);
		$this->set('templates', $templates);
		$this->set('user', $user);
		$this->set('search', $search);
		$this->set('pagination', $pagination);
		$this->set('disabled', $disabled);

		parent::display('dashboard/templates/default');
	}

	/**
	 * Renders a list of block templates for the user
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function blockTemplates()
	{
		EB::requireLogin();

		if (!$this->acl->get('create_block_templates') && !FH::isSiteAdmin()) {
			return $this->app->redirect(EB::_('index.php?option=com_easyblog&view=dashboard', false));
		}

		$limit = EB::getLimit();
		$model = EB::model('BlockTemplates');

		$blockTemplates = $model->getBlockTemplates(array(
			'userId' => $this->my->id,
			'includeUnpublish' => true,
			'includeGlobal' => false,
			'limit' => $limit
		));

		$user = EB::user($this->my->id);

		$pagination = $model->getPagination();

		if ($pagination) {
			$pagination->setAdditionalUrlParam('view', 'dashboard');
			$pagination->setAdditionalUrlParam('layout', 'blockTemplates');
		}

		$search = $this->input->get('search', '', 'default');

		$this->set('pagination', $pagination);
		$this->set('blockTemplates', $blockTemplates);
		$this->set('user', $user);
		$this->set('search', $search);
		$this->set('pagination', $pagination);

		parent::display('dashboard/blocktemplates/default');
	}

	/**
	 * Renders category form
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function blockTemplatesForm()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Check if the user is allowed to create categories
		$this->checkAcl('create_block_templates');

		$id = $this->input->get('id', 0, 'int');

		$template = EB::table('blockTemplates');
		$template->load($id);

		if (!$id || !$template->id) {
			return $this->app->redirect(EB::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplates', false));
		}

		$title = EB::getPageTitle(JText::_('COM_EB_DASHBOARD_EDITBLOCKTEMPLATES_PAGE_TITLE'));

		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// // Set views breadcrumbs
		// $this->setViewBreadcrumb($this->getName());
		// $this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_BREADCRUMB'), '');

		$this->set('template', $template);

		parent::display('dashboard/blocktemplates/form');
	}

	/**
	 * Displays a list of versions for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function compare()
	{
		// Only allow logged in users on this page
		EB::requireLogin();
		//Get Revisions table
		$revisionModel = EB::model('Revisions');

		// get blogid
		$blogId = $this->input->get('blogid', '');

		//Load the version for the blog post
		$versions		= $revisionModel->getAllBlogs($blogId);

		$this->set('versions', $versions);

		echo parent::display('dashboard/version/default');
	}

	/**
	 * Displays a comparison of two versions for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function diff()
	{
		require(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/htmldiff/html_diff.php');

		// Only allow logged in users on this page
		EB::requireLogin();

		//Get revision table
		$Revision = EB::table('Revision');

		// get blogid
		$versionId			= $this->input->get('id', '');
		$postId			= $this->input->get('post_id', '');

		//Load the version for the blog post
		$currentBlog		= $Revision->getCurrentBlog($postId);
		$compareBlog		= $Revision->getCompareBlog($versionId);

		$currentData	= json_decode($currentBlog->params);
		$compareData	= json_decode($compareBlog->params);

		$diff	=	html_diff($currentData->intro,$compareData->intro, true);

		// Get category title
		$category = EB::table('Category');
		$category->load($currentData->category_id);
		$dataArr = array();
		$dataArr['catOld'] = $category->title;
		$category->load($compareData->category_id);
		$dataArr['catNew'] = $category->title;

		$this->set('currentData', $currentData);
		$this->set('compareData', $compareData);
		$this->set('dataArr', $dataArr);
		$this->set('diff', $diff);
		$this->set('blogId', $postId);
		$this->set('versionId', $versionId);

		echo parent::display('dashboard/version/compare');
	}

	/**
	 * Renders a list of comments for the author or admin to manage comments
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function comments()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb();
		$this->setPathway('COM_EASYBLOG_DASHBOARD_COMMENTS_BREADCRUMB', '');

		// Load up comments model
		$model = EB::model('Comment');

		// Filters
		$search = $this->input->get('post-search', '', 'string');
		$filter = $this->input->get('filter', 'all', 'word');
		$sort = 'latest';

		// Get limit
		$limit = EB::getLimit();


		// If the user is allowed to manage comments, allow them to view all comments
		if ($this->acl->get('manage_comment')) {
			$result = $model->getComments(0, '', $sort, '', $search, $filter, $limit);
		} else {
			$result = $model->getComments(0, $this->my->id, $sort, 'comment', $search, $filter, $limit);
		}

		// Get pagination
		$pagination	= $model->getPagination();
		$comments = array();

		if ($result) {

			foreach ($result as $row) {

				$comment = EB::table('Comment');
				$comment->bind($row);

				$comment->isOwner = $this->my->id == $row->blog_owner;

				$comments[] = $comment;
			}
		}

		$this->set('search', $search);
		$this->set('filter', $filter);
		$this->set('comments', $comments);
		$this->set('pagination', $pagination);

		parent::display('dashboard/comments/default');
	}

	/**
	 * Renders category form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function categoryForm()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Check if the user is allowed to create categories
		$this->checkAcl('create_category');

		$id = $this->input->get('id', 0, 'int');

		if ($id) {
			$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_EDITCATEGORY_PAGE_TITLE'));
		} else {
			$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_NEWCATEGORY_PAGE_TITLE'));
		}
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_BREADCRUMB'), '');

		$category = EB::table('Category');
		$category->load($id);

		if (!$category->created) {

			$date = EB::date();

			$category->created = $date->toSql();
			$category->published = true;
			$category->autopost = true;
		}

		// Get assigned acl
		$groups = $category->getGroupAssignedACL();
		$usertags = $category->getUserAssignedACL();

		$parentList = EB::populateCategories('', '', 'select', 'parent_id', $category->parent_id);
		$editor = EB::getEditor();

		// Get the category params
		$params = $category->getParams();

		$this->set('parentList', $parentList);
		$this->set('params', $params);
		$this->set('editor', $editor);
		$this->set('groups', $groups);
		$this->set('category', $category);
		$this->set('usertags', $usertags);

		parent::display('dashboard/categories/form');
	}

	/**
	 * Renders a list of categories
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function categories()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Check if the user is allowed to create categories
		$this->checkAcl('create_category');

		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_BREADCRUMB'), '');

		// Get model
		$model = EB::model('Categories');

		// Get filters
		$order = $this->input->get('order', '', 'cmd');
		$search = $this->input->get('search', '', 'default');

		$bloggerId = $this->my->id;
		$showAuthorName = false;

		// if user is a super admin, we do not need to filter the author of the categories.
		if (FH::isSiteAdmin()) {
			$bloggerId = 0;
			$showAuthorName = true;
		}

		$limit = EB::getLimit();

		// Get categories
		$rows = $model->getCategoriesByBlogger($bloggerId, $order, $search, $limit);

		$pagination = $model->getPagination($bloggerId, $search);

		$categories = array();

		$category = EB::table('Category');
		$assignedACL = $category->getGroupAssignedACL();

		if (count($rows) > 0) {

			$authors = array();

			foreach ($rows as $row) {
				$category = EB::table('Category');
				$category->bind($row);

				$authors[] = $category->created_by;
				$categories[]	= $category;
			}

			EB::user($authors);
		}

		$this->set('order', $order);
		$this->set('search', $search);
		$this->set('categories', $categories);
		$this->set('pagination', $pagination);
		$this->set('assignedACL', $assignedACL);
		$this->set('showAuthorName', $showAuthorName);

		parent::display('dashboard/categories/default');
	}

	/**
	 * Renders tag form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function tagForm()
	{
		EB::requireLogin();

		$this->checkAcl('create_tag');

		$id = $this->input->get('id', 0, 'int');
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_NEWTAG_PAGE_TITLE'));

		if ($id) {
			$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_EDITTAG_PAGE_TITLE'));
		}

		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway('COM_EASYBLOG_DASHBOARD_TAGS_BREADCRUMB', '');

		$tag = EB::table('Tag');
		$tag->load($id);

		$this->set('tag', $tag);

		parent::display('dashboard/tags/form');
	}

	/**
	 * Displays a list of tags on the dashboard
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function tags()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Ensure that the user has access to the tags page
		$this->checkAcl('create_tag');

		// Set the page title
		$title 	= EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_TAGS_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_TAGS_BREADCRUMB'));

		// Load the tags
		$model = EB::model('Tags');

		// Get the current search behavior
		$search = $this->input->get('search', '', 'default');

		// Get the current sorting behavior
		$sort = $this->input->get('sort', 'post', 'cmd');

		// Get limit
		$limit = EB::getLimit();

		// Render the tags
		$tags = $model->getBloggerTags($this->my->id, $sort, $search, $limit);

		$pagination = $model->getPagination();
		$pagination->setAdditionalUrlParam('view', 'dashboard');
		$pagination->setAdditionalUrlParam('layout', 'tags');

		$this->set('search', $search);
		$this->set('sort', $sort);
		$this->set('tags', $tags);
		$this->set('pagination', $pagination);

		parent::display('dashboard/tags/default');
	}

	/**
	 * Displays a list of requests to join a team
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function requests()
	{
		// Require the user to be logged in.
		EB::requireLogin();

		// Ensure that the user really has access to this listing
		if (!FH::isSiteAdmin() && !EB::isTeamAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard', false));
		}

		// Set the page title
		$title 	= EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_REQUESTS_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB_REQUESTS'));

		$search = $this->input->get('requests-search', '', 'string');
		$model = EB::model('TeamBlogs');
		$userId = FH::isSiteAdmin() ? '' : $this->my->id;

		$requests = $model->getRequests($userId, true, $search);

		foreach ($requests as &$request) {

			$request->user = EB::user($request->user_id);

			$request->team = EB::table('Teamblog');
			$request->team->load($request->team_id);

			$request->date = EB::date($request->created);
		}

		$pagination = $model->getPagination(true);

		$this->set('search', $search);
		$this->set('requests', $requests);
		$this->set('pagination', $pagination);

		parent::display('dashboard/requests/default');
	}

	/**
	 * Deprecated. Use @entries layout instead
	 *
	 * @deprecated	5.1
	 */
	public function revisions()
	{
		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=3', false);
		return $this->app->redirect($redirect);
	}

	/**
	 * Deprecated. Use @entries layout instead
	 *
	 * @deprecated	5.1
	 */
	public function pending()
	{
		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=4', false);
		return $this->app->redirect($redirect);
	}

	/**
	 * Displays a list of blog posts pending approval from admin
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function moderate()
	{
		// Require user to be logged in
		EB::requireLogin();

		// User must have access to view pending blog posts
		if ((!$this->acl->get('manage_pending') || !$this->acl->get('publish_entry')) && !FH::isSiteAdmin()) {
			throw EB::exception(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'), 500);
		}

		// Set the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_PENDING_PAGE_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB_PENDING_YOUR_REVIEW'));

		// Get filters
		$search = $this->input->get('post-search', '', 'string');

		$model = EB::model('Pending');
		$posts = $model->getBlogs();

		// Format the posts
		$posts = EB::formatter('list', $posts);

		// Get pagination
		$pagination	= $model->getPagination(true);

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('search', $search);

		parent::display('dashboard/moderate/default');
	}

	/**
	 * Renders the quick post form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function quickpost()
	{
		// Require user to be logged in
		EB::requireLogin();

		// Test if microblogging is allowed
		if (!$this->config->get('main_microblog')) {
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard', false));
		}

		// Test ACL if add entry is allowed
		if (!$this->acl->get('add_entry')) {
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard', false));
		}

		// Set the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_SHARE_A_STORY_TITLE'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Get active tabs
		$active = $this->input->get('type', 'standard', 'word');

		$link = '';

		if ($this->config->get('main_microblog_blogthis')) {
			$link = $this->input->get('link', '', 'default');
		}

		// Get a list of available auto post sites
		$twitter = EB::oauth()->isUserAssociated('twitter', $this->my->id);
		$linkedin = EB::oauth()->isUserAssociated('linkedin', $this->my->id);

		// Retrieve existing tags
		$tagsModel = EB::model('Tags');
		$tags = $tagsModel->getTags();

		// Generate the media manager key for the user's folder. Used for photo uploads
		$media = EB::mediamanager();
		$place = $media->getPlace('user:' . $this->my->id);
		$webcamKey = $media->getKey('user:' . $this->my->id . '/webcam');

		$defaultCategory = $this->input->get('categoryId', 0, 'int');

		$this->set('link', $link);
		$this->set('defaultCategory', $defaultCategory);
		$this->set('webcamKey', $webcamKey);
		$this->set('place', $place);
		$this->set('twitter', $twitter);
		$this->set('linkedin', $linkedin);
		$this->set('active', $active);
		$this->set('tags', $tags);

		parent::display('dashboard/quickpost/default');
	}

	/**
	 * Display list of teams on the site for team admin or site admin to manage
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function teamblogs()
	{
		EB::requireLogin();

		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS'));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		$model = EB::model('TeamBlogs');

		// Get the current search behavior
		$search = $this->input->get('search', '', 'default');

		$limit = EB::getLimit();

		// Get all teams available on the site for site admin
		if (FH::isSiteAdmin()) {
			$teamblogs = $model->getAllTeams($search, $limit);

			foreach ($teamblogs as &$team) {
				$table = EB::table('TeamBlog');
				$table->bind($team);

				$team = $table;
			}
		} else {
			$teamblogs = $model->getUserTeams($this->my->id, $search, $limit);
		}

		$pagination = $model->getPagination(true);

		$this->set('search', $search);
		$this->set('teams', $teamblogs);
		$this->set('pagination', $pagination);

		parent::display('dashboard/teamblogs/default');
	}

	/**
	 * Renders a teamblog form
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function teamblogForm()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Get the id if any
		$id = $this->input->get('id', 0, 'int');

		// Load the team blog
		$teamblog = EB::table('Teamblog');
		$teamblog->load($id);

		// Identify if this is new teamblog or editing
		$isNew = $teamblog->id ? false : true;

		// Check if the user is allowed to create team
		if ($isNew && !FH::isSiteAdmin() && !$this->acl->get('create_team_blog')) {
			$this->info->set(JText::_('COM_EASYBLOG_NOT_ALLOWED_TO_CREATE_TEAM_BLOG'), 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false));
		}

		// If id exist, check if the user able to edit this team or not
		if (!$isNew && !FH::isSiteAdmin() && !$teamblog->isTeamAdmin()) {
			$this->info->set(JText::_('COM_EASYBLOG_NOT_ALLOWED_TO_EDIT_TEAM_BLOG'), 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false));
		}

		if (!$isNew) {
			$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_EDITTEAMBLOG_PAGE_TITLE'));
		} else {
			$title = EB::getPageTitle(JText::_('COM_EASYBLOG_DASHBOARD_NEWTEAMBLOG_PAGE_TITLE'));
		}

		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set views breadcrumbs
		$this->setViewBreadcrumb($this->getName());
		$this->setPathway(JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_BREADCRUMB'), '');

		if (!$teamblog->created) {

			$date = EB::date();

			$teamblog->created = $date->toSql();
			$teamblog->published = true;
			$teamblog->autopost = true;
		}

		// Get editor
		$editor = EB::getEditor();

		$this->set('editor', $editor);
		$this->set('teamblog', $teamblog);

		parent::display('dashboard/teamblogs/form');
	}

	/**
	 * Displays a list of report posts on the site
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function reports()
	{
		EB::requireLogin();

		// User must have access to view pending blog posts
		if ((!$this->acl->get('manage_pending') || !$this->acl->get('publish_entry') || !$this->acl->get('moderate_entry')) && !FH::isSiteAdmin()) {
			throw EB::exception(JText::_('COM_EB_NO_PERMISSION_TO_MODERATE_REPORT_POST'), 500);
		}

		// Get the page title
		$title = EB::getPageTitle(JText::_('COM_EB_DASHBOARD_REPORTS_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		$search = $this->input->get('post-search', '', 'string');

		// Get limit
		$limit = EB::getLimit();

		$options = array();
		$options['limit'] = $limit;
		$options['search'] = $search;

		$model = EB::model('Reports');
		$reports = $model->getReportPosts($options);

		$pagination = $model->getPagination();

		if ($search) {
			$pagination->setAdditionalUrlParam('post-search', $search);
		}

		$this->set('reports', $reports);
		$this->set('search', $search);
		$this->set('pagination', $pagination);

		echo parent::display('dashboard/reports/default');
	}

	/**
	 * Displays a list of polls that the current user created
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function polls()
	{
		if (!$this->config->get('main_polls')) {
			throw EB::exception(JText::_('COM_EB_POLLS_FEATURE_DISABLED'), 500);
		}

		EB::requireLogin();

		// User must have access the poll management page
		if (!$this->acl->get('polls_manage') && !$this->acl->get('polls_create')) {
			throw EB::exception(JText::_('COM_EB_NO_PERMISSION_ACCESS_DASHBOARD_POLLS'), 500);
		}

		$limit = EB::getLimit();

		$filter = $this->input->get('filter', 'all', 'default');
		$search = $this->input->get('polls-search', '', 'string');

		$options = [];
		$options['state'] = $filter;
		$options['search'] = $search;
		$options['userId'] = $this->my->id;
		$options['limit'] = $limit;

		$model = EB::model('Polls');
		$polls = $model->getPolls($options);

		$pagination = $model->getPagination();
		$pagination = $pagination->getPagesLinks();

		// Set the page title
		$title = EB::getPageTitle(JText::_('COM_EB_DASHBOARD_POLLS_PAGE_TITLE'));
		$this->setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		// Set the breadcrumbs
		$this->setViewBreadcrumb('dashboard');
		$this->setPathway(JText::_('COM_EB_DASHBOARD_POLLS_BREADCRUMB'), '');

		$createButton = null;

		if ($this->acl->get('polls_create')) {
			$createButton = [
				'icon' => 'fdi fas fa-poll-h',
				'text' => 'COM_EB_BLOCKS_POLL_CREATE_BUTTON',
				'attribute' => 'data-dashboard-poll-compose',
				'style' => 'primary'
			];
		}

		$this->set('createButton', $createButton);
		$this->set('polls', $polls);
		$this->set('filter', $filter);
		$this->set('search', $search);
		$this->set('pagination', $pagination);

		parent::display('dashboard/polls/default');
	}
}
