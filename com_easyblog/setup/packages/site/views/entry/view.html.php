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

class EasyBlogViewEntry extends EasyBlogView
{
	/**
	 * Main display for the blog entry view
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Get the blog post id from the request
		$id = $this->input->get('id', 0, 'int');

		// Load the blog post now
		$post = EB::post($id);

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$post->id) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// check if the blog post is associated with the correct menu item or not.
		$menuItemId = $this->input->getInt('Itemid', 0);
		if (!$post->validatePostMenuItemAccess($menuItemId)) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// Validate whether the current viewer can able to access this blog post under current site language
		$post->validateMultilingualPostAccess($post);

		// After the post is loaded, set it into the cache
		EB::cache()->insert([$post]);

		// Render necessary data on the headers
		$post->renderHeaders();

		// Check if blog is password protected.
		$protected = $this->isProtected($post);

		if ($protected !== false) {
			return $this->renderProtectedPost($post);
		}

		// Perform validation checks to see if post is valid
		$exception = $post->checkView();

		if ($exception instanceof EasyBlogException) {
			EB::getErrorRedirection($exception->getMessage());
		}

		// Increment the hit counter for the blog post.
		$post->hit();

		// Format the post
		$post = EB::formatter('entry', $post);

		// Add bloggers breadcrumbs
		if (!EBR::isCurrentActiveMenu('blogger', $post->creator->id) && $this->config->get('layout_blogger_breadcrumb')) {
			$this->setPathway($post->creator->getName(), $post->creator->getPermalink());
		}

		// Add entry breadcrumb
		if (!EBR::isCurrentActiveMenu('entry', $post->id)) {
			$this->setPathway($post->title, '');
		}

		// Load up the blog model
		$model = EB::model('Blog');

		// Add canonical URLs for the blog post
		$canonical = 'index.php?option=com_easyblog&view=entry&id=' . $post->id;
		$routeCanonical = true;

		// If the feed is imported from external feed, determine if we should be adding the original post permalink as canonical
		if ($post->isFromFeed()) {
			$feedHistory = EB::table('FeedHistory');
			$feedHistory->load(array('post_id' => $post->id));

			$feed = $feedHistory->getFeedTable();
			$feedParams = $feed->getParams();

			// Insert canonical link to the original parent
			if ($feedParams->get('canonical', false)) {
				$feedHistoryParams = $feedHistory->getParams();
				$originalPermalink = $feedHistoryParams->get('permalink', '');

				if ($originalPermalink) {
					$canonical = $originalPermalink;
					$routeCanonical = false;
				}
			}
		}

		// If there is a canonical link for the post, it should have the highest precedence
		if ($post->canonical) {
			$canonical = $post->canonical;
			$routeCanonical = false;
		}

		$this->canonical($canonical, $routeCanonical);

		// Add AMP metadata on the page
		if ($this->config->get('main_amp')) {
			$this->amp($post->getPermalink(false, false, 'amp'), false);
		}

		// Add preload link tag for post cover
		$cover = $post->getImage(EB::getCoverSize('cover_size_entry'), $this->config->get('layout_post_image_placeholder'), false, false);

		if ($cover && EB::image()->isImage($cover)) {
			$this->preload($cover, 'image');
		}

		// Retrieve Google Adsense codes
		$adsense = EB::adsense()->html($post);

		// If a custom theme is setup for entries in the category, set a different theme
		if (!empty($post->category->theme)) {
			$this->setTheme($post->category->theme);
		}

		$theme = EB::themes();

		// Get the menu params associated with this post
		$params = $post->getMenuParams();

		if (!$post->posttype) {
			$post->posttype = 'standard';
		}

		// we need to test here if we should display the entry toolbars or admin toolbars or not to
		// prevent div.eb-entry-tools div added
		$hasEntryTools = false;
		$hasAdminTools = false;

		// lets test for entry tools
		if ($params->get('post_font_resize', true) ||
			($this->config->get('main_reporting') && (!$this->my->guest || $this->my->guest && $this->config->get('main_reporting_guests')) && $params->get('post_reporting', true)) ||
			$params->get('post_print', true) || $post->canFavourite()) {
			$hasEntryTools = true;
		}

		//now we test the entry admin tools
		if (FH::isSiteAdmin() ||
			($post->isMine() && !$post->hasRevisionWaitingForApproval()) ||
			($post->isMine() && $this->acl->get('publish_entry')) ||
			($post->isMine() && $this->acl->get('delete_entry')) ||
			$this->acl->get('feature_entry') ||
			$this->acl->get('moderate_entry')) {
			$hasAdminTools = true;
		}

		// load language for blog app in mini header
		if ($this->config->get('integrations_easysocial_miniheader') && EB::easysocial()->exists()) {

			ES::initialize();

			$cluster = $post->source_type;
			$cluster = str_replace('easysocial.', '', $cluster);

			ES::language()->loadApp($cluster,'blog');
		}

		$previousPostId = false;
		$exclude = [];

		// Prepare navigation object
		$navigation = $model->getBlogNavigation($post);

		if ($params->get('pagination_style') == 'autoload' && $navigation->prev && isset($navigation->prev->id)) {
			$previousPostId = $navigation->prev->id;
			$exclude[] = $post->id;
		}

		$showIntro = $params->get('show_intro', true);
		$requireLogin = $post->requiresLoginToRead();

		$options = [
			'showIntro' => $showIntro,
			'requireLogin' => $requireLogin
		];

		$content = $this->getContent($post, $options);

		$exclude = json_encode($exclude);

		// Get the post rating value
		$ratings = $post->getRatings();

		$this->theme->entryParams = $params;

		$this->set('protected', false);
		$this->set('preview', false);
		$this->set('post', $post);
		$this->set('content', $content);
		$this->set('requireLogin', $requireLogin);
		$this->set('navigation', $navigation);
		$this->set('adsense' , $adsense);
		$this->set('hasEntryTools', $hasEntryTools);
		$this->set('hasAdminTools', $hasAdminTools);
		$this->set('previousPostId', $previousPostId);
		$this->set('exclude', $exclude);
		$this->set('ratings', $ratings);

		parent::display('entry/default/wrapper');
	}

	/**
	 * Login layout for entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function login()
	{
		$return = $this->input->get('return', '', 'string');

		if (!$return) {
			$return = base64_encode(EBR::_('index.php?option=com_easyblog', false));
		}

		$this->set('message', 'COM_EASYBLOG_PLEASE_LOGIN_TO_READ_FULL_ENTRY');
		$this->set('return', $return);

		parent::display('login/default');
	}

	/**
	 * Displays the latest entry on the site using the entry view
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function latest()
	{
		// Fetch the latest blog entry
		$model = EB::model('Blog');

		// Get the current active menu's properties.
		$menu = $this->app->getMenu()->getActive();
		$inclusion = '';

		if (is_object($menu)) {
			$inclusion = EB::getCategoryInclusion($menu->getParams()->get('inclusion'));
		}

		// Retrieve a list of featured blog posts on the site.
		$featured = $model->getFeaturedBlog($inclusion);
		$excludeIds = [];

		// Test if user also wants the featured items to be appearing in the single latest menu on entry page.
		// Otherwise, we'll need to exclude the featured id's from appearing on the single latest entr page.
		if (!$this->params->get('entry_include_featured', true)) {
			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		$items = $model->getBlogsBy('latest', 0, '', 1, EBLOG_FILTER_PUBLISHED, null, true, $excludeIds, false, false, true, array(), $inclusion);

		if (is_array($items) && !empty($items)) {
			$this->input->set('id', $items[0]->id);
			return $this->display();
		}

		echo JText::_( 'COM_EASYBLOG_NO_BLOG_ENTRY' );
	}

	/**
	 * Renders the blog post preview
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function preview($tpl = null)
	{
		// Get the blog post id from the request
		$id = $this->input->get('uid', '', 'default');

		// Load the blog post now
		$post = EB::post($id);

		// After the post is loaded, set it into the cache
		EB::cache()->insert(array($post));

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$post->id) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// Perform validation checks to see if post is valid
		$exception = $post->checkViewPreview();

		if ($exception instanceof EasyBlogException) {
			throw EB::exception($exception->getMessage(), 404);
		}

		// Render necessary data on the headers
		$post->renderHeaders(array('isPreview' => true));

		// Check if blog is password protected.
		$protected = $this->isProtected($post);

		if ($protected !== false) {
			return $this->renderProtectedPost($post);
		}

		// If the viewer is the owner of the blog post, display a proper message
		if ($this->my->id == $post->created_by && !$post->isPublished()) {
			$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_OWNER');
		}

		if (FH::isSiteAdmin() && !$post->isPublished()) {
			$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_ADMIN');
		}

		// Format the post
		$post = EB::formatter('entry', $post);

		// Add bloggers breadcrumbs
		if (!EBR::isCurrentActiveMenu('blogger', $post->creator->id) && $this->config->get('layout_blogger_breadcrumb')) {
			$this->setPathway($post->creator->getName(), $post->creator->getPermalink());
		}

		// Add entry breadcrumb
		if (!EBR::isCurrentActiveMenu('entry', $post->id)) {
			$this->setPathway($post->title, '');
		}

		// Load up the blog model
		$model = EB::model('Blog');

		// Add canonical URLs for the blog post
		$this->canonical('index.php?option=com_easyblog&view=entry&id=' . $post->id);

		// Retrieve Google Adsense codes
		$adsense = EB::adsense()->html($post);

		// If a custom theme is setup for entries in the category, set a different theme
		if (!empty($post->category->theme)) {
			$this->setTheme($post->category->theme);
		}

		// Check if the user subscribed to this post.
		$isBlogSubscribed = $model->isBlogSubscribedEmail($post->id, $this->my->email);

		$theme = EB::themes();

		if (!$post->posttype) {
			$post->posttype = 'standard';
		}

		// We will always allow tools to be enabled by default in preview layout.
		$hasEntryTools = true;
		$hasAdminTools = true;

		// We need to prepare the content here so that all the trigger will work correctly.
		$showIntro = $theme->params->get('show_intro', true);

		$options = [
			'showIntro' => $showIntro,
			'isPreview' => true
		];

		$content = $this->getContent($post, $options);

		$navigation = $model->getBlogNavigation($post);

		// Get the menu params associated with this post
		$params = $post->getMenuParams();

		$this->theme->entryParams = $params;

		// Get the post rating value
		$ratings = $post->getRatings();

		$this->set('preview', true);
		$this->set('protected', false);
		$this->set('post', $post);
		$this->set('content', $content);
		$this->set('requireLogin', false);
		$this->set('navigation', $navigation);
		$this->set('previousPostId', false);
		$this->set('exclude', []);
		$this->set('adsense' , $adsense);
		$this->set('isBlogSubscribed', $isBlogSubscribed);
		$this->set('hasEntryTools', $hasEntryTools);
		$this->set('hasAdminTools', $hasAdminTools);
		$this->set('ratings', $ratings);

		parent::display('entry/default/wrapper');
	}

	/**
	 * Retrieve the content of the blog post
	 *
	 * @since	6.0.0
	 * @access	private
	 */
	private function getContent(EasyBlogPost $post, $options = [])
	{
		$showIntro = EB::normalize($options, 'showIntro', true);
		$isPreview = EB::normalize($options, 'isPreview', false);
		$requireLogin = EB::normalize($options, 'requireLogin', false);

		if ($isPreview || (!$isPreview && !$requireLogin && $post->isPending())) {
			if ($showIntro) {
				$content = $post->getContent(EASYBLOG_VIEW_ENTRY, true, null, [
					'isPreview' => true,
					'ignoreCache' => true
				]);
			}

			if (!$showIntro) {
				$content = $post->getContentWithoutIntro(EASYBLOG_VIEW_ENTRY, true, [
					'isPreview' => true,
					'ignoreCache' => true
				]);
			}
		}

		if (!$isPreview && !$post->isPending()) {
			// If the post is required to login to read, we only show the introtext
			if ($requireLogin) {
				$content = $post->getIntro();
			}

			if (!$requireLogin) {
				$content = $showIntro ? $post->getContent(EASYBLOG_VIEW_ENTRY) : $post->getContentWithoutIntro();
			}
		}

		return $content;
	}

	/**
	 * Renders protected post layout
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	private function renderProtectedPost(EasyBlogPost $post)
	{
		$post = EB::formatter('entry', $post);

		// Set the return url to the current url
		$return = base64_encode($post->getPermalink(false));

		// Get the menu params associated with this post
		$params = $post->getMenuParams();
		$this->theme->entryParams = $params;

		// we need to test here if we should display the entry toolbars or admin toolbars or not to
		// prevent div.eb-entry-tools div added
		$hasEntryTools = false;
		$hasAdminTools = false;

		// lets test for entry tools
		if ($params->get('post_font_resize', true) ||
			($this->config->get('main_reporting') && (!$this->my->guest || $this->my->guest && $this->config->get('main_reporting_guests')) && $params->get('post_reporting', true))) {
			$hasEntryTools = true;
		}

		//now we test the entry admin tools
		if (FH::isSiteAdmin() ||
			($post->isMine() && !$post->hasRevisionWaitingForApproval()) ||
			($post->isMine() && $this->acl->get('publish_entry')) ||
			($post->isMine() && $this->acl->get('delete_entry')) ||
			$this->acl->get('feature_entry') ||
			$this->acl->get('moderate_entry')) {
			$hasAdminTools = true;
		}

		$previousPostId = false;

		// Prepare navigation object
		$model = EB::model('Blog');
		$navigation = $model->getBlogNavigation($post);

		if ($params->get('pagination_style') == 'autoload' && $navigation->prev && isset($navigation->prev->id)) {
			$previousPostId = $navigation->prev->id;
		}

		$this->set('preview', false);
		$this->set('protected', true);
		$this->set('previousPostId', $previousPostId);
		$this->set('exclude', '');
		$this->set('post', $post);
		$this->set('hasEntryTools', $hasEntryTools);
		$this->set('hasAdminTools', $hasAdminTools);
		$this->set('previousPostId', $previousPostId);
		$this->set('previousPostId', $previousPostId);

		parent::display('entry/default/wrapper');

		return true;
	}

	/**
	 * Determines if the current post is protected
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	private function isProtected(EasyBlogPost $post)
	{
		if (!$this->config->get('main_password_protect') || !$post->isPasswordProtected() || FH::isSiteAdmin() || $post->verifyPassword()) {
			return false;
		}

		return true;
	}
}
