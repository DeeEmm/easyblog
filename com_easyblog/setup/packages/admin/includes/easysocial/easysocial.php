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

class EasyBlogEasySocial extends EasyBlog
{
	public static $file = null;

	public function __construct()
	{
		parent::__construct();

		self::$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';

		// Load languages
		$this->loadLanguage();
	}

	/**
	 * Determines if EasySocial is installed on the site.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function exists()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			jimport('joomla.filesystem.file');

			$exists = JFile::exists(self::$file);

			if ($exists) {
				include_once(self::$file);

				// If the ES class doesn't exist, easyblog shouldn't support it
				if (!class_exists('ES') || !JComponentHelper::isEnabled('com_easysocial')) {
					$exists = false;
				}
			}

			$loaded = $exists;
		}

		return $loaded;
	}

	/**
	 * Renders the messaging link
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getMessagingHtml($authorId)
	{
		if (!$this->exists()) {
			return;
		}

		$my = ES::user($this->my->id);
		$conversation = ES::conversation($this->my->id);

		// Initialize easysocial's library
		$this->init();

		$targetUser = ES::user($authorId);

		if (!$conversation->canCreate()){
			return;
		}

		if (!$my->canStartConversation($targetUser->id)) {
			return;
		}

		$legacy = $this->isLegacy();

		$template = EB::themes();
		$template->set('user', $targetUser);
		$template->set('legacy', $legacy);

		$output = $template->output('site/integrations/easysocial/conversation');

		return $output;
	}


	/**
	 * Renders the friend link
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getFriendsHtml($authorId)
	{
		if (!$this->exists()) {
			return;
		}

		$user = ES::user($authorId);

		// Check if the user is friends with the current viewer.
		if ($user->isFriends($this->my->id)) {
			return;
		}

		$this->init();
		$legacy = $this->isLegacy();

		$template = EB::themes();
		$template->set('user', $user);
		$template->set('id', $authorId);
		$template->set('legacy', $legacy);

		$output = $template->output('site/integrations/easysocial/friends');

		return $output;
	}

	/**
	 * Retrieves EasySocial's toolbar
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getToolbar()
	{
		if (!$this->exists()) {
			return;
		}

		$toolbar = ES::toolbar();
		$output = $toolbar->render();

		return $output;
	}

	/**
	 * Renders the mini header of EasySocial
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function renderMiniHeader($group)
	{
		if (!$this->exists()) {
			return;
		}

		// Initialize EasySocial's css files
		$this->init();

		$themes = ES::themes();

		$output = '';

		$extraClass = EB::responsive()->isMobile() ? ' is-mobile' : ' is-desktop';

		ob_start();
		echo '<div id="es" class="es' . $extraClass . '">';
		echo $themes->html('html.miniheader', $group);
		echo '</div>';
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Initializes EasySocial
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function init()
	{
		static $loaded 	= false;

		if (!$loaded) {

			require_once(self::$file);

			$document = JFactory::getDocument();

			if ($document->getType() == 'html') {
				if (EB::easysocial()->isLegacy()) {
					// We also need to render the styling from EasySocial.
					$doc = ES::document();
					$doc->init();

					$page = ES::page();
					$page->processScripts();
				} else {
					ES::initialize();
				}
			}

			ES::language()->load('com_easysocial', JPATH_ROOT);

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Determines if this is EasySocial prior to 2.x
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function isLegacy()
	{
		if (!$this->exists()) {
			return;
		}

		// Get the current version.
		$local = ES::getLocalVersion();

		$legacy = version_compare($local, '2.0.0') == -1 ? true : false;

		return $legacy;
	}

	/**
	 * Retrieves a list of events joined by the user
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getEvents()
	{
		if (!$this->exists()) {
			return array();
		}

		$eventApp = ES::table('App');
		$eventApp->loadByElement('blog', 'event', 'apps');

		// If the event app is disabled, skip this.
		if (!$eventApp->state) {
			return array();
		}

		$options = array();

		if (!FH::isSiteAdmin()) {
			$options['uid'] = $this->my->id;
		}

		$model = ES::model('Events');
		$result = $model->getEvents($options);
		$events = array();

		if (!$result) {
			return $events;
		}

		foreach ($result as $event) {
			$obj = new stdClass();
			$obj->title = $event->getName();
			$obj->source_id = $event->id;
			$obj->source_type = EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT;
			$obj->type = 'event';
			$obj->avatar = $event->getAvatar();

			$groups[] = $obj;
		}

		return $groups;
	}

	/**
	 * Retrieves a list of groups joined by the user
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getGroups()
	{
		if (!$this->exists()) {
			return array();
		}

		$groupApp = ES::table('App');
		$groupApp->loadByElement('blog', 'group', 'apps');

		// If the group blog app is disabled, skip this.
		if (!$groupApp->state) {
			return array();
		}

		$model = ES::model('Groups');

		$options = array();

		if (!FH::isSiteAdmin()) {
			$options['uid'] = $this->my->id;
		}

		$result = $model->getGroups($options);
		$groups = array();

		if (!$result) {
			return $groups;
		}

		foreach ($result as $group) {
			$obj = new stdClass();
			$obj->title = $group->getName();
			$obj->source_id = $group->id;
			$obj->source_type = EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP;
			$obj->type = 'group';
			$obj->avatar = $group->getAvatar();

			$groups[] = $obj;
		}

		return $groups;
	}

	/**
	 * Retrieves a list of pages joined by the user
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getPages()
	{
		if (!$this->exists()) {
			return array();
		}

		$pageApp = ES::table('App');
		$pageApp->loadByElement('blog', 'page', 'apps');

		// If the page blog app is disabled, skip this.
		if (!$pageApp->state) {
			return array();
		}

		$model = ES::model('Pages');

		$options = array();

		if (!FH::isSiteAdmin()) {
			$options['uid'] = $this->my->id;
		}

		$result = $model->getPages($options);
		$pages = array();

		if (!$result) {
			return $pages;
		}

		foreach ($result as $page) {
			$obj = new stdClass();
			$obj->title = $page->getName();
			$obj->source_id = $page->id;
			$obj->source_type = EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE;
			$obj->type = 'page';
			$obj->avatar = $page->getAvatar();

			$pages[] = $obj;
		}

		return $pages;
	}

	/**
	 * Determine if easysocial's blog apps installed for particular group
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function isBlogAppInstalled($group)
	{
		if (!$this->exists()) {
			return array();
		}

		static $_cache = array();

		if (isset($_cache[$group])) {
			return $_cache[$group];
		}

		$_cache[$group] = true;

		$blogApp = ES::table('App');
		$blogApp->loadByElement('blog', $group, 'apps');

		if (!$blogApp->state) {
			$_cache[$group] = false;
		}


		return $_cache[$group];
	}

	/**
	 * Displays the user's points
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getPoints($id)
	{
		$config = EB::config();

		if (!$this->exists()) {
			return;
		}

		if (!$config->get('integrations_easysocial_points')) {
			return;
		}

		$theme = EB::themes();

		$user = ES::user($id);

		$theme->set('user', $user);
		$output = $theme->output('site/integrations/easysocial/points');

		return $output;
	}

	/**
	 * Retrieve total of parent comment count
	 *
	 * @since	6.0.10
	 * @access	public
	 */
	public function getParentCommentTotalCount($comment)
	{
		if (!$comment) {
			return 0;
		}

		$options = [
			'element' => $comment->getElement(),
			'uid' => $comment->uid,
			'parentid' => 0,
			'includeReplies' => 0,
			'force' => true
		];

		if ($comment->stream_id) {
			$options['stream_id'] = $comment->stream_id;
		}

		$model = ES::model('Comments');
		$total = $model->getCommentCount($options);

		return $total;
	}

	/**
	 * Displays comments
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getCommentHTML($blog)
	{
		if (!$this->exists()) {
			return;
		}

		ES::language()->load('com_easysocial', JPATH_ROOT);

		$url = $blog->getExternalPermalink();
		$comments = ES::comments($blog->id, 'blog', 'create', SOCIAL_APPS_GROUP_USER, array('url' => $url));

		// Get total of parent comment count
		$total = $this->getParentCommentTotalCount($comments);

		// If the viewer is guest and there are no comment made yet, do not render the comment tab.
		if (!$this->my->id && !$total) {
			return;
		}

		// Initialize EasySocial's library
		EB::easysocial()->init();

		// Do not get the total from the cache
		// since other place already get the total comment count which included the replies
		$options = [
			'force' => true
		];

		if (!$blog->allowComments()) {
			$options['hideForm'] = 1;
		}

		$legacy = $this->isLegacy();

		$theme = EB::themes();
		$theme->set('blog', $blog);
		$theme->set('comments', $comments);
		$theme->set('legacy', $legacy);
		$theme->set('options', $options);
		$output = $theme->output('site/integrations/easysocial/comments');

		return $output;
	}

	/**
	 * Returns the comment counter
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getCommentCount($blog)
	{
		if (!$this->exists()) {
			return;
		}

		ES::language()->load('com_easysocial', JPATH_ROOT);

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $blog->id);
		$comments = ES::comments($blog->id, 'blog', 'create', SOCIAL_APPS_GROUP_USER, $url);

		return $comments->getCount();
	}

	/**
	 * Assign badge
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function assignBadge($rule, $message, $creatorId = null)
	{
		if(!$this->exists()) {
			return false;
		}

		$creator = ES::user($creatorId);

		$badge = ES::badges();
		$state = $badge->log('com_easyblog', $rule, $creator->id, $message);

		return $state;
	}


	/**
	 * Assign points
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function assignPoints($rule, $creatorId = null)
	{
		if (!$this->exists()) {
			return false;
		}

		$creator = ES::user($creatorId);
		$points = ES::points();
		$state = $points->assign($rule, 'com_easyblog', $creator->id);

		return $state;
	}

	/**
	 * Creates a new stream for new blog post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function createBlogStream($blog, $isNew)
	{
		if (!$this->exists()) {
			return false;
		}

		$postLib = EB::post($blog->id);

		// we need to check if this blog creation stream already exists
		// from post creation via blog apps.
		if ($isNew) {
			$streams = $this->getStreamId($blog->id, 'blog', 'create');
			if ($streams) {
				return false;
			}
		}

		$stream = ES::stream();
		$template = $stream->getTemplate();

		// Format blog content
		$content = $blog->removeLoadmodulesTags($blog->content);

		// Get the stream template
		$template->setActor($blog->created_by, SOCIAL_TYPE_USER);
		$template->setContext($blog->id, 'blog');
		$template->setContent($content);

		// stream creation date should always follow the blog post creation date. #1401
		// Update : Date source is now configurable to further avoid confusion with the date in blog apps. #1619
		$postDateSource = $this->config->get('integrations_easysocial_stream_date_source');
		$streamDate = isset($blog->$postDateSource) ? $blog->$postDateSource : $blog->created;

		$template->setDate($streamDate);

		// Add hashtags when verb is 'create'
		if ($isNew) {
			$tags = $blog->getTags();

			$tagsArray = array();

			if (!empty($tags)) {
				foreach ($tags as $tag) {
					$obj = new stdClass();
					$obj->start = 0;
					$obj->length = 0;
					$obj->type = 'hashtag';
					$obj->value = $tag->title;

					$tagsArray[] = $obj;
				}
			}

			$template->setMentions($tagsArray);
		}

		$esClusterType = array('event', 'group', 'page');

		// Determines if this post was contributed in a cluster
		$contribution = $blog->getBlogContribution();

		if ($contribution) {
			if (in_array($contribution->type, $esClusterType)) {
				$template->setCluster($contribution->id, $contribution->type);
			} else {
				// teamblog, jomosical.group, jomsocial.event

				$obj = new stdClass();
				$obj->utype = $contribution->type;
				$obj->uid = $contribution->id;

				$template->setParams($obj);
			}
		}

		$template->setVerb('create');

		if (!$isNew) {

			$model = EB::model('Blog');
			$template->setVerb('update');
			$hasUpdatedAuthor = false;

			// Retrieve a list of stream id
			$streams = $this->getStreamId($blog->id, 'blog');

			// Determine if the original author already get updated
			if ($blog->original->created_by != $blog->created_by) {
				$hasUpdatedAuthor = true;
			}

			// Only process this if the update stream more than 0
			if (count($streams) > 0) {

				$streamIds = array();

				foreach ($streams as $streamItem) {
					$streamIds[] = $streamItem->uid;
				}

				// There is a case where user updated from no contribution, to contribution
				// We need to change the previous created stream to change the contribution #1328
				if ($contribution && in_array($contribution->type, $esClusterType)) {

					$model->updateStreamContribution($streamIds, $contribution, $blog, $hasUpdatedAuthor, EASYBLOG_STREAM_CONTEXT_TYPE);

				// Determine if the original author already get updated
				} elseif (!$contribution && $hasUpdatedAuthor) {

					$model->updateStreamData($streamIds, $blog, EASYBLOG_STREAM_CONTEXT_TYPE);
				}
			}
		}

		// By default the privacy is visible publicly
		$privacyVal = 0;

		// Determines if the blog post should be visible publicly
		if ($blog->access) {
			$privacyVal = $blog->access;
		}

		$template->setAccess('easyblog.blog.view', $privacyVal);

		return $stream->add($template);
	}

	/**
	 * Retrieves the stream id given the appropriate item contexts
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function getStreamId($contextId, $contextType, $verb = '')
	{
		$db = EB::db();

		$query = 'SELECT `uid` FROM `#__social_stream_item`';
		$query .= ' WHERE `context_id` = ' . $db->Quote($contextId);
		$query .= ' AND `context_type` = ' . $db->Quote($contextType);

		if ($verb) {
			$query .= ' AND `verb` = ' . $db->Quote($verb);
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Creates a new stream for new reaction
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createReactionStream($postId, $historyId)
	{
		if (!$this->exists()) {
			return false;
		}

		$postLib = EB::post($postId);
		$stream = ES::stream();
		$template = $stream->getTemplate();

		// Get the stream template
		$template->setActor($this->my->id, SOCIAL_TYPE_USER);
		$template->setContext($historyId, 'blog');
		$template->setAccess('easyblog.blog.view');

		$template->setVerb('add.reaction');

		return $stream->add($template);
	}

	/**
	 * Creates a new stream for new blog post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function createFeaturedBlogStream(EasyBlogPost $post)
	{
		// Check if easysocial exists on the site
		if (!$this->exists()) {
			return false;
		}

		$stream = ES::stream();
		$template = $stream->getTemplate();

		// Get the stream template
		$template->setActor($post->getAuthor()->id, SOCIAL_TYPE_USER);
		$template->setContext($post->id, 'blog');
		$template->setContent($post->getContent());
		$template->setTarget($post->getAuthor()->id);

		$template->setSiteWide();
		$template->setVerb('featured');

		// By default the privacy is visible publicly
		$privacyVal = 0;

		// Determines if the blog post should be visible publicly
		if ($post->access) {
			$privacyVal = $post->access;
		}

		$template->setAccess('easyblog.blog.view', $privacyVal);

		return $stream->add($template);
	}

	/**
	 * Notify site subscribers whenever a new blog post is created
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function notifySubscribers(EasyBlogPost $blog, $action, $comment = null)
	{
		if (!$this->exists()) {
			return false;
		}

		// We don't want to notify via e-mail
		$emailOptions = false;
		$recipients = array();
		$rule = '';

		// Get the permalink of the post
		$permalink = $blog->getPermalink(true, false, null, false);

		if ($comment) {

			$commentLink = $blog->getExternalBlogLink($comment->id);

			// Retrieving the parent comment.
			$parentComment = null;

			if ($comment->isReply()) {
				// $parentAuthor = $comment->getParentComment();
				$parentComment = $comment->getParentComment();
			}
		}

		// Get the blog image
		$image = $blog->getImage() ? $blog->getImage('frontpage') : '';

		// New post created on the site
		if ($action == 'new.post') {
			$rule = 'blog.create';
			$recipients = $blog->getRegisteredSubscribers('new', array($blog->created_by));
			$options = array(
				'uid' => $blog->id,
				'actor_id' => $blog->created_by,
				'title' => JText::sprintf('COM_EASYBLOG_EASYSOCIAL_NOTIFICATION_NEW_BLOG_POST', $blog->title),
				'type' => 'blog',
				'url' => $permalink,
				'image' => $image
			);
		}

		// New comment posted on the site
		if ($action == 'new.comment') {
			if (!$this->config->get('integrations_easysocial_notifications_newcomment')) {
				return;
			}

			$rule = 'blog.comment';

			$exclude = array($comment->created_by);

			if ($comment->isReply()) {
				$exclude = array($comment->created_by, $parentComment->created_by);
			}

			// Get a list of recipients that we should notify
			$recipients = $comment->getSubscribers($blog, $exclude);

			// Exclude blog author if the current comment is replying to his.
			if (!$comment->isReply()) {
				// If this a new comment on your blogpost that is not you.
				if ($blog->created_by != $comment->created_by) {
					$recipients = array_merge($recipients, array($blog->created_by));
				}
			}
			else if ($comment->isReply()) {
				// Blog author should not receive new comment notification if he is replying a comment.
				if ($blog->created_by != $comment->created_by) {
					// Blog author should not receive new comment notification if he is replying his own comment. Let comment reply notification handle this.
					if ($comment->created_by != $parentComment->created_by && $blog->created_by != $parentComment->created_by) {
						$recipients = array_merge($recipients, array($blog->created_by));
					}
					// Blog author should receive new comment notification if somebody replying a comment on the site.
					else if ($comment->created_by == $parentComment->created_by && $blog->created_by != $comment->created_by) {
						$recipients = array_merge($recipients, array($blog->created_by));
					}
				}
			}

			// Format the comment's content
			$content = $comment->getContent(true);

			$options = array(
				'uid' => $blog->id,
				'actor_id' => $comment->created_by,
				'type' => 'blog',
				'content' => $content,
				'url' => $commentLink,
				'image' => $image
			);
		}

		if ($action == 'comment.reply' && $comment->isReply()) {

			if (!$this->config->get('integrations_easysocial_notifications_commentreply')) {
				return;
			}

			$rule = 'blog.comment.reply';

			if ($comment->created_by != $parentComment->created_by) {
				$recipients = array($parentComment->created_by);
			}

			// Format the comment's content
			$content = $comment->getContent(true);

			$options = array(
				'uid' => $blog->id,
				'actor_id' => $comment->created_by,
				'type' => 'blog',
				'content' => $content,
				'url' => $commentLink,
				'image' => $image
			);
		}

		// New ratings added on the post
		if ($action == 'ratings.add' && $this->config->get('integrations_easysocial_notifications_ratings')) {

			$rule = 'blog.ratings';

			// @TODO: Perhaps notify everyone else that subscribed to this post?
			// Notify the blog author
			$recipients = array($blog->created_by);

			$options = array(
				'uid' => $blog->id,
				'actor_id' => $this->my->id,
				'title' => JText::sprintf('COM_EASYBLOG_EASYSOCIAL_NOTIFICATION_NEW_RATINGS_FOR_YOUR_BLOG_POST', $blog->title),
				'type' => 'blog',
				'url' => $permalink,
				'image' => $image
			);
		}

		// New reaction given to the blog post
		if ($action == 'reaction.add' && $this->config->get('integrations_easysocial_notifications_reaction')) {
			$rule = 'blog.reaction';

			// Notify the blof author
			$recipients = array($blog->created_by);

			$options = array(
				'uid' => $blog->id,
				'actor_id' => $this->my->id,
				'title' => JText::sprintf('COM_EB_EASYSOCIAL_NOTIFICATION_NEW_REACTION_FOR_YOUR_BLOG_POST', $blog->title),
				'type' => 'blog',
				'url' => $permalink,
				'image' => $image
			);
		}

		if (!$rule) {
			return false;
		}

		// Send notifications to the receivers when they unlock the badge
		ES::notify($rule, $recipients, $emailOptions, $options);
	}


	/**
	 * Creates a new stream for new comments in EasyBlog
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function createCommentStream($comment, $blog)
	{
		if (!$this->exists()) {
			return false;
		}

		$stream = ES::stream();
		$template = $stream->getTemplate();

		// Get the stream template
		$template->setActor($comment->created_by, SOCIAL_TYPE_USER);
		$template->setContext($comment->id, 'blog');
		$template->setContent($comment->comment);

		$esClusterType = array('event', 'group', 'page');

		// Determines if this post was contributed in a cluster
		$contribution = $blog->getBlogContribution();

		if ($contribution) {
			if (in_array($contribution->type, $esClusterType)) {
				$template->setCluster($contribution->id, $contribution->type);
			}
		}

		$template->setVerb('create.comment');
		$template->setAccess('easyblog.blog.view');
		$state 	= $stream->add($template);

		return $state;
	}

	/**
	 * Creates a new stream for new comments in EasyBlog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function addIndexerNewBlog(EasyBlogPost $blog)
	{
		if (!$this->exists() || !$this->config->get('integrations_easysocial_indexer_newpost')) {
			return false;
		}

		$indexer = ES::get('Indexer', 'com_easyblog');
		$template = $indexer->getTemplate();

		// getting the blog content
		$content = $blog->intro . $blog->content;
		$image = '';

		// @rule: Try to get the blog image.
		if ($blog->getImage()) {
			$image = $blog->getImage('thumbnail');
		}

		if (empty($image)) {
			// @rule: Match images from blog post
			$pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
			preg_match($pattern, $content, $matches);

			if ($matches) {
				$image = isset($matches[1]) ? $matches[1] : '';

				if (EBString::stristr($matches[1], 'https://') === false && EBString::stristr($matches[1], 'http://') === false && !empty($image)) {
					$image = rtrim(JURI::root(), '/') . '/' . ltrim($image, '/');
				}
			}
		}

		if (!$image) {
			$image = rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/images/default_facebook.png';
		}

		// Cleanup content
		$content = $this->removeEmbedTags($content);
		$content = strip_tags($content);

		$length = EBString::strlen($content);
		$maxLength = $this->config->get('integrations_easysocial_indexer_newpost_length', 250);

		if ($length > $maxLength) {
			$content = EBString::substr($content, 0, $maxLength);
		}

		// lets include the title as the search snapshot.
		$content = $blog->title . ' ' . $content;
		$template->setContent($blog->title, $content);

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id='.$blog->id);

		// Remove /administrator/ from the url.
		$url = EBString::str_ireplace( 'administrator/' , '' , $url );

		$template->setSource($blog->id, 'blog', $blog->created_by, $url);
		$template->setThumbnail($image);
		$template->setLastUpdate($blog->modified);

		$state = $indexer->index($template);
		return $state;
	}

	/**
	 * Cleanup given content by removing embed tags
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeEmbedTags($content)
	{
		// @task: Strip out video tags
		$content = EB::videos()->strip($content);

		// @task: Strip out audio tags
		$content = EB::audio()->strip($content);

		// @task: Strip out gallery tags
		$content = EB::gallery()->strip($content);

		// @task: Strip out album tags
		$content = EB::album()->strip($content);

		// @rule: Once the gallery is already processed above, we will need to strip out the gallery contents since it may contain some unwanted codes
		// @2.0: <input class="easyblog-gallery"
		// @3.5: {ebgallery:'name'}
		$content = EB::gallery()->removeGalleryCodes($content);

		return $content;
	}


	/**
	 * Removes a stream from EasySocial
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function removePostStream(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return false;
		}

		return ES::stream()->delete($post->id, 'blog');
	}

	/**
	 * Removes comments related streams from EasySocial
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function removePostCommentStream(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return false;
		}

		$db = EB::db();

		$query = "delete b, c, d, e";
		$query .= " from `#__easyblog_comment` as a";
		$query .= " inner join `#__social_stream_item` as b on a.`id` = b.`context_id`";
		$query .= " inner join `#__social_stream` as c on b.`uid` = c.`id`";
		$query .= " left join `#__social_comments` as d on c.`id` = d.`stream_id`";
		$query .= " left join `#__social_likes` as e on c.`id` = e.`stream_id`";
		$query .= " where a.`post_id` = " . $db->Quote($post->id);
		$query .= " and b.`context_type` = " . $db->Quote('blog');
		$query .= " and b.`verb` = " . $db->Quote('create.comment');

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}

	public function updateBlogPrivacy($blog)
	{
		if (!$this->exists()) {
			return false;
		}

		$privacyLib = ES::privacy($blog->created_by, SOCIAL_PRIVACY_TYPE_USER);
		$privacyLib->add('easyblog.blog.view', $blog->id, 'blog', $blog->access);
	}

	/**
	 * Builds the sql query for privacy
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function buildPrivacyQuery($alias = 'a', $includeAnd = true)
	{
		if (!$this->exists() || !$this->config->get('integrations_es_privacy')) {
			return;
		}

		$db = EB::db();
		$my = JFactory::getUser();
		$config = EB::config();

		$my = JFactory::getUser();
		$esFriends = ES::model('Friends');

		$friends = $esFriends->getFriends($my->id, array('idonly' => true));

		if ($friends) {
			array_push($friends, $my->id);
		}

		// Set the alias for this query
		$alias = $alias . '.';

		// Determines if we should prepend the and in front of the query
		$queryWhere = '(';

		if ($includeAnd) {
			$queryWhere = ' AND (';
		}

		$queryWhere .= ' (' . $alias . '`access`= 0) OR';

		// we also need to check access = 1 #1197
		$queryWhere .= ' ((' . $alias . '`access` = 1) AND (' . $db->Quote($my->id) . ' > 0)) OR';

		$queryWhere .= ' ((' . $alias . '`access` = 10) AND (' . $db->Quote($my->id) . ' > 0)) OR';

		if (!$friends) {
			$queryWhere	.= ' ((' . $alias . '`access` = 30) AND (1 = 2)) OR';
		} else {
			$queryWhere	.= ' ((' . $alias . '`access` = 30) AND (' . $alias . $db->qn('created_by') . ' IN (' . implode(',', $friends) . '))) OR';
		}

		$queryWhere .= ' ((' . $alias . '`access` = 40) AND ('. $alias . $db->qn('created_by') .'=' . $my->id . ')) OR';

		// my own blog post
		$queryWhere .= ' ('. $alias . $db->qn('created_by') .'=' . $my->id . ')';

		$queryWhere .= ')';

		return $queryWhere;
	}

	public function truncateContent($content, $appParams)
	{
		// Get the app params
		static $maxLength = null;

		if (is_null($maxLength)) {
			$maxLength = $appParams->get('maxlength', 0);
		}

		if ($maxLength) {

			$truncateType = $appParams->get('truncation', 'chars');

			// Remove uneccessary html tags to avoid unclosed html tags
			$content = EBString::str_ireplace('&nbsp;', '', $content);
			$content = strip_tags($content);

			// Remove blank spaces since the word calculation should not include new lines or blanks.
			$content = trim($content);

			// @task: Let's truncate the content now.
			switch($truncateType) {
				case 'words':

					$tag = false;
					$count = 0;
					$output = '';

					$chunks = preg_split("/([\s]+)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

					foreach($chunks as $piece) {

						if (!$tag || stripos($piece, '>') !== false) {
							$tag = (bool) (strripos($piece, '>') < strripos($piece, '<'));
						}

						if (!$tag && trim($piece) == '') {
							$count++;
						}

						if ($count > $maxLength && !$tag) {
							break;
						}

						$output .= $piece;
					}

					unset($chunks);
					$content = $output;

					break;
				case 'chars':
				default:
					$content = EBString::substr($content, 0, $maxLength);
					break;
			}
		}

		return $content;
	}

	private function prepareContent(&$content)
	{
		// See if there's any audio files to process.
		$audios = EB::audio()->getItems($content);

		// Get videos attached in the content
		$videos = $this->getVideos($content);
		$video = false;

		if (isset($videos[0])) {
			$video = $videos[0];
		}

		// Remove videos from the source
		$content = EB::videos()->strip($content);

		// Remove audios from the content
		$content = EB::audio()->strip($content);

		$this->set('video', $video);
		$this->set('audios', $audios);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('blog', $blog);
		$this->set('actor', $item->actor);
		$this->set('content', $content);

		$catUrl = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $blog->category_id, true, null, false, true);
		$this->set('categorypermalink', $catUrl);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->content = parent::display('streams/' . $item->verb . '.content');
	}

	private function getVideo($content)
	{
		$videos = EB::videos()->getVideoObjects($content);

		if (isset($videos[0])) {
			return $videos[0];
		}

		return false;
	}

	/**
	 * Retrieves a list of badges from the user
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getToolbarBadges($userId = null)
	{
		$user = $this->user($userId);

		$badges = $user->getBadges();

		if (!$badges) {
			return;
		}

		$theme = EB::themes();
		$theme->set('badges', $badges);

		$output = $theme->output('site/integrations/easysocial/toolbar.badges');

		return $output;
	}

	/**
	 * Retrieves the cover for the user
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getCover($id = null)
	{
		if (!$this->exists()) {
			return;
		}

		$user = $this->user($id);
		$cover = $user->getCoverData();

		return $cover;
	}

	/**
	 * Renders the toolbar dropdown html
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getToolbarDropdown()
	{
		$theme = EB::themes();
		$theme->set('esConfig', ES::config());

		$namespace = 'site/integrations/easysocial/toolbar';

		if ($this->isMobile()) {
			$namespace .= '.mobile';
		}

		$output = $theme->output($namespace);

		return $output;
	}

	/**
	 * SocialUser layer
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function user($id = null)
	{
		if (!$this->exists()) {
			return;
		}

		return ES::user($id);
	}

	/**
	 * Generate subscribers access query for blog privacy
	 *
	 * @since   5.2.6
	 * @access  public
	 */
	public function getSubscriberAccessQuery($value, $column, $authorId)
	{
		$accessQuery = '';

		if (!$this->exists()) {
			return $accessQuery;
		}

		$db = EB::db();

		// Registered user (10)
		if ($value == SOCIAL_PRIVACY_MEMBER) {
			$accessQuery = $db->qn($column) . ' != ' . $db->Quote('0');
		}

		// Friends only (30)
		if ($value == SOCIAL_PRIVACY_FRIEND) {

			// Get list of friends
			$friendsIds = $this->getFriendsIds($authorId);

			if ($friendsIds) {
				$friendsIds = implode(',', $friendsIds);
			} else {

				// User don't have any friend :( hence the trick is we assume the author as the friend
				$friendsIds = $authorId;
			}

			$accessQuery = $db->qn($column) . ' IN (' . $friendsIds . ')' ;
		}

		return $accessQuery;
	}

	/**
	 * Retrieve friend ids of specified user
	 *
	 * @since   5.2.6
	 * @access  public
	 */
	public function getFriendsIds($userId)
	{
		if (!$this->exists()) {
			return false;
		}

		$model = ES::model('Friends');
		$friends = $model->getFriends($userId, array('idonly' => true));

		return $friends;
	}
}
