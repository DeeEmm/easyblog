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
	 * Some description
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function loadmore()
	{
		// Get the blog post id from the request
		$id = $this->input->get('id', 0, 'default');

		// Load the blog post now
		$post = EB::post($id);

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$post->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// After the post is loaded, set it into the cache
		EB::cache()->insert(array($post));

		// Check if blog is password protected.
		$protected = $this->isProtected($post);

		if ($protected !== false) {
			return $this->renderProtectedPost($post);
		}

		// Perform validation checks to see if post is valid
		$exception = $post->checkView();

		// Ajax calls should never be redirecting
		if ($exception instanceof EasyBlogException) {
			throw EB::exception($exception->getMessage(), EASYBLOG_MSG_ERROR);
		}

		// Increment the hit counter for the blog post.
		$post->hit();

		// Format the post
		$post = EB::formatter('entry', $post);

		// Load up the blog model
		$model = EB::model('Blog');

		// Get author's recent posts.
		$recent = $this->getRecentPosts($post);

		// Retrieve Google Adsense codes
		$adsense = EB::adsense()->html($post);

		// Check if the user subscribed to this post.
		$subscription = EB::table('Subscriptions');

		if ($this->my->id) {
			$subscription->load(array('uid' => $post->id, 'utype' => 'entry', 'user_id' => $this->my->id));
		}

		$theme = EB::themes();

		// Prepare related post
		$relatedPosts = array();

		// Get the menu params associated with this post
		$params = $post->getMenuParams();

		// Related posts seems to be missing from the theme file.
		if ($params->get('post_related', true)) {
			$behavior = $params->get('post_related_behavior', 'tags');

			$options = [];
			$options['ordering'] = $params->get('post_related_ordering', 'created');
			$options['sort'] = $params->get('post_related_sort', 'desc');

			$relatedLimit = (int) $params->get('post_related_limit', 5);
			if ($relatedLimit < 0) {
				$relatedLimit = 5;
			}

			$relatedPosts = $model->getRelatedPosts($post->id, $relatedLimit, $behavior, $post->category->id, $post->getTitle(), $options);

			// Format the related posts image
			if ($relatedPosts) {
				foreach ($relatedPosts as $relatedPost) {
					$relatedPost->postimage = $relatedPost->getImage('thumbnail', true, true);
				}
			}
		}

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
			$params->get('post_print', true)) {
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

		$requireLogin = $post->requiresLoginToRead();
		$showIntro = $params->get('show_intro', true);

		if ($requireLogin) {
			$content = $post->getIntro();
		}

		if (!$requireLogin) {
			// We need to prepare the content here so that all the trigger will work correctly.
			if ($post->isPending()) {
				$content = $showIntro ? $post->getContent(EASYBLOG_VIEW_ENTRY, true, null, ['isPreview' => true, 'ignoreCache' => true]) : $post->getContentWithoutIntro(EASYBLOG_VIEW_ENTRY, true, ['isPreview' => true, 'ignoreCache' => true]);
			} 

			if (!$post->isPending()) {
				$content = $showIntro ? $post->getContent(EASYBLOG_VIEW_ENTRY) : $post->getContentWithoutIntro();
			}
		}

		// Fix issue with missing slashes on relative image. #1410
		$content = EB::string()->relAddSlashes($content);

		// Determines if the viewer has subscribed to the author
		$isBloggerSubscribed = false;

		if ($this->config->get('main_bloggersubscription')) {
			$bloggerModel = EB::model('Blogger');
			$isBloggerSubscribed = $bloggerModel->isBloggerSubscribedEmail($post->creator->id, $this->my->email);
		}

		// load language for blog app in mini header
		if (EB::easysocial()->exists()) {

			ES::initialize();

			$cluster = $post->source_type;
			$cluster = str_replace('easysocial.', '', $cluster);

			ES::language()->loadApp($cluster,'blog');
		}

		// Get posts to be excluded from the pagination
		$exclusion = $this->input->get('exclusion', '', 'string');
		$exclusion = json_decode($exclusion);

		$navigation = $model->getBlogNavigation($post, $exclusion);

		$previousPostId = false;

		// Retrieve previous post id if exists
		if ($navigation->prev && isset($navigation->prev->id)) {
			$previousPostId = $navigation->prev->id;
		}

		// Get the post rating value
		$ratings = $post->getRatings();

		$themes = EB::themes();
		$themes->set('isBloggerSubscribed', $isBloggerSubscribed);
		$themes->set('post', $post);
		$themes->set('content', $content);
		$themes->set('navigation', $navigation);
		$themes->set('relatedPosts', $relatedPosts);
		$themes->set('recent', $recent);
		$themes->set('preview', false);
		$themes->set('adsense' , $adsense);
		$themes->set('subscription', $subscription);
		$themes->set('hasEntryTools', $hasEntryTools);
		$themes->set('hasAdminTools', $hasAdminTools);
		$themes->set('previousPostId', $previousPostId);
		$themes->set('showIntro', $showIntro);
		$themes->set('ratings', $ratings);
		$themes->set('requireLogin', $requireLogin);

		$themes->entryParams = $params;

		$output = $themes->output('site/entry/default/default');

		$data = new stdClass();
		$data->contents = $output;
		$data->previousPostId = $previousPostId;
		$data->uri = $post->getPermalink();
		$data->exclusion = json_encode($exclusion);

		$data = json_encode($data);

		return $this->ajax->resolve($data);
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

		$theme = EB::themes();
		$theme->set('preview', false);
		$theme->set('protected', true);
		$theme->set('previousPostId', $previousPostId);
		$theme->set('exclude', '');
		$theme->set('post', $post);
		$theme->set('hasEntryTools', $hasEntryTools);
		$theme->set('hasAdminTools', $hasAdminTools);
		$theme->set('previousPostId', $previousPostId);
		$theme->set('previousPostId', $previousPostId);

		$output = $theme->output('site/entry/default/default.protected');

		$data = new stdClass();
		$data->contents = $output;
		$data->previousPostId = $previousPostId;


		$data = json_encode($data);

		return $this->ajax->resolve($data);

		return true;
	}

	/**
	 * Retrieves a list of recent posts
	 *
	 * @since	4.0
	 * @access	private
	 */
	public function getRecentPosts(EasyBlogPost &$post)
	{
		// Get the menu params associated with this post
		$params = $post->getMenuParams();
		$recent = array();

		if (!$params->get('show_author_box', true) || !$params->get('post_author_recent', true)) {
			return $recent;
		}

		$limit = (int) $params->get('post_author_recent_limit', 5);
		if ($limit < 0) {
			$limit = 5;
		}

		$model = EB::model('Blog');

		// exclude the current entry post from the author recent post section
		$result = $model->getBlogsBy('blogger', $post->created_by, 'latest', $limit, EBLOG_FILTER_PUBLISHED, false, false, array($post->id));

		if (!$result) {
			return $recent;
		}

		$posts = array();

		foreach ($result as $row) {
			$item = EB::post();
			$item->bind($row, array('force' => true));

			$posts[] = $item;
		}

		return $posts;
	}

	/**
	 * Displays confirmation to publish a previewed post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmUseRevision()
	{
		$uid = $this->input->get('uid', '', 'default');

		$post = EB::post($uid);

		if (!$uid || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Default return url.
		$return = base64_encode($post->getPermalink());

		// Theme uses back end language file
		EB::loadLanguages(JPATH_ADMINISTRATOR);

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $return);

		$output = $theme->output('site/entry/dialogs/userevision');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to publish a previewed post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmPublish()
	{
		$id = $this->input->get('id', 0, 'int');

		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Default return url.
		$return = base64_encode($post->getPermalink());

		// Theme uses back end language file
		EB::loadLanguages(JPATH_ADMINISTRATOR);

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $return);

		$output = $theme->output('site/entry/dialogs/publish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Move the post to trash
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function trash()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canDelete()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$post->trash();

		$this->info->set(JText::_('COM_EASYBLOG_DASHBOARD_TRASH_SUCCESS'), 'success');

		$return = EB::_('index.php?option=com_easyblog&view=latest', false);

		return $this->ajax->redirect($return);
	}

	/**
	 * Publish the blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function publish()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		$options = array();

		// require to normalise the post data if the post isnew
		if ($post->isnew) {
			$options = array('normalizeData' => true);
		}

		$post->publish($options);

		$this->info->set(JText::_('COM_EASYBLOG_POSTS_PUBLISHED_SUCCESS'), 'success');

		$return = $post->getPermalink();

		return $this->ajax->redirect($return);
	}

	/**
	 * Submit the post for approval
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function submitApproval()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		$post->published = EASYBLOG_POST_PENDING;

		try {
			$post->save();
		} catch (Exception $e) {
			$this->info->set($e->getMessage(), 'error');

			$return = $post->getPreviewLink(false);

			return $this->ajax->redirect($return);
		}

		$this->info->set(JText::_('COM_EASYBLOG_POST_SUBMITTED_FOR_APPROVAL'), 'success');

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		return $this->ajax->redirect($return);
	}

	/**
	 * Displays confirmation to unarchive a post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmUnarchive()
	{
		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/entry/dialogs/unarchive');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to archive a post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmArchive()
	{
		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/entry/dialogs/archive');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays a trash confirmation dialog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDelete()
	{
		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to delete.
		if (!$post->canDelete()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/entry/dialogs/trash');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the dialog confirmation to unpublish a post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmUnpublish()
	{
		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		// Load up the blog post
		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		// Check if the user has access to approve
		if (!$post->canModerate() && !$post->canPublish()) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NO_PERMISSIONS_TO_MODERATE'));
		}

		// Get the return url if there's any so that we can redirect them accordingly later
		$return = $this->input->get('return', '', 'default');

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $return);
		$output = $theme->output('site/entry/dialogs/unpublish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders dialog confirmation to favourites a post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function confirmFavourite()
	{
		$id = $this->input->get('id', 0, 'int');

		$post = EB::post($id);

		if (!$id || !$post->id) {
			// return EB::exception('COM_EASYBLOG_INVALID_ID_PROVIDED');
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		if (!$post->canFavourite() || $post->isFavourited()) {
			return $this->ajax->reject('You already favourite this post');
		}

		$return = $this->input->get('return', '', 'default');

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $return);

		$output = $theme->output('site/entry/dialogs/favourite');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders dialog confirmation to unfavourites a post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function confirmUnfavourite()
	{
		$id = $this->input->get('id', 0, 'int');

		$post = EB::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'));
		}

		if (!$post->canFavourite() || !$post->isFavourited()) {
			return $this->ajax->reject('You did not favourite this post before');
		}

		$return = $this->input->get('return', '', 'default');

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $return);

		$output = $theme->output('site/entry/dialogs/unfavourite');

		return $this->ajax->resolve($output);
	}
}

