<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogJomSocial extends EasyBlog
{
	private $exists	= false;

	public function __construct()
	{
		// Load language file
		EB::loadLanguages();

		$this->app = JFactory::getApplication();
		$this->exists = $this->exists();

		parent::__construct();
	}

	/**
	 * Renders the messaging link
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getMessagingHtml($authorId)
	{
		if (!$this->exists()) {
			return;
		}

		$file = JPATH_ROOT . '/components/com_community/libraries/messaging.php';

		if (!JFile::exists($file)) {
			return;
		}

		require_once($file);

		CMessaging::load();

		$template = EB::themes();
		$template->set('authorId', $authorId);
		$output = $template->output('site/integrations/jomsocial/messaging');

		return $output;
	}


	/**
	 * Renders the friend link
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getFriendsHtml($authorId)
	{
		if (!$this->exists()) {
			return;
		}

		require_once(JPATH_ROOT . '/components/com_community/libraries/friends.php');

		$friends = array();

		// Get the current logged in user
		$my = JFactory::getUser();

		$model = CFactory::getModel("Friends");
		$friends = $model->getFriendIds($authorId);

		// If they are already friends, no point displaying the friends link
		if (in_array($my->id, $friends)) {
			return;
		}

		$lib = new CFriends();

		$template = EB::themes();
		$template->set('lib', $lib);
		$template->set('authorId', $authorId);
		$output = $template->output('site/integrations/jomsocial/friends');

		return $output;
	}

	/**
	 * Displays the toolbar of JomSocial
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getToolbar()
	{
		// Get the current logged in user
		$my = JFactory::getUser();

		// Jomsocial toolbar only appear when you turn on the integration option and logged in user.
		if (!$this->config->get('integrations_jomsocial_toolbar') || !$my->id) {
			return;
		}

		// Allow third party to control the toolbar
		$displayToolbar = $this->input->get('showJomsocialToolbar', true);
		$format = $this->input->get('format', '', 'word');
		$tmpl = $this->input->get('tmpl', '', 'word');

		if ($format == 'pdf' || $format == 'phocapdf' || $tmpl == 'component' || !$displayToolbar) {
			return;
		}

		// Ensure that JomSocial exists
		if (!$this->exists()) {
			return;
		}


		// Ensure the library really exists on the site.
		if (!class_exists('CToolbarLibrary') || !method_exists('CToolbarLibrary', 'getInstance')) {
			return;
		}

		$svg = '';

		if (method_exists('CFactory', 'getPath')) {
			$svg = CFactory::getPath('template://assets/icon/joms-icon.svg');
		}

		// Load up the apps
		$appsLib = CAppPlugins::getInstance();
		$appsLib->loadApplications();
		$appsLib->triggerEvent('onSystemStart', array());

		// Get the toolbar library
		$toolbar = CToolbarLibrary::getInstance();

		$theme = EB::themes();
		$theme->set('svg', $svg);
		$theme->set('toolbar', $toolbar);
		$contents = $theme->output('site/toolbar/toolbar.jomsocial');

		return $contents;
	}

	/**
	 * Retrieves a list of groups the user has joined / created
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups()
	{
		if (!$this->exists()) {
			return array();
		}

		// Check if the group system plugin is enabled.
		if (!JPluginHelper::isEnabled('system', 'groupeasyblog')) {
			return array();
		}

		// Load up jomsocial's model
		$model = CFactory::getModel('Groups');

		if (FH::isSiteAdmin()) {
			$result = $model->getAllGroups();
		} else {
			$result = $model->getGroups($this->my->id, null, false);
		}

		$groups = array();

		if ($result) {
			foreach ($result as $row) {
				$group = JTable::getInstance('Group', 'CTable');
				$group->load($row->id);

				$obj = new stdClass();
				$obj->title = $group->name;
				$obj->source_id = $group->id;
				$obj->source_type = EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP;
				$obj->type = 'group';
				$obj->avatar = $group->getThumbAvatar();

				$groups[] = $obj;
			}
		}

		return $groups;
	}

	/**
	 * Retrieves a list of events from JomSocial
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEvents()
	{
		$result = array();

		if (!$this->exists()) {
			return $result;
		}

		// Check if the group system plugin is enabled.
		if (!JPluginHelper::isEnabled('system', 'eventeasyblog')) {
			return $result;
		}

		$model = CFactory::getModel('Events');
		$rows = $model->getEvents(null, $this->my->id, null, null, false, false, null, null, CEventHelper::ALL_TYPES, 0, 999999);

		if (!$rows) {
			return $result;
		}

		foreach ($rows as $row) {
			$event = JTable::getInstance('Event', 'CTable');
			$event->bind($row);

			$obj = new stdClass();
			$obj->title = $event->title;
			$obj->source_id = $event->id;
			$obj->source_type = EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT;
			$obj->type = 'event';
			$obj->avatar = $event->getAvatar();

			$result[] = $obj;
		}

		return $result;
	}

	/**
	 * Removes a stream from JomSocial
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removePostStream(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return false;
		}

		if (!$this->config->get('integrations_jomsocial_unpublish_remove_activity')) {
			return false;
		}

		CFactory::load('libraries', 'activities');
		CActivityStream::remove('easyblog', $post->id);
	}


	/**
	 * Removes a stream item
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeCommentStream($commentId)
	{
		// Test if jomsocial exists
		if (!$this->exists()) {
			return false;
		}

		// Test if new comment activity is added
		if ($this->config->get('integrations_jomsocial_comment_new_activity')) {

			$db = EB::db();
			$query	= 'SELECT COUNT(1) FROM ' . $db->quoteName('#__community_activities') . ' '
					. 'WHERE ' . $db->quoteName('app') . '=' . $db->Quote('com_easyblog') . ' '
					. 'AND ' . $db->quoteName('cid') . '=' . $db->Quote($commentId) . ' '
					. 'AND ' . $db->quoteName('comment_type') . '=' . $db->Quote('com_easyblog.comments');
			$db->setQuery($query);

			$exists	= $db->loadResult();

			if ($exists) {

				$query	= 'DELETE FROM ' . $db->quoteName('#__community_activities' ) . ' '
						. 'WHERE ' . $db->quoteName('app') . '=' . $db->Quote('com_easyblog' ) . ' '
						. 'AND ' . $db->quoteName('cid') . '=' . $db->Quote($commentId) . ' '
						. 'AND ' . $db->quoteName('comment_type') . '=' . $db->Quote('com_easyblog.comments');

				$db->setQuery($query);
				$db->Query();
			}
		}
	}

	/**
	 * Assign points for jomsocial
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function assignPoints($command, $userId = null)
	{
		if (!$this->exists()) {
			return false;
		}

		if (!$this->config->get('main_jomsocial_userpoint')) {
			return false;
		}

		$user = JFactory::getUser($userId);

		return CUserPoints::assignPoint($command, $user->id);
	}

	/**
	 * Determines if JomSocial exists on the current site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			jimport('joomla.filesystem.file');

			$file = JPATH_ROOT . '/components/com_community/libraries/core.php';
			$exists = JFile::exists($file);

			if ($exists) {
				include_once($file);
			}
		}

		return $exists;
	}

	/**
	 * Formats the title of the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostTitle(EasyBlogPost $post)
	{
		$title = htmlspecialchars($post->title);

		$max = $this->config->get('jomsocial_blog_title_length', 100);

		if (EBString::strlen($title) > $max) {
			$title = EBString::substr($post->title, 0, $max) . ' ' . JText::_('COM_EASYBLOG_ELLIPSES');
		}

		return $title;
	}

	/**
	 * Inserts activity stream for JomSocial
	 *
	 * @since	5.0
	 * @access	public
	 * @param	EasyBlogTableBlog
	 * @return
	 */
	public function insertActivity(EasyBlogPost $post, $command = '', $content = '')
	{
		// Determine which command to use for this stream
		if (empty($command)) {
			$command = $post->isNew() ? 'easyblog.blog.add' : 'easyblog.blog.update';
		}

		// Check if Jomsocial exists
		if (!$this->exists()) {
			return false;
		}

		// If this is a feed source and the settings doesn't allow this, skip this
		if ($command == 'easyblog.blog.add' && $post->isNew() && $post->isFromFeed() && !$this->config->get('integrations_jomsocial_rss_import_activity')) {
			return false;
		}

		// Ensure that the configuration allows user to publish new feed
		if ($command == 'easyblog.blog.add' && $post->isNew() && !$this->config->get('integrations_jomsocial_blog_new_activity')) {
			return false;
		}

		// Ensure that the configuration allows user to publish new feed when blog is updated
		if ($command == 'easyblog.blog.update' && !$post->isNew() && !$this->config->get('integrations_jomsocial_blog_update_activity')) {
			return false;
		}

		// Determine the post title
		$title = $this->getPostTitle($post);

		// Get the category the post is associated to
		$category = $post->getPrimaryCategory();
		$categoryLink = $category->getExternalPermalink();

		// Get the permalink of the blog post
		$permalink = $post->getExternalPermalink();

		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		if (EB::isFromAdmin() && EBR::isSh404Enabled()) {
			$itemId = EBR::getItemId('latest');
			$itemId = '&Itemid=' . $itemId;

			$permalink = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=entry&id=' . $post->id . $itemId;
			$categoryLink = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=categories&layout=listings&id=' . $category->id . $itemId;
		}

		// If there is no content provided, we assume that the user wants to use the blog post
		if (!$content) {
			$content = $this->prepareBlogContent($post, $permalink);
		}

		// Prepare the title of the post
		$streamTitle = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_ADDED_NON_CATEGORY', $permalink, $title);

		// If this is not a new post, we need to use a different title
		if (!$post->isNew() && $command == 'easyblog.blog.update') {
			$streamTitle = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_UPDATED_NON_CATEGORY', $permalink, $title);
		}

		// Determines which stream title to use
		if ($this->config->get('integrations_jomsocial_show_category') && $post->isNew() && $command == 'easyblog.blog.add') {
			$streamTitle = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_ADDED', $permalink, $title, $categoryLink, JText::_($category->title));
		}

		if (!$post->isNew() && $command == 'easyblog.blog.update') {
			$streamTitle = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_UPDATED', $permalink, $title, $categoryLink, JText::_($category->title));
		}

		// Featuring of a blog post
		if ($command == 'easyblog.blog.featured') {
			$streamTitle = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_BLOG_FEATURED', $permalink, $title);
		}

		// Insert the object into the stream now
		$obj = new stdClass();
		$obj->access = $post->access;
		$obj->title = $streamTitle;
		$obj->content = $content;
		$obj->cmd = $command;

		// Should we enable likes
		if ($this->config->get('integrations_jomsocial_activity_likes')) {
			$obj->like_id = $post->id;
			$obj->like_type = 'com_easyblog';
		}

		// Should we link the comments
		if ($this->config->get('integrations_jomsocial_activity_comments')) {
			$obj->comment_id = $post->id;
			$obj->comment_type = 'com_easyblog';
		}

		$obj->actor = $post->created_by;
		$obj->target = 0;
		$obj->app = 'easyblog';
		$obj->cid = $post->id;

		// If this post is contributed in an event or a group, update it accordingly.
		if ($post->getBlogContribution()) {

			// Get the event object
			// $contribution = $post->getBlogContribution();

			// Event type
			if ($post->source_type == EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT) {
				JTable::addIncludePath(JPATH_ROOT . '/components/com_community/tables');

				// Load the event
				$event = JTable::getInstance('Event', 'CTable');
				$event->load($post->source_id);

				// Set the stream title
				$eventLink = CRoute::_('index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id);
				$obj->title = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_EVENT_BLOG_ADDED', $permalink, $title, $eventLink, $event->title);

				// Set a new command
				$obj->cmd = 'event.blog.added';
				$obj->target = $event->id;
				$obj->cid = $event->id;
				$obj->eventid = $event->id;
			}

			// Group type
			if ($post->source_type == EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP) {

				JTable::addIncludePath(JPATH_ROOT . '/components/com_community/tables');

				// Load the group
				$group = JTable::getInstance('Group', 'CTable');
				$group->load($post->source_id);

				// Set the stream title
				$groupLink = CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id);
				$obj->title = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_GROUP_BLOG_ADDED', $permalink, $title, $groupLink, $group->name);

				$obj->group_access = $group->approvals;
				$obj->target = $group->id;
				$obj->cid = $group->id;
				$obj->groupid = $group->id;
				$obj->cmd = 'group.blog.added';
			}
		}


		// Ensure that the likes and comment uses the correct type
		if (!$post->isNew()) {
			$obj->like_type = 'com_easyblog.update';
			$obj->comment_type = 'com_easyblog.update';
		}


		// Insert into jomsocial now
		CFactory::load('libraries', 'activities');
		CActivityStream::add($obj);
	}

	/**
	 * Prepares a blog content before submitting to JomSocial's stream
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function prepareBlogContent(EasyBlogPost $post, $permalink)
	{
		$content = '';

		// If the stream is configured to not display any contents at all, skip this
		if (!$this->config->get('integrations_jomsocial_submit_content')) {
			return $content;
		}

		// Check if the post requires verification
		if ($this->config->get('main_password_protect', true) && !empty($blog->blogpassword)) {
			$template = EB::themes();
			$template->set('id', $blog->id);
			$template->set('return', base64_encode($blogLink));

			$content = $template->output('site/blogs/protected');

			return $content;
		}

		// Get the content
		$content = $post->getContent('entry', false, true);
		$image = '';

		// If there's no post image, search for the first image
		if (!$post->hasImage()) {

			// This will return a string of img tag if exist.
			$image = EB::string()->searchImage($content);

			if (!is_array($image)) {

				// We need to extract the src attribute
				preg_match('/src="([^"]*)"/i', $image, $matches);

				if ($matches) {
					$image = $matches[1];
				}
			}

		} else {
			$image = $post->getImage();
		}

		// Normalize the content of the post
		$content = $this->normalizeContent($content);

		$template = EB::themes();

		$template->set('permalink', $permalink);
		$template->set('image', $image);
		$template->set('content', $content);

		$output = $template->output('site/integrations/jomsocial/stream');

		return $output;

	}

	/**
	 * Normalize the content for stream display purposes
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeContent($content)
	{
		$content = EB::videos()->strip($content);
		$content = EB::adsense()->strip($content);
		$content = strip_tags($content);
		$content = EBString::substr($content, 0, $this->config->get('integrations_jomsocial_blogs_length', 250)) . ' ' . JText::_('COM_EASYBLOG_ELLIPSES');

		return $content;
	}

	/**
	 * Creates a new stream item on JomSocial when a post is featured
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createFeaturedBlogStream(EasyBlogPost $post)
	{
		// Check if jomsocial exists
		if (!$this->exists()) {
			return false;
		}

		// Check if we should integrate this
		if (!$this->config->get('integrations_jomsocial_feature_blog_activity')) {
			return false;
		}

		return $this->insertActivity($post, 'easyblog.blog.featured');
	}

	/**
	 *
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function insetCommentActivity($comment, EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return false;
		}

		// We do not want to add activities if new comment activity is disabled.
		if (!$this->config->get('integrations_jomsocial_comment_new_activity')) {
			return false;
		}

		$command = 'easyblog.comment.add';

		// Get the post title
		$title = $this->getPostTitle($post);

		// Get the permalink
		$permalink = EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id='. $comment->post_id, false, true) . '#comment-' . $comment->id;

		// Get the content
		$content = '';

		if ($this->config->get('integrations_jomsocial_submit_content')) {
			$content = $comment->comment;
			$content = EB::comment()->parseBBCode($content);
			$content = nl2br($content);
			$content = strip_tags($content);
			$content = EBString::substr($content, 0, $this->config->get('integrations_jomsocial_comments_length'));
		}

		$obj = new stdClass();
		$obj->title = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_COMMENT_ADDED', $permalink, $title);

		if (!$comment->created_by) {
			$obj->title = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_GUEST_COMMENT_ADDED', $permalink, $title);
		}

		$obj->content = $content;
		$obj->cmd = $command;
		$obj->actor = $comment->created_by;

		$obj->target = 0;
		$obj->app = 'easyblog';
		$obj->cid = $comment->id;

		if ($this->config->get('integrations_jomsocial_activity_likes')) {
			$obj->like_id = $comment->id;
			$obj->like_type = 'com_easyblog.comments';
		}

		if ($this->config->get('integrations_jomsocial_activity_comments')) {
			$obj->comment_id = $comment->id;
			$obj->comment_type = 'com_easyblog.comments';
		}

		// add JomSocial activities
		CFactory::load('libraries', 'activities');
		CActivityStream::add($obj);
	}

	/**
	 * Builds the privacy query when integrated with jomsocial
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function buildPrivacyQuery()
	{
		if (!$this->exists() || !$this->config->get('main_jomsocial_privacy')) {
			return;
		}

		$db = EB::db();
		$my	= JFactory::getUser();
		$jsFriends = CFactory::getModel( 'Friends' );
		$friends = $jsFriends->getFriendIds( $my->id );

		// Insert query here.
		$query	= ' AND (';
		$query	.= ' (a.`access`= 0 ) OR';
		$query	.= ' ( (a.`access` = 20) AND (' . $db->Quote($my->id) . ' > 0 ) ) OR';

		if (empty($friends)) {
			$query	.= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';

		} else {
			$query	.= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';

		}

		$query	.= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $my->id . ') )';
		$query	.= ' )';

		return $query;
	}
}
