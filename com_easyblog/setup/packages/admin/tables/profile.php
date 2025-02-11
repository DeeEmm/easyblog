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

class EasyBlogTableProfile extends EasyBlogTable
{
	public $id = '';
	public $title = '';
	public $nickname = '';
	public $avatar = null;
	public $description = null;
	public $biography = '';
	public $url = '';
	public $params = null;
	public $user = null;
	public $permalink = '';
	public $custom_css = null;
	public $ordering = null;

	static $oauthClients = array();

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_users', 'id', $db);
	}

	/**
	 * Binds an array of data with the current profile object.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function bind($data, $ignore = array())
	{
		// Load the parent bind method
		$state = parent::bind($data, $ignore);
		// Normalize the url
		$this->url = EB::string()->normalizeUrl($this->url);

		// If user's permalink is empty, we need to generate one for them.
		if (empty($this->permalink)) {
			$user = JFactory::getUser($this->id);
			$this->permalink = $user->username;
		}

		// if the user doesn't have the ordering, we will store the next ordering value for this user
		if (empty($this->ordering)) {
			$userModel = EB::model('Users');
			$lastOrdering = $userModel->authorLastOrdering();

			$this->ordering = $lastOrdering + 1;
		}

		// Ensure that the permalink is valid
		// $this->permalink = JFilterOutput::stringURLSafe($this->permalink);
		$this->permalink = EBR::normalizePermalink($this->permalink);

		return $state;
	}

	/**
	 * Override the parents implementation of storing a profile
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function store($updateNulls = false)
	{
		$isNew = empty($this->id) ? true : false;

		$invalidKeys = array('twitter', 'blogCount', 'messaging', 'featured', 'isBloggerSubscribed');

		// before we store, we need to unset unnessary attribute.
		foreach ($invalidKeys as $iKey) {
			if (isset($this->$iKey)) {
				unset($this->$iKey);
			}
		}

		$state = parent::store($updateNulls);
		$my = JFactory::getUser();

		// If the user is updating their own profile
		if ($my->id == $this->id) {
			JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

			// @rule: Integrations with EasyDiscuss
			EB::easydiscuss()->log('easyblog.update.profile', $this->id, JText::_('COM_EASYBLOG_EASYDISCUSS_HISTORY_UPDATE_PROFILE'));
			EB::easydiscuss()->addPoint('easyblog.update.profile', $this->id);
			EB::easydiscuss()->addBadge('easyblog.update.profile', $this->id);
		}

		return $state;
	}

	/**
	 * Determines if the user has permissions to create posts on the site
	 *
	 * @since	5.4.5
	 * @access	public
	 */
	public function canCompose()
	{
		static $data = array();

		if (!isset($data[$this->id])) {
			$acl = $this->getAcl();

			$data[$this->id] = (bool) $acl->get('add_entry');
		}

		return $data[$this->id];
	}

	/**
	 * Creates a new record for the user
	 *
	 * @since	5.2.10
	 * @access	public
	 */
	public function createDefault($id)
	{
		$db = EB::db();
		$user = JFactory::getUser($id);

		$obj = new stdClass();
		$obj->id = $user->id;
		$obj->nickname = $user->name;
		$obj->avatar = 'default_blogger.png';
		$obj->description = '';
		$obj->url = '';
		$obj->params = '';
		$obj->title = '';
		$obj->biography = '';

		// Update user ordering
		$userModel = EB::model('Users');
		$lastOrdering = $userModel->authorLastOrdering();

		$obj->ordering = $lastOrdering + 1;

		// Check if username is the same as email.
		// If yes, we need to fallback to name instead due to privacy concern. #1720
		if ($user->name && $user->email && $user->username == $user->email) {
			$permalink = $user->name;
		} else {
			// Default to username for blogger permalink
			$permalink = $user->username;
		}

		$obj->permalink = EBR::normalizePermalink($permalink);

		// We do not insert 0 id into users table.
		if ($user->id) {
			$db->insertObject('#__easyblog_users', $obj);
		}

		return $obj;
	}

	/**
	 * Loads the blogger record
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function load($id = null, $reset = true)
	{
		static $users = null;

		$id = ( $id == '0' ) ? null : $id;

		if (is_null($id)) {
			$this->bind(JFactory::getUser(0));
			return $this;
		}

		if (empty($id)) {
			// When the id is null or 0
			$this->bind( JFactory::getUser() );
			return $this;
		}

		if (!isset($users[$id])) {

			if ((! parent::load($id)) && ($id != 0)) {
				$obj	= $this->createDefault($id);
				$this->bind($obj);
			}

			$users[$id] = clone $this;
		}

		$this->user	= JFactory::getUser($id);
		$this->bind($users[$id]);

		return $users[$id];
	}

	public function setUser($my)
	{
		$this->load($my->id);
		$this->user = $my;
	}

	/**
	 * Determines if the author is associated with any teams or not
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasTeams()
	{
		static $teams = array();

		if (!isset($teams[$this->id])) {
			$model = EB::model('TeamBlogs');

			$teams[$this->id] = $model->getUserTeams();
		}

		return count($teams[$this->id]) >= 1;
	}

	public function hasAssociations()
	{
		static $associations = array();

		if (!isset($associations[$this->id])) {

			$associations[$this->id] = false;

			// EasySocial groups
			$groups = EB::easysocial()->getGroups();
			$events = EB::easysocial()->getEvents();
			$pages = EB::easysocial()->getPages();

			// List groups the user joined on the site
			$groups = array_merge($groups, EB::jomsocial()->getGroups());
			$events = array_merge($events, EB::jomsocial()->getEvents());

			if ($groups || $events || $pages) {
				$associations[$this->id] = true;
			}
		}

		return $associations[$this->id];
	}

	/**
	 * Retrieves the blogger's name
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getName()
	{
		// Load front end language file
		EB::loadLanguages();

		if($this->id == 0) {
			return JText::_('COM_EASYBLOG_GUEST');
		}

		if (!$this->user) {
			$this->user = JFactory::getUser($this->id);
		}

		$config = EB::config();
		$type = $config->get('layout_nameformat');

		// Default to the person's name
		$name = $this->user->name;

		if ($type == 'username') {
			$name = $this->user->username;
		}

		if ($type == 'nickname' && !empty($this->nickname)) {
			$name = $this->nickname;
		}

		// PHP 8.1 compatibility
		$name = is_null($name) ? '' : $name;

		// Ensure that the name cannot be exploited.
		$name = EB::string()->escape($name);

		return $name;
	}

	/**
	 * Sets an author as featured author
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setFeatured()
	{
		$model = EB::model('Featured');

		$state = $model->makeFeatured(EBLOG_FEATURED_BLOGGER, $this->id);

		return $state;
	}

	/**
	 * Remove a featured status for author on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function removeFeatured()
	{
		$model = EB::model('Featured');
		$state = $model->removeFeatured(EBLOG_FEATURED_BLOGGER, $this->id);

		return $state;
	}

	/**
	 * Retrieves author's acl
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public function getAcl()
	{
		$acl = EB::acl($this->id);

		return $acl;
	}

	/**
	 * Retrieves the user's avatar
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public function getAvatar($fromOpengraph = false)
	{
		$avatar = EB::avatar()->getAvatarURL($this, $fromOpengraph);

		return $avatar;
	}

	/**
	 * Retrieve the editor to use for this user
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getEditor()
	{
		$config = EB::config();
		$defaultEditor = $config->get('layout_editor');
		$defaultEditorEnabled = JPluginHelper::isEnabled('editors', $defaultEditor);

		$params = $this->getParam();
		$userEditor = $params->get('user_editor');
		// Check if the editor plugin is enabled
		$userEditorEnabled = JPluginHelper::isEnabled('editors', $userEditor);

		// Check if the author have permission to use their own editor
		$allow = $this->getAcl()->get('allow_user_editor');

		// check user's editor
		if ($allow && $userEditorEnabled && !empty($userEditor)) {
			$editor = $userEditor;
		} else {
			$editor = 'composer';

			if ($defaultEditorEnabled) {
				$editor = $defaultEditor;
			}
		}

		return $editor;
	}

	/**
	 * Retrieves the description of the blogger
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getDescription($raw = false)
	{
		$description = $raw ? $this->description : nl2br($this->description);
		return $description;
	}

	/**
	 * Retrieves author's twitter link
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getTwitterLink()
	{
		static $links = array();

		if (!isset($links[$this->id])) {

			$links[$this->id] = '';

			$oauth = EB::table('OAuth');
			$oauth->loadByUser($this->id, 'twitter');

			$params = EB::registry($oauth->params);
			$screenName = $params->get('screen_name');

			if ($screenName) {
				$links[$this->id] = 'https://twitter.com/' . $screenName;
			}
		}

		return $links[$this->id];
	}

	/**
	 * Determines if the blogger is featured on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isFeatured()
	{
		$model = EB::model('Featured');

		return $model->isFeatured('blogger', $this->id);
	}

	/**
	 * Retrieves the total number of posts created by the author
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalPosts()
	{
		static $total = array();

		if (!isset($total[$this->id])) {
			$model = EB::model('Blogger');

			$total[$this->id] = $model->getTotalBlogCreated($this->id);
		}

		return $total[$this->id];
	}

	/**
	 * Retrieves the biography from the specific blogger
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getBiography($raw = false, $placeholder = false)
	{
		static $items = array();

		$key = (int) $raw  . (int) $placeholder . $this->id;

		if (!isset($items[$key])) {

			EB::loadLanguages();

			$biography = $this->biography;

			// We should only add newlines if the editor is wysiwyg editor
			$editorName = EB::getEditor(true);

			if (!$editorName) {
				$editorName = 'none';
			}

			if ($editorName == 'none' && !$raw) {
				$biography = nl2br($biography);
			}

			if (!$biography && $placeholder) {
				$biography = JText::sprintf('COM_EASYBLOG_BIOGRAPHY_NOT_SET', $this->getName());
			}

			$items[$key] = $biography;
		}

		return $items[$key];
	}

	/**
	 * Retrieves the website for the author
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getWebsite()
	{
		$url = $this->url == 'http://' ? '' : $this->url;

		return $url;
	}

	/**
	 * Generates the profile link for an author
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getProfileLink()
	{
		$profile = EB::profile($this);

		return $profile->getLink();
	}

	/**
	 * Retrieves the alias for this author
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getAlias()
	{
		static $permalinks = array();

		if (!isset($permalinks[$this->id])) {
			$config = EB::config();

			if (!$this->user && $this->id) {
				$this->user = JFactory::getuser($this->id);
			}

			// If the username is invalid
			if (!isset($this->user->username) || !$this->user->username) {
				return JText::_('COM_EASYBLOG_INVALID_PERMALINK_BLOGGER');
			}

			// If user doesn't have a permalink, generate it for them
			if (!$this->permalink) {
				$this->permalink = EBR::normalizePermalink($this->user->username);

				// we canot do store here or else any 'extra' attribute that get set before this function will be wide put.!
				// $this->store();
				$this->savePermalink($this->permalink);
			}

			$permalink = $this->permalink;

			if (EBR::isIDRequired()) {
				$permalink = $this->id . '-' . $this->permalink;
			}

			$permalinks[$this->id] = $permalink;
		}

		return $permalinks[$this->id];
	}

	private function savePermalink($permalink)
	{
		$db = EB::db();

		$query = "update `#__easyblog_users` set `permalink` = " . $db->Quote($permalink);
		$query .= " where id = " . $db->Quote($this->id);

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Retrieves the external permalink for this author
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getExternalPermalink()
	{
		$link = EBR::getRoutedURL('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $this->id, false, true, true);

		return $link;
	}

	/**
	 * Retrieves the permalink for this author
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPermalink($xhtml = true)
	{
		$url = EB::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $this->id, $xhtml);

		return $url;
	}

	/**
	 * Use @getParams instead
	 *
	 * @deprecated	5.3.0
	 */
	public function getParam()
	{
		return $this->getParams();
	}

	/**
	 * Retrieves the user type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getUserType()
	{
		return $this->user->usertype;
	}

	/**
	 * Retrieves rss link for the blogger
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRssLink()
	{
		$config = EB::config();

		static $_cache = array();

		$key = $this->id;

		if (!isset($_cache[$key])) {

			if ($config->get('main_feedburnerblogger')) {

				$feedburner	= EB::table('Feedburner');
				$feedburner->load($this->id);

				if (!empty($feedburner->url)) {
					$_cache[$key] = $feedburner->url;

					return $_cache[$key];
				}
			}

			$_cache[$key] = EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=blogger&id=' . $this->id, false, 'author');
		}

		return $_cache[$key];
	}

	/**
	 * Retrieves bloggers rss link
	 *
	 * @deprecated	4.0
	 * @access	public
	 */
	public function getRSS()
	{
		return $this->getRssLink();
	}

	public function getAtom()
	{
		return EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=blogger&id=' . $this->id, true);
	}

	/**
	 * Binds avatar
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function bindAvatar($file, $acl)
	{
		if (!$acl->get('upload_avatar')) {
			return false;
		}

		// Try to upload the avatar
		$avatar = EB::avatar();

		// Get the avatar path
		$this->avatar = $avatar->upload($file, $this->user->id);

		// Assign point for altauserpoints
		EB::altauserpoints()->assign('plgaup_easyblog_upload_avatar', $this->user->id, JText::_('COM_EASYBLOG_AUP_UPLOADED_AVATAR'));
	}

	/**
	 * Binds users oauth settings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function bindOauth($post, $acl)
	{
		// Store twitter settings
		if ($acl->get('update_twitter') ) {
			$twitter = EB::table('Oauth');
			$twitter->loadByUser($this->user->id, EBLOG_OAUTH_TWITTER);

			// If there is no result means we need to insert a new row for it
			// Same goes to LinkedIn and Facebook
			if (!$twitter->id) {
				$twitter->user_id = $this->user->id;
				$twitter->type = EBLOG_OAUTH_TWITTER;
			}

			$twitter->auto = isset($post['integrations_twitter_auto']) ? $post['integrations_twitter_auto'] : false;
			$twitter->message = isset($post['integrations_twitter_message']) ? $post['integrations_twitter_message'] : '';

			$twitter->store();
		}

		// Store linkedin settings
		if ($acl->get('update_linkedin')) {
			$linkedin = EB::table('Oauth');
			$linkedin->loadByUser($this->user->id, EBLOG_OAUTH_LINKEDIN);

			if (!$linkedin->id) {
				$linkedin->user_id = $this->user->id;
				$linkedin->type = EBLOG_OAUTH_LINKEDIN;
			}

			$linkedin->auto	= isset($post['integrations_linkedin_auto']) ? $post['integrations_linkedin_auto'] : false;
			$linkedin->message = isset($post['integrations_linkedin_message']) ? $post['integrations_linkedin_message'] : '';
			$linkedin->private = isset($post['integrations_linkedin_private']) ? $post['integrations_linkedin_private'] : true;

			$linkedin->store();
		}

		// Store fb settings
		if ($acl->get('update_facebook')) {
			$facebook = EB::table('Oauth');
			$facebook->loadByUser($this->user->id, EBLOG_OAUTH_FACEBOOK);

			if (!$facebook->id) {
				$facebook->user_id = $this->user->id;
				$facebook->type = EBLOG_OAUTH_FACEBOOK;
			}

			$facebook->auto	= isset($post['integrations_facebook_auto']) ? $post['integrations_facebook_auto'] : false;
			$facebook->message = '';
			$facebook->store();
		}
	}

	/**
	 * Binds feedburner settings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function bindFeedburner($post, $acl)
	{
		$config 	= EB::config();

		if (!$config->get('main_feedburner') || !$config->get('main_feedburnerblogger')) {
			return false;
		}

		if (!$acl->get('allow_feedburner')) {
			return false;
		}

		$feedburner	= EB::table('Feedburner');
		$feedburner->load($this->user->id);
		$feedburner->url	= $post['feedburner_url'];

		$state = $feedburner->store();

		return $state;
	}

	/**
	 * Binds users oauth settings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function bindAdsense($post, $acl)
	{
		$config 	= EB::config();

		if (!$config->get( 'integration_google_adsense_enable')) {
			return false;
		}

		if (!$acl->get('add_adsense')) {
			return false;
		}

		$adsense = EB::table('Adsense');
		$adsense->load($this->user->id);

		// Prevent Joomla from acting funny as on some site's it automatically adds the quote character at the end.
		$adsense->code 		= rtrim( $post['adsense_code'] , '"' );
		$adsense->display 	= $post['adsense_display'];
		$adsense->published = $post['adsense_published'];

		$state 	= $adsense->store();

		if (!$state) {
			$this->setError($adsense->getError());

			return false;
		}

		return true;
	}

	/**
	 * Retrieve a list of tags created by this user
	 **/
	public function getTags()
	{
		$db		= EB::db();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_tag' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'created_by' ) .'=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();
		$tags	= array();

		foreach( $rows as $row )
		{
			$tag	= EB::table('Tag');
			$tag->bind( $row );
			$tags[]	= $tag;
		}

		return $tags;
	}

	/**
	 * Retrieves a list of oauth clients
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getOauthClients()
	{
		// Load this only once.
		if (!self::$oauthClients) {
			$model = EB::model('Oauth');
			$result = $model->getUserClients($this->id);

			if ($result) {
				foreach ($result as $row) {

					$client = EB::table('Oauth');
					$client->bind($row);

					self::$oauthClients[$row->type] = $client;
				}
			}
		}

		return self::$oauthClients;
	}

	/**
	 * Determines if the user has oauth setup for specific site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasOauth($site)
	{
		$clients = $this->getOauthClients();

		if (!isset($clients[$site])) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the user has oauth setup for specific site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getOauth($site)
	{
		$clients = $this->getOauthClients();

		if (!isset($clients[$site])) {
			return false;
		}

		return $clients[$site];
	}

	/**
	 * Retrieves total number of comments the author made on the site.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCommentsCount()
	{
		if (!EB::comment()->isBuiltin()) {
			return 0;
		}

		$db = EB::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_comment' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'created_by' ) .'=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		return $db->loadResult();
	}

	/**
	 * Perform move up or down blogger ordering
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function move($direction, $where = '')
	{
		$db = EB::db();

		// moving up
		if ($direction == -1) {

			$query = 'UPDATE ' . $db->nameQuote('#__easyblog_users');
			$query .= ' SET ' . $db->nameQuote('ordering') . ' = ' . $db->nameQuote('ordering') . ' + 1';
			$query .= ' WHERE ' . $db->nameQuote('id') . ' = ' . $db->Quote($this->id);
			$db->setQuery($query);
			$db->query();

			return true;

		} else {

			$query = 'UPDATE ' . $db->nameQuote('#__easyblog_users');
			$query .= ' SET ' . $db->nameQuote('ordering') . ' = ' . $db->nameQuote('ordering') . ' - 1';
			$query .= ' WHERE ' . $db->nameQuote('id') . ' = ' . $db->Quote($this->id);
			$db->setQuery($query);
			$db->query();

			return true;
		}
	}

	/**
	 * Update the rest of the user ordering
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function updateOrdering()
	{
		$db = EB::db();

		$query = 'SELECT `id` FROM ' . $db->nameQuote('#__easyblog_users');
		$query .= ' ORDER BY ' . $db->nameQuote('id') . ' DESC';

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (count($rows) > 0) {

			$orderNum = '1';

			foreach ($rows as $row) {

				$query = 'UPDATE ' . $db->nameQuote('#__easyblog_users');
				$query .= ' SET ' . $db->nameQuote('ordering') . ' = ' . $db->Quote($orderNum);
				$query .= ' WHERE ' . $db->nameQuote('id') . ' = ' . $db->Quote($row->id);

				$db->setQuery($query);
				$db->query();

				$orderNum++;
			}
		}

		return true;
	}

	/**
	 * Retrieves the preferences of the composer panel of the user
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getPanelPreferences()
	{
		$params = $this->getParams();
		$preferences = new JRegistry($params->get('panelPreferences', ''));

		return $preferences;
	}

	/**
	 * Performs profile validation
	 *
	 * @since	5.4.9
	 * @access	public
	 */
	public function validateProfile($post, $avatar = null)
	{
		$valid = true;

		if (isset($post['fullname']) && EBString::strlen($post['fullname']) == 0) {
			$message = JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_REALNAME_EMPTY');

			$valid = false;
		}

		if (isset($avatar['tmp_name']) && !empty($avatar['tmp_name']) && !EB::image()->canUpload($avatar, $message)) {
			$valid = false;
		}

		if (!$valid) {
			EB::info()->set($message, 'error');
		}

		return $valid;
	}
}
