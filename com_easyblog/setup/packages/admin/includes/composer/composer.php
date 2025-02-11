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

class EasyBlogComposer extends EasyBlog
{
	public $user;
	
	public function __construct()
	{
		parent::__construct();

		// Set the easyblog user object
		$this->user = EB::user($this->my->id);
	}

	/**
	 * Determines the editor to use
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getEditor()
	{
		// Get the editor to use
		$editorSetting = $this->user->getEditor();
		$editorSetting = $editorSetting == 'composer' ? JFactory::getConfig()->get('editor') : $editorSetting;

		$editor = EBFactory::getEditor($editorSetting);

		return $editor;
	}

	/**
	 * Determines if the composer is rendering the default built in composer
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isComposer()
	{
		$editor = $this->user->getEditor();

		if ($editor == 'composer') {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the default category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getDefaultCategory()
	{
		// Get the default category.
		$category = EB::model('Category')->getDefaultCategory();

		return $category;
	}

	/**
	 * Handler the template's editor.css file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderEditorCss()
	{
		$model = EB::model('Themes');
		$template = $model->getDefaultJoomlaTemplate();
		$path = JPATH_ROOT . '/templates/' . $template;

		// Render EB typography.
		if ($this->config->get('enable_typography')) {
			$typographyPath = JPATH_ROOT . '/components/com_easyblog/themes/wireframe/styles/typography.css';
			$typographyCss = '/components/com_easyblog/themes/wireframe/styles/typography.css';

			if (JFile::exists($typographyPath)) {
				$this->doc->addStylesheet($typographyCss, array('version' => 'auto'));
			}
		}

		// Check if the current Joomla template has an editor.css around
		$cssPath = $path . '/css/editor.css';
		$cssUri = rtrim(JURI::root(), '/') . '/templates/' . $template . '/css/editor.css';

		if (JFile::exists($cssPath)) {
			$this->doc->addStylesheet($cssUri);
			return;
		}

		// Check if the current Joomla template has an editor.css around
		$cssPath = JPATH_ROOT . '/templates/system/css/editor.css';
		$cssUri = rtrim(JURI::root(), '/') . '/templates/system/css/editor.css';

		if (JFile::exists($cssPath)) {
			$this->doc->addStylesheet($cssUri);
			return;
		}

		return;
	}

	/**
	 * Retrieves the dropbox data for the current user
	 *
	 * @since	4.0
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
	 * Retrieves a list of categories
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function getParentCategories()
	{
		// Load up categories available on the site
		$model = EB::model('Categories');
		$result = $model->getParentCategories('', 'all', true, true);

		$categories = new stdClass();
		$categories->primary = null;
		$categories->items = array();

		foreach ($result as $row) {
			$category = EB::table('Category');
			$category->bind($row);

			if ($category->default) {
				$categories->primary = $category;
			}

			$categories->items[] = $category;
		}

		// if there is no primary category,
		// let select the 1st category as the default one.
		if(! $categories->primary) {
			$categories->primary = $categories->items[0];
		}

		return $categories;
	}

	/**
	 * Retrieves the meta for a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostMeta($post)
	{
		$model = EB::model('Metas');
		$meta = $model->getPostMeta($post->id);

		return $meta;
	}

	/**
	 * Retrieves a list of tags associated with a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function getPostTags($post)
	{
		$model = EB::model('PostTag');
		$tags = $model->getBlogTags($post->id);

		return $tags;
	}

	/**
	 * Get appearance mode for the composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getAppearance()
	{
		// Determine if the user changed their appearance
		$user = EB::user();
		$params = $user->getParams();

		$appearance = $params->get('composer_appearance', $this->config->get('layout_composer_appearance'));

		$responsive = EB::responsive();

		// If webview, we check for app appearance.
		if ($responsive->isEBTabletWevbiew()) {
			$agent = $responsive->getAgent();

			preg_match('/composer-mode="(.*)"/is', $agent, $matches);

			if (count($matches) > 1 && isset($matches[1])) {
				$appearance = in_array($matches[1], ['dark', 'light']) ? $matches[1] : $appearance;
			}
		}

		return $appearance;
	}

	/**
	 * Retrieves a list of teams from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTeams($authorId = null)
	{
		$model = EB::model('TeamBlogs');

		if ($authorId) {
			$teams = $model->getTeamJoined($authorId);
		}

		return $teams;
	}

	/**
	 * Retrieves the html codes for composer
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderManager($uid = null)
	{
		// Get the current post library
		$post = EB::post($uid);
		$revision = $post->getWorkingRevision();
		$revisionContent = json_decode($revision->content);

		// lets check if we have any data stored in the session or not.
		$sessionData = EB::getSession('EASYBLOG_COMPOSER_POST');
		if ($sessionData) {
			// restoring data from the form post.
			$post->restoreFromPost($sessionData);
		}

		// Get the editor to use
		$editor = $this->getEditor();

		// There is a possibility that this is a legacy post
		if ($this->isComposer() && !$post->isLegacy()) {
			$this->renderEditorCss();
		} else {
			// Only render behavior.modal when the editor isn't composer
			EBCompat::renderModalLibrary();
		}

		// Get a list of parent categories
		$parentCategories = $this->getParentCategories();

		// Get the default category.
		$defaultCategoryId = EB::model('Category')->getDefaultCategoryId();

		// Allow caller to alter default category
		if ($post->isBlank()) {

			// There is a possibility that someone sets an invalid "category id" in the request
			$categoryIdFromRequest = $this->input->get('category', $defaultCategoryId, 'int');

			if ($categoryIdFromRequest) {
				$categoryFromRequest = EB::table('Category');
				$categoryFromRequest->load($categoryIdFromRequest);

				if ($categoryFromRequest->id) {
					$defaultCategoryId = $categoryFromRequest->id;
				}
			}
		}

		$primaryCategory = $post->getPrimaryCategory();

		// If the menu has a default category, the primary category should be the pre-selected one.
		// And this shouldn't happening on draft post because user no need to re-configure which category should set as primary again
		if (!$post->isFromFeed() && $post->isNew() && !$post->isDraft() && $defaultCategoryId && !$post->isScheduled() && !$post->isPending()) {
			$primaryCategory = EB::table('Category');
			$primaryCategory->load($defaultCategoryId);
		}

		$postCategories = $post->getCategories();

		// Get primary category from the revision if this post is pending for approval purpose.
		if ($post->isPending() && $revision) {

			// Reload primary category for revisionss
			$primaryCategory = EB::table('Category');
			$primaryCategory->load($revisionContent->category_id);

			// Get the rest of categories
			$revisionCategories = $revisionContent->categories;
			$postCategories = array();

			foreach ($revisionCategories as $categoryId) {
				$table = EB::table('Category');
				$table->load($categoryId);
				$postCategories[] = $table;
			}
		}

		// Get a list of categories
		// Prepare selected category
		$selectedCategories = array();
		$selectedCategoriesId = array();

		foreach ($postCategories as $row) {

			$cat = EB::table('Category');
			$cat->load($row->id);

			$selectedCategories[] = $cat;
			$selectedCategoriesId[] = (int) $row->id;
		}

		// if there is no category selected, or this is a new blog post, lets use the default category id.
		if (!$selectedCategories && $defaultCategoryId) {
			$defaultCategory = EB::table('Category');
			$defaultCategory->load($defaultCategoryId);

			$selectedCategories[] = $defaultCategory;
			$selectedCategoriesId[] = $defaultCategory->id;
		}

		// attached selectedCategories as parentCategories so that frontend able to access it.
		$parentIds = array();
		foreach ($parentCategories->items as $row) {
			$parentIds[] = $row->id;
		}

		foreach ($selectedCategories as $cat) {
			if (! in_array($cat->id, $parentIds)) {
				$parentCategories->items[] = $cat;
			}
		}

		// Prepare categories object
		$categories = array();

		foreach ($parentCategories->items as $row) {

			$category = new stdClass();
			$category->id = (int) $row->id;
			$category->title = JText::_($row->title, true);
			$category->parent_id = (int) $row->parent_id;

			$category->lft = $row->lft;
			$category->rgt = $row->rgt;

			$params = new JRegistry($row->params);

			$category->tags = $params->get('tags');

			// get childs count
			$category->childs = $row->getChildCount(true);

			if ($category->tags && $this->config->get('layout_composer_tags')) {
				$tags = explode(',', $category->tags);

				for($i = 0; $i < count($tags); $i++) {
					$tags[$i] = EBString::trim($tags[$i]);
				}

				$category->tags = implode(',', $tags);
			} else {
				$category->tags = array();
			}

			// Cross check if this category is selected
			$category->selected = in_array($category->id, $selectedCategoriesId);

			$category->hadChildSelectedCount = 0;

			foreach ($selectedCategories as $selected) {

				// we only want the immediate childs count.
				// if ($selected->lft > $category->lft && $selected->rgt < $category->rgt) {
				// 	$category->hadChildSelectedCount++;
				// }

				// we only want the immediate childs count.
				if ($selected->lft > $category->lft && $selected->rgt < $category->rgt && ($category->rgt - $category->lft == 1)) {
					$category->hadChildSelectedCount++;
				} else if ($selected->lft > $category->lft && $selected->rgt < $category->rgt) {
					$category->hadChildSelectedCount = 1;
				}
			}

			// check if this is a primary category or not
			$category->isprimary = $category->id == $primaryCategory->id;

			$categories[] = $category;
		}

		// Prepare tags
		$tags = array();
		foreach ($post->getTags() as $row) {
			$tag = new stdClass();
			$tag->id = (int) $row->id;
			$tag->title = $row->title;

			$tags[] = $tag;
		}

		// Get the default tags
		$defaultTags = EB::model('Tags')->getDefaultTagsTitle();

		// Render default post templates
		$postTemplatesModel = EB::model('Templates');
		$postTemplates = $postTemplatesModel->getPostTemplates($this->my->id, false, true, $post->doctype);

		$totalTemplates = count($postTemplates);
		$singleTemplate = false;

		if ($totalTemplates == 1) {

			// Blank template will always use default empty post content. #214
			if (!$postTemplates[0]->isBlank()) {
				$singleTemplate = $postTemplates[0];
			}
		}

		// Further check if this post is already being writen before, we should not display post template. #428
		$uidExists = $this->input->get('uid');

		if ($uidExists) {
			$postTemplates = false;
			$singleTemplate = false;
		}

		// Revisions
		$revisions = $post->getRevisions('desc');
		$workingRevision = $post->getWorkingRevision();

		$blockType = 'ebd';

		// Only load media blocks for media manager use. #1735
		if ($post->isLegacy()) {
			$blockType = 'legacy';
		}

		// Get available blocks on the site
		$blocks = EB::blocks()->getAvailableBlocks($blockType);

		// Get available blocks template
		$blockTemplates = EB::blocks()->getBlockTemplates(array('userId' => $this->my->id));

		// Get a list of selected categories
		$selectedCategories = $post->getCategories();

		$totalAvailableCategories = count($categories);

		$tmpRegistry = new JRegistry();
		$dispatcher = EB::dispatcher();
		$dispatcher->trigger('onEasyBlogPrepareComposerCategories', array(&$categories, $selectedCategories));

		// due to 3rd party might altering the categories. e.g. disallow primary category from select in payplans. we need to recheck the primary category.
		// #1693
		$totalAvailableCategoriesAftterTrigger = count($categories);

		if ($totalAvailableCategoriesAftterTrigger < $totalAvailableCategories) {

			// look like there are missing categories after the trigger.
			// we need to recheck for the selected primary category.
			$hasSelected = false;
			foreach ($categories as $cat) {
				if ($cat->selected) {
					$hasSelected = true;
					break;
				}
			}

			// selected categories not found. lets reselect a category manually.
			if (!$hasSelected) {

				$hasPrimary = false;
				// lets check if there is any primary unselected categories or not.
				for ($i = 0; $i < count($categories); $i++) {

					$cat =& $categories[$i];

					if ($cat->isprimary) {

						// lets use this one.
						$cat->selected = true;
						$hasPrimary = true;
						break;
					}
				}

				// no primary category found. lets use the next available category.
				if (!$hasPrimary) {

					$nextPrimaryCat = $categories[0];
					// update the flag
					$nextPrimaryCat->isprimary = true;
					$nextPrimaryCat->selected = true;
					$categories[0] = $nextPrimaryCat;

					$primaryCategory = EB::table('Category');
					$primaryCategory->load($nextPrimaryCat->id);
				}
			}
		}

		// Determines if we should display the custom fields tab by default
		$displayFieldsTab = false;

		// Get a list of selected categories
		$selectedCategories = $post->getCategories();

		// If there's no selected categories, we assume that the primary category
		if (!$selectedCategories) {
			$selectedCategories = array($primaryCategory);
		}

		// If explicitly configured to be hidden, skip the checks altogether
		if ($this->config->get('layout_composer_fields')) {
			foreach ($selectedCategories as $category) {
				if ($category->hasCustomFields()) {
					$displayFieldsTab = true;
					break;
				}
			}
		}

		// we need to check if this post has any draft copy or not.
		// if yes, we need to alert user to either continue from the draft or discard the draft post.
		// #259
		$draftRevision = '';
		if (!$post->isBlank() && !$post->isDraft() && !$post->isPending()) {
			$draftRevision = $post->getLatestDraftRevision();
		}

		$user = EB::table('Profile');
		$user = $user->load($this->my->id);

		//available languages
		$languages = JLanguageHelper::getLanguages('lang_code');

		//post association
		$associations = $post->getAssociation();

		// Short language tag
		$momentLanguage = EB::getMomentLanguage();

		// Get Google map api key
		$gMapkey = $this->config->get('googlemaps_api_key');

		// Process any info that needs to be displayed
		$alert = EB::info()->getMessage();

		$returnUrl = $this->getReturnUrl();

		// Get list of ACL user group
		$model = EB::model('Acls');
		$aclRuleSets = $model->getRuleSets();

		$aclNotification = $post->getParams()->get('aclNotification');

		if ($this->config->get('layout_composer_customnotifications') && $aclNotification) {
			foreach ($aclRuleSets as $acl) {

				$acl->selected = false;

				if (in_array($acl->id, $aclNotification)) {
					$acl->selected = true;
				}
			}
		}

		// Construct draft edit link
		$draftEditLink = false;

		if ($draftRevision) {
			$draftEditLink = EBR::_('index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $post->id . '.' . $draftRevision->id);

			if (EB::isFromAdmin()) {
				$draftEditLink = rtrim(JURI::base(true), '/') . '/index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $post->id . '.' . $draftRevision->id;
			}
		}

		// Load legacy editor template based on the Joomla version
		$legacyEditorNamespace = 'site/composer/editor/legacy';

		if (FH::isJoomla4()) {
			$legacyEditorNamespace = 'site/composer/editor/legacyj4';
		}

		$userParams = $user->getParams();
		$composerPreferences = $userParams->get('composer', null);

		if ($composerPreferences) {
			$composerPreferences = new JRegistry($composerPreferences);
		}

		$undoPublishing = $this->config->get('publish_post_confirmation') ? 1 : 0;
		$postTemplateIsLocked = $post->isPostTemplateLocked();
		$postTemplateId = $post->getPostTemplateId();

		// Check for template lock
		if ($post->isNew() && $singleTemplate) {
			$postTemplateIsLocked = $singleTemplate->isLocked();
			$postTemplateId = $singleTemplate->id;
		}

		$videoOptions = [
			'width' => '400',
			'height' => '100',
			'ratio' => '',
			'muted' => false,
			'autoplay' => false,
			'loop' => false
		];
		$mediaLib = EB::media();
		$imageLib = EB::image();

		$defaultCategoryPostCover = $post->getCategoryDefaultPostCover();

		$coverUrl = null;
		$isImage = false;

		// Display the its category's default post cover if there is
		if (!$post->image && $defaultCategoryPostCover) {
			$coverUrl = $defaultCategoryPostCover;
		}

		if ($post->image) {
			$coverUrl = $post->getImage();
		}

		if ($coverUrl) {
			$imageFileName = $imageLib->getFileName($coverUrl);
			$isImage = $imageLib->isImage($imageFileName) || EB::unsplash()->isValidUrl($coverUrl);
		}

		// #2806 - Fixed possible issue when the post cover's image is deleted via media manager
		if ($isImage && $coverUrl) {
			$mm = EB::mediamanager();
			$placeId = $mm->getPlaceId($post->image);
			$sourceType = $mm->getSourceType($placeId);

			if ($sourceType === EBLOG_MEDIA_SOURCE_LOCAL && !JFile::exists($mm->getPath($post->image))) {
				$isImage = false;
				$post->image = null;
				$coverUrl = null;
			}
		}

		// Determines the platform of the viewer
		$app = JFactory::getApplication();

		// Get current viewer's operating system
		$navigator = EBCompat::getNavigator();
		$platform = $navigator->getPlatform();

		$isPanelPreferencesEnabled = $this->config->get('layout_composer_remember_panels');
		$panelPreferences = $user->getPanelPreferences();

		JPluginHelper::importPlugin('easyblog');
		$dispatcher = EB::dispatcher();
		$onEasyblogPrepareEditForm = implode('', $dispatcher->trigger('onEasyblogPrepareEditForm', [$post, '']));

		// Determine if this is being view in webview
		$isWebview = EB::responsive()->isEBTabletWevbiew();

		$theme = EB::themes();
		$theme->set('onEasyblogPrepareEditForm', $onEasyblogPrepareEditForm);
		$theme->set('platform', $platform);
		$theme->set('composerPreferences', $composerPreferences);
		$theme->set('templateEditor', false);
		$theme->set('returnUrl', $returnUrl);
		$theme->set('alert', $alert);
		$theme->set('user', $user);
		$theme->set('displayFieldsTab', $displayFieldsTab);
		$theme->set('postTemplates', $postTemplates);
		$theme->set('totalTemplates', $totalTemplates);
		$theme->set('singleTemplate', $singleTemplate);
		$theme->set('workingRevision', $workingRevision);
		$theme->set('revisions', $revisions);
		$theme->set('editor', $editor);
		$theme->set('primaryCategory', $primaryCategory);
		$theme->set('categories', $categories);
		$theme->set('tags', $tags);
		$theme->set('post', $post);
		$theme->set('uuid', uniqid());
		$theme->set('blocks', $blocks);
		$theme->set('languages', $languages);
		$theme->set('associations', $associations);
		$theme->set('momentLanguage', $momentLanguage);
		$theme->set('gMapkey', $gMapkey);
		$theme->set('draftRevision', $draftRevision);
		$theme->set('aclRuleSets', $aclRuleSets);
		$theme->set('draftEditLink', $draftEditLink);
		$theme->set('defaultTags', $defaultTags);
		$theme->set('undoPublishing', $undoPublishing);
		$theme->set('videoOptions', $videoOptions);
		$theme->set('imageLib', $imageLib);
		$theme->set('mediaLib', $mediaLib);
		$theme->set('isImage', $isImage);
		$theme->set('blockTemplates', $blockTemplates);
		$theme->set('appearance', $this->getAppearance());

		// Determines if the source id and source type is provided
		$sourceId = $this->input->get('source_id', 0, 'int');
		$sourceType = $this->input->get('source_type', '', 'default');
		$contribution = '';

		if ($sourceId && $sourceType) {
			$contribution = EB::contributor()->load($sourceId, $sourceType);
			$post->source_id = $sourceId;
			$post->source_type = $sourceType;
		}

		$theme->set('contribution', $contribution);
		$theme->set('sourceId', $sourceId);
		$theme->set('sourceType', $sourceType);
		$theme->set('legacyEditorNamespace', $legacyEditorNamespace);
		$theme->set('postTemplateIsLocked', $postTemplateIsLocked);
		$theme->set('postTemplateId', $postTemplateId);
		$theme->set('isPanelPreferencesEnabled', $isPanelPreferencesEnabled);
		$theme->set('panelPreferences', $panelPreferences);
		$theme->set('googleImportEnabled', EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE)->isEnabled());
		$theme->set('coverUrl', $coverUrl);
		$theme->set('isWebview', $isWebview);
		$theme->set('isShortCutsEnabled', $this->config->get('layout_composer_shortcuts'));

		$output = $theme->output('site/composer/manager');

		return $output;
	}

	/**
	 * Retrieves the html codes for composer
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderTemplateManager($templateId = null)
	{
		// Get the current post library
		$post = EB::post(null);

		// Default editor to use
		$editor = $this->getEditor();

		$parentCategories = $this->getParentCategories();
		$defaultCategory = $this->getDefaultCategory();
		$primaryCategory = $defaultCategory;

		// Prepare selected categories
		$selectedCategories = array($defaultCategory);
		$selectedCategoriesId = array($defaultCategory->id);

		// Prepare tags
		$tags = array();

		// Determines if we should display the custom fields tab by default
		$displayFieldsTab = false;

		// If explicitly configured to be hidden, skip the checks altogether
		if ($this->config->get('layout_composer_fields')) {
			foreach ($selectedCategories as $category) {
				if ($category->hasCustomFields()) {
					$displayFieldsTab = true;
					break;
				}
			}
		}

		// Available languages
		$languages = JLanguageHelper::getLanguages('lang_code');

		// Short language tag
		$momentLanguage = EB::getMomentLanguage();

		// Get Google map api key
		$gMapkey = $this->config->get('googlemaps_api_key');

		// Process any info that needs to be displayed
		$alert = EB::info()->getMessage();

		$postTemplate = EB::table('PostTemplate');
		$postTemplate->load($templateId);

		// lets check if we have any data stored in the session or not.
		$sessionData = EB::getSession('EASYBLOG_COMPOSER_POST_TEMPLATES');

		if ($sessionData) {
			// restoring data from the form post.
			$postTemplate->restoreFromPost($sessionData);
		}

		$returnUrl = $this->getReturnUrl();

		// Load legacy editor template based on the Joomla version
		$legacyEditorNamespace = 'site/composer/editor/legacy';

		if (FH::isJoomla4()) {
			$legacyEditorNamespace = 'site/composer/editor/legacyj4';
		}

		// Get the default tags
		$defaultTags = EB::model('Tags')->getDefaultTagsTitle();

		$blockType = 'ebd';

		// Only load media blocks for media manager use. #1735
		if ($postTemplate->isLegacy()) {
			$blockType = 'legacy';
		}

		// Get available blocks on the site
		$blocks = EB::blocks()->getAvailableBlocks($blockType);

		// Determine if this is being view in webview
		$isWebview = EB::responsive()->isEBTabletWevbiew();

		$theme = EB::themes();
		$theme->set('onEasyblogPrepareEditForm', '');
		$theme->set('postTemplate', $postTemplate);
		$theme->set('composerPreferences', null);
		$theme->set('templateEditor', true);
		$theme->set('alert', $alert);
		$theme->set('displayFieldsTab', $displayFieldsTab);
		$theme->set('editor', $editor);
		$theme->set('primaryCategory', $primaryCategory);
		$theme->set('categories', $selectedCategories);
		$theme->set('tags', $tags);
		$theme->set('post', $post);
		$theme->set('uuid', uniqid());
		$theme->set('blocks', $blocks);
		$theme->set('gMapkey', $gMapkey);
		$theme->set('user', $this->user);
		$theme->set('momentLanguage', $momentLanguage);
		$theme->set('languages', $languages);
		$theme->set('returnUrl', $returnUrl);
		$theme->set('draftRevision', '');
		$theme->set('legacyEditorNamespace', $legacyEditorNamespace);
		$theme->set('defaultTags', $defaultTags);
		$theme->set('postTemplateIsLocked', false);
		$theme->set('appearance', $this->getAppearance());
		$theme->set('isPanelPreferencesEnabled', 0); // for template manager, we need to disable the panel preferences.
		$theme->set('isWebview', $isWebview);
		$theme->set('isShortCutsEnabled', $this->config->get('layout_composer_shortcuts'));
		$theme->set('blockTemplates', []);

		$output = $theme->output('site/composer/manager');

		return $output;
	}

	/**
	 * Set return url into the session
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function setReturnUrl($url = null)
	{
		// Directly set the url
		if ($url) {
			return EB::setCallBack($url);
		}

		// Try to get referer url
		$previousUrl = EBR::getReferer();

		if ($previousUrl) {

			$setUrl = true;

			// Check if this is composer url
			parse_str($previousUrl, $query);

			// Only set the url if url is not from composer
			if (isset($query['view']) && $query['view'] === 'composer') {
				$setUrl = false;
			}

			if ($setUrl) {
				EB::setCallBack($previousUrl);
			}
		}
	}

	/**
	 * Process return url to exit the composer
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getReturnUrl()
	{
		// Get return url from user session
		$default = EBR::_('index.php?option=com_easyblog');

		// Capture any return url from the request
		$return = $this->input->get('return', '', 'BASE64');

		if ($return) {

			$return = base64_decode($return);

			// Set return url in session if there is any
			$this->setReturnUrl($return);

			return $return;
		}

		$callback = EB::getCallBack($default, false);

		return $callback;
	}

	/**
	 * Generate url to view the composer
	 *
	 * @since	5.1.13
	 * @access	public
	 */
	public function getComposeUrl($options = array(), $xhtml = true)
	{
		$permalink = 'index.php?option=com_easyblog&view=composer&tmpl=component';

		// Default return url
		$return = base64_encode(EBR::current());

		// Set return url if there any
		if (isset($options['return'])) {
			$return = $options['return'];
			unset($options['return']);
		}

		// we will add the return segment separately due to the return is in base64
		// and jroute will most likley break the value. #1794
		// $options['return'] = $return;

		if ($options) {
			foreach ($options as $option => $value) {
				$permalink = $permalink . '&' . $option . '=' . $value;
			}
		}

		if (!EB::isFromAdmin()) {
			$permalink = EBR::_($permalink, $xhtml);
		}

		// after jroute, we can now add the return. #1794
		if ($return) {
			$concat = '?';
			if (stristr($permalink, '?') !== false) {
				$concat = '&';
			}
			$permalink .= $concat . 'return=' . urlencode($return);
		}

		return $permalink;
	}

	/**
	 * Generate url to view a template composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getTemplateComposeUrl($options = array(), $xhtml = true)
	{
		$permalink = 'index.php?option=com_easyblog&view=templates&tmpl=component';

		// Default return url
		$return = base64_encode(EBR::current());

		// Set return url if there any
		if (isset($options['return'])) {
			$return = $options['return'];
		}

		// we will add the return segment separately due to the return is in base64
		// and jroute will most likley break the value. #1794
		// $options['return'] = $return;

		if ($options) {
			foreach ($options as $option => $value) {
				$permalink = $permalink . '&' . $option . '=' . $value;
			}
		}

		if (!EB::isFromAdmin()) {
			$permalink = EBR::_($permalink, $xhtml);
		}

		// after jroute, we can now add the return. #1794
		if ($return) {
			$concat = '?';
			if (stristr($permalink, '?') !== false) {
				$concat = '&';
			}
			$permalink .= $concat . 'return=' . urlencode($return);
		}

		return $permalink;
	}
}
