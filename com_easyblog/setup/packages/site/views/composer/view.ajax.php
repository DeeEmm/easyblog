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

// As this file may be included from the back-end, we need to explicitly load the front end view
require_once(EB_ROOT . '/views/views.php');

class EasyBlogViewComposer extends EasyBlogView
{
	/**
	 * Allows caller to browse for a post for language association
	 *
	 * @since	5.1.8
	 * @access	public
	 */
	public function browsePosts()
	{
		// Ensure that the user is logged in.
		EB::requireLogin();

		$id = $this->input->get('id', 0, 'int');
		$code = $this->input->get('code', '', 'default');

		$browseUrl = rtrim(JURI::root(true), '/') . '/';

		if (EB::isFromAdmin()) {
			$browseUrl .= 'administrator/';
		}

		$browseUrl .= 'index.php?option=com_easyblog&view=composer&layout=getPosts&code=' . $code . '&codeid=' . $id . '&tmpl=component&browse=1';

		$theme = EB::themes();
		$theme->set('browseUrl', $browseUrl);

		$output = $theme->output('site/composer/panels/post/association/dialogs/browse');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the blog template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderTemplate()
	{
		$uid = $this->input->get('uid', 0, 'int');

		$postTemplate = EB::table('PostTemplate');
		$postTemplate->load($uid);

		if (!$postTemplate->data) {
			return $this->ajax->resolve();
		}

		// Directly return the data for legacy template
		if ($postTemplate->isLegacy()) {
			return $this->ajax->resolve('title', 'permalink', $postTemplate->data);
		}

		// Determine whether the selected post template is locked or not
		$isLocked = $postTemplate->isLocked();

		$document = $postTemplate->getDocument();
		$content = $document->getEditableContent($isLocked);

		return $this->ajax->resolve(JText::_($document->title), $postTemplate->canEdit(), $content, $isLocked);
	}

	/**
	 * Renders the module
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function checkModuleContent()
	{
		$module = $this->input->get('module', '', 'string');
		$isBackend = EB::isFromAdmin();

		$doc = JFactory::getDocument();

		$attributes = array();
		$attributes['style'] = 'xhtml';

		$module = JModuleHelper::getModule($module);
		$output = JModuleHelper::renderModule($module, $attributes);

		if (empty($output) && !$isBackend) {
			return $this->ajax->reject();
		}

		return $this->ajax->resolve();
	}

	/**
	 * Given a value, normalize the permalink and ensure that it's valid
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function normalizePermalink()
	{
		$original = $this->input->get('permalink', '', 'default');
		$postId = $this->input->get('postId', 0, 'int');

		$post = EB::post($postId);

		if (!$post->canCreate()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG'));
		}

		$model = EB::model('Blog');
		$permalink = $model->normalizePermalink($original, $post->id);

		$theme = EB::themes();
		$theme->set('permalink', $permalink);
		$permalinkHtml = $theme->output('site/composer/document/permalink');

		return $this->ajax->resolve($permalink, $permalinkHtml);
	}

	/**
	 * Displays confirmation to delete post in composer
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function confirmTrash()
	{
		// Get blog id
		$uid = $this->input->get('uid', '', 'int');

		$lib = EB::post($uid);

		$title = 'COM_EB_DIALOG_COMPOSER_DELETE_DRAFT';
		$content = 'COM_EB_DIALOG_COMPOSER_DELETE_DRAFT_CONFIRMATION';
		$operation = 'delete';

		if ($lib->isPostPublished()) {
			$title = 'COM_EB_COMPOSER_MOVE_TO_TRASH';
			$content = 'COM_EB_COMPOSER_MOVE_TO_TRASH_MESSAGE';
			$operation = 'trash';
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('content', $content);
		$theme->set('operation', $operation);
		$output = $theme->output('site/composer/dialogs/delete.post');

		return $this->ajax->resolve($output);
	}

	/**
	 * Deletes blog posts from composer view
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete()
	{
		EB::requireLogin();

		// Check for tokens
		FH::checkToken();

		// Get the list of blog id's
		$uid = $this->input->get('uid', '', 'int');

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		if (EB::isFromAdmin()) {
			$return = EB::_('index.php?option=com_easyblog&view=blogs', false);
		}

		$post = EB::post($uid);

		// if the draft do not have post we only need to delete draft
		if ($post->isDraft()) {

			$model = EB::model('Revisions');
			$model->deleteRevisions($uid);

			$this->info->set(JText::_('COM_EB_DRAFT_TRASH_SUCCESS'), 'success');

			if (EB::isFromAdmin()) {
				$return = EB::_('index.php?option=com_easyblog&view=blogs&layout=drafts', false);
			}
			return $this->ajax->redirect($return);
		}

		if (!$post->canDelete()) {
			$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_BLOG'), 'error');
			return $this->ajax->redirect($return);
		}

		$post->trash();

		$this->info->set(JText::_('COM_EB_COMPOSER_TRASH_SUCCESS'), 'success');

		return $this->ajax->redirect($return);
	}

	/**
	 * Retrieves suggestions for keywords based on the content
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function suggestKeywords()
	{
		EB::requireLogin();

		// Check if the author has access to write and publish post
		if (!$this->acl->get('add_entry')) {
			die();
		}

		// Skip this if the site do not have install CURL from PHP
		if (!function_exists('curl_init')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_CURL_DOES_NOT_EXIST'));
		}

		$content = $this->input->get('data', '', 'default');

		$url = EBLOG_KEYWORDS_SERVER;
		$apiKey = $this->config->get('main_apikey');

		$connector = FH::connector($url);
		$connector->addQuery('key', $apiKey);
		$connector->addQuery('text', $content);
		$connector->addQuery('domain', JURI::root());
		$connector->setMethod('POST');
		$result = $connector->execute()->getResult();
		$result = json_decode($result);

		if (!$result) {
			return $this->ajax->reject(JText::_('COM_EB_SUGGEST_TAG_AUTOFILL_GENERAL_MSG'));
		}

		if ((isset($result->code) && $result->code != 200) && (isset($result->error) && $result->error)) {
			return $this->ajax->reject($result->error);
		}

		$this->ajax->resolve($result->result);
	}

	/**
	 * Lists down recent articles created by the author
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function listArticles()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		$exclude = $this->input->get('exclude', '', 'default');

		$options = [
			'exclude' => $exclude,
			'limit' => 20,
			'sort' => 'latest'
		];

		$model = EB::model('Blog');
		$items = $model->getUserPosts($this->my->id, $options);

		if (!$items) {
			return $this->ajax->resolve($items);
		}

		//cache the posts
		EB::cache()->cachePosts($items);

		$posts = array();

		foreach ($items as $item) {

			$post = EB::post($item->id);

			// Set a formatted date
			$post->formattedDate = EB::date($post->created)->format(JText::_('DATE_FORMAT_LC2'));
			$post->intro = $post->getIntro(true);
			$post->permalink = $post->getExternalPermalink();

			$posts[] = $post;
		}

		$theme = EB::themes();
		$theme->set('posts', $posts);

		$output = $theme->output('site/composer/posts/default');

		return $this->ajax->resolve($output);

	}

	/**
	 * This does nothing apart from keeping the user's connection active
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function keepAlive()
	{
		return $this->ajax->resolve();
	}

	/**
	 * Renders a list of authors available on the site.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function listAuthors()
	{
		EB::requireLogin();

		$my = JFactory::getUser();

		// Anyone with moderate_entry acl is also allowed to change author.
		if (!FH::isSiteAdmin() && !$this->acl->get('moderate_entry')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$authors = [];
		$pagination = null;

		if (!FH::isSiteAdmin() && !$this->acl->get('moderate_entry')) {
			// always return the current user.
			$user = EB::user($my->id);
			$authors[] = $user;
		} else {

			$model = EB::model('Users');
			$result = $model->getUsers(true, true);
			$pagination = $model->getPagination(true);

			if (!$result) {
				// always return the current user.
				$user = EB::user($my->id);
				$authors[] = $user;
			} else {

				//preload users
				$ids = array();
				foreach ($result as $row) {
					$ids[] = $row->id;
				}

				EB::user($ids);

				foreach ($result as $row) {
					$user = EB::user($row->id);
					$authors[] = $user;
				}

			}
		}

		// Get the selected author
		$selected = $this->input->get('selected', 0, 'int');

		$template = EB::themes();
		$template->set('selected', $selected);
		$template->set('authors', $authors);
		$template->set('pagination', $pagination);

		$output = $template->output('site/composer/panels/post/author/list');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders a list of associates the author has to the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function listAssociates()
	{
		EB::requireLogin();

		// Associates may compromise of teams, groups, events etc.
		$associates = [
			'teams' => [],
			'events' => [],
			'groups' => [],
			'pages' => []
		];

		// Get a list of selected items
		$source_id = $this->input->get('source_id', 0, 'int');
		$source_type = $this->input->get('source_type', '', 'default');

		// List teams the user joined on the site
		$model = EB::model('TeamBlogs');
		$teams = $model->getTeamJoined($this->my->id);

		if ($teams) {
			foreach ($teams as $team) {
				$obj = new stdClass();
				$obj->title = $team->title;
				$obj->source_id = $team->id;
				$obj->source_type = EASYBLOG_POST_SOURCE_TEAM;
				$obj->type = 'team';
				$obj->avatar = $team->getAvatar();
				$associates['teams'][] = $obj;
			}
		}

		// EasySocial groups
		$groups = EB::easysocial()->getGroups();
		$events = EB::easysocial()->getEvents();
		$pages = EB::easysocial()->getPages();

		// List groups the user joined on the site
		$groups = array_merge($groups, EB::jomsocial()->getGroups());
		$events = array_merge($events, EB::jomsocial()->getEvents());

		// Assign them into the main object.
		$associates['groups'] = $groups;
		$associates['events'] = $events;
		$associates['pages'] = $pages;

		$template = EB::themes();
		$template->set('source_id', $source_id);
		$template->set('source_type', $source_type);
		$template->set('associates', $associates);

		$output = $template->output('site/composer/panels/post/author/associates');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows uploading of an audio file to the server temporarily.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function uploadAudio()
	{
		// Check for request forgeries
		// FH::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Ensure that the user really has permissions to create blog posts on the site
		if (!$this->acl->get('add_entry')) {

			EB::exception('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG', EASYBLOG_MSG_ERROR)->setGlobal();

			return $this->ajax->reject();
		}

		$file = $this->input->files->get('file');

		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			echo JText::_("COM_EASYBLOG_COMPOSER_UNABLE_TO_LOCATE_TEMPORARY_FILE");
			exit;
		}

		// Upload this file into their respective images folder.
		$mm = EB::mediamanager();
		$path = $mm->getAbsolutePath('/', 'user:' . $this->my->id);
		$uri = $mm->getAbsoluteURI('/', 'user:' . $this->my->id);

		$result = $mm->upload($file, 'user:' . $this->my->id);

		// Get the audio player which needs to be embedded on the composer.
		$player = EB::audio()->getPlayer($result->url);

		$obj = new stdClass();
		$obj->title = $result->title;
		$obj->player = $player;
		$obj->file = $result->url;
		$obj->path = $result->path;

		echo json_encode($obj);
		exit;
	}

	/**
	 * Location suggestions
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getLocations()
	{
		// Require user to be logged in
		EB::requireLogin();

		$lat = $this->input->get('latitude', '', 'string');
		$lng = $this->input->get('longitude', '', 'string');
		$query = $this->input->get('query', '', 'string');

		// Get the configured service provider for location
		$provider = $this->config->get('location_service_provider');

		$service = EB::location($provider);

		if ($service->hasErrors()) {
			return $this->ajax->reject($service->getError());
		}

		if ($lat && $lng) {
			$service->setCoordinates($lat, $lng);
		}

		if ($query) {
			$service->setSearch($query);
		}

		$venues = $service->getResult($query);

		if ($service->hasErrors()) {
			return $this->ajax->reject($service->getError());
		}

		return $this->ajax->resolve($venues);
	}

	/**
	 * Renders the embed video dialog for legacy posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function embedVideoDialog()
	{
		EB::requireLogin();

		$theme = EB::themes();
		$output = $theme->output('site/composer/media/dialogs/video');

		return $this->ajax->resolve($output);
	}

	/**
	 * Cancel file size warning
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function cancelFileSizeWarning()
	{
		$theme = EB::themes();
		$contents = $theme->output('site/composer/dialogs/cancel.warning');
		return $this->ajax->resolve($contents);
	}

	/**
	 * Untick only one default category from the composer
	 *
	 * @since	5.0.37
	 * @access	public
	 */
	public function errorDefaultCategoryWarning()
	{
		$contents = JText::_('COM_EASYBLOG_UNTICK_DEFAULT_CATEGORY_IN_COMPOSER_ERROR');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Show an error message during suggest tag auto fill process
	 *
	 * @since	5.4.8
	 * @access	public
	 */
	public function errorSuggestTagAutoFill()
	{
		$errorMsg = $this->input->get('errorMsg', '', 'default');

		return $this->ajax->resolve($errorMsg);
	}

	/**
	 * Method to get the post name for post association
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostName()
	{
		$postId = $this->input->get('id', 0, 'int');

		if (!$postId) {
			$contents = JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED');
			return $this->ajax->reject($contents);
		}

		$post = EB::post($postId);

		return $this->ajax->resolve($post->title);
	}

	/**
	 * Allows caller to discard a draft
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function discardDraft()
	{
		EB::requireLogin();

		// Get postId
		$postId = $this->input->get('id', '');

		if (!$postId) {
			$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries');
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');
			return $this->app->redirect($redirect);
		}

		$post = EB::post($postId);

		// Ensure that the user really has permissions to remove draft revisions.
		if (!$post->canDeleteRevision()) {
			$error = EB::exception('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_DRAFTS', EASYBLOG_MSG_ERROR);
			return $this->ajax->reject($error);
		}

		$model = EB::model('Revisions');
		$state = $model->removeDraftRevisions($postId);

		$message = JText::_('COM_EASYBLOG_COMPOSER_DRAFT_DISCARD_SUCCESSFULLY');

		return $this->ajax->resolve($message);
	}

	public function abs2rel()
	{
		$url = $this->input->get('url', '', 'raw');

		if (!$url) {
			$this->ajax->resolve();
		}

		$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

		if ($useRelative) {
			$url = EB::string()->abs2rel($url);
		}

		return $this->ajax->resolve($url);
	}

	/**
	 * Saves user preferences for composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function saveComposerAppearance()
	{
		EB::requireLogin();

		$appearance = $this->input->get('appearance', '', 'string');

		if (!$appearance) {
			return;
		}

		$user = EB::user();
		$params = $user->getParams();
		$params->set('composer_appearance', $appearance);

		$user->params = $params->toString();
		$user->store();

		return $this->ajax->resolve();
	}

	/**
	 * Saves the user preferences of the block browser
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function saveUserBlocksParams()
	{
		EB::requireLogin();

		$preferences = $this->input->get('preferences', [], 'array');

		if (!$preferences) {
			return;
		}

		$preferences = new JRegistry($preferences);

		$user = EB::user();
		$params = $user->getParams();

		$params->set('composer', $preferences->toString());

		$user->params = $params->toString();
		$user->store();

		return $this->ajax->resolve();
	}

	/**
	 * Set video cover in composer
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function setVideoCover()
	{
		$videoUrl = $this->input->get('url', '', 'raw');
		$videoHtml = $this->input->get('html', '', 'raw');

		if (!$videoUrl && !$videoHtml) {
			return;
		}

		$mediaLib = EB::media();

		if (empty($videoHtml)) {
			$videoOptions = array(
								'width' => '260',
								'height' => '200',
								'ratio' => '',
								'muted' => false,
								'autoplay' => false,
								'loop' => false,
								'isCover' => true
							);

			$videoHtml = $mediaLib->renderVideoPlayer($videoUrl, $videoOptions, false);
		}


		$theme = EB::themes();
		$theme->set('mediaLib', $mediaLib);
		// $theme->set('videoUrl', $videoUrl);
		$theme->set('videoHtml', $videoHtml);
		// $theme->set('videoOptions', $videoOptions);

		$output = $theme->output('site/composer/toolbar/videocover');

		return $this->ajax->resolve($output);
	}

	/**
	 * Set image cover in composer
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function setImageCover()
	{
		$imageUrl = $this->input->get('url', '', 'raw');

		if (!$imageUrl) {
			return;
		}

		$test = true;

		$theme = EB::themes();
		$theme->set('imageUrl', $imageUrl);
		$theme->set('test', $test);

		$output = $theme->output('site/composer/toolbar/imagecover');

		return $this->ajax->resolve($output);
	}

	/**
	 * Retrieve the default category post cover in the composer
	 *
	 * @since    6.0.0
	 * @access    public
	 */
	public function getDefaultPostCover()
	{
		$catId = $this->input->get('catId', 0, 'int');

		if (!$catId) {
			return $this->ajax->resolve();
		}

		$category = EB::table('Category');
		$category->load($catId);

		if (!$category->id) {
			return $this->ajax->resolve();
		}

		$imageUrl = $category->getDefaultPostCover();
		$output = false;

		if ($imageUrl) {
			$themes = EB::themes();
			$themes->set('imageUrl', $imageUrl);

			$output = $themes->output('site/composer/toolbar/imagecover');
		}

		return $this->ajax->resolve($output);
	}

	/**
	 * Display dialog for post template saving
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function saveAsPostTemplateDialog()
	{
		$id = $this->input->get('template_id', 0, 'int');

		$template = EB::table('PostTemplate');
		$template->load($id);

		// Updating the template
		if ($template->id && !$template->canEdit()) {
			$template = EB::table('PostTemplate');
		}

		$theme = EB::themes();
		$theme->set('template', $template);

		$contents = $theme->output('site/composer/dialogs/save.as.template');
		return $this->ajax->resolve($contents);
	}

	/**
	 * Display confirmation dialog to save a block template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function saveBlockAsTemplate()
	{
		EB::requireLogin();

		// Check for permission
		if (!FH::isSiteAdmin() && !$this->acl->get('create_block_templates')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$id = $this->input->get('id', 0, 'int');

		$template = EB::table('BlockTemplates');
		$template->load($id);

		if ($template->id && !$template->isOwner()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$theme = EB::themes();
		$theme->set('template', $template);

		$contents = $theme->output('site/composer/dialogs/save.block.template');
		return $this->ajax->resolve($contents);
	}

	/**
	 * Remembering the section of the panel given for the current user
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function savePanelPreferences()
	{
		EB::requireLogin();

		if (!$this->config->get('layout_composer_remember_panels') || !$this->acl->get('add_entry')) {
			return $this->ajax->reject();
		}

		$section = $this->input->get('section', '', 'string');
		$isOpen = $this->input->get('isOpen', 0, 'bool');
		$userId = JFactory::getUser()->id;

		$table = EB::table('Profile');
		$table->load($userId);

		// Get the current panel preferences
		$params = $table->getParams();
		$preferences = $table->getPanelPreferences();

		$isOpen = $isOpen ? 1 : 0;

		// Update the section
		$preferences->set($section, $isOpen);

		// Update the panel preferences with the updated value
		$params->set('panelPreferences', $preferences->toString());

		$table->params = $params->toString();
		$table->store();

		return $this->ajax->resolve();
	}
}
