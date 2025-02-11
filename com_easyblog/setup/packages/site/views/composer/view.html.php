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

require_once(EB_ROOT . '/views/views.php');

class EasyBlogViewComposer extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Handles backedn
		if (EB::isFromAdmin() && method_exists($this, $this->getLayout())) {
			$layoutName = $this->getLayout();
			return $this->$layoutName();
		}

		// If there is no tmpl=component, we will enforce this
		$tmpl = $this->input->get('tmpl', '');

		if ($tmpl != 'component') {
			$this->input->set('tmpl', 'component');
		}

		// Ensure that the user is logged in.
		EB::requireLogin();

		// null = new post
		// 63   = post 63 from post table
		// 63.2 = post 63, revision 2 from history table
		$uid = $this->input->getVar('uid', null);

		// If no id given, create a new post.
		$post = EB::post($uid);

		// If there is a uid, ensure that it is loadable
		if ($uid && !$post->id) {
			$redirect = EBR::_('index.php?option=com_easyblog', false);
			return $this->app->redirect($redirect);
		}

		// Do not allow normal user to edit the existing post if there is already another revision that was submitted for approvals.
		if (!FH::isSiteAdmin() && !$this->acl->get('moderate_entry') && (!$this->acl->get('manage_pending') || !$this->acl->get('publish_entry')) && $post->hasRevisionWaitingForApproval()) {
			throw EB::exception(JText::_('COM_EASYBLOG_NOT_ALLOWED_TO_EDIT_ANOTHER_REVISION_PENDING_APPROVAL'), 500);
			return;
		}

		// we need to set the jdoc base to root so that images with src = images/xxxx/yyy.jpg can render properly inside editor. #955
		$this->doc->setBase(JURI::root());

		// Prevent zooming
		$this->doc->setMetaData('viewport', 'width=device-width, initial-scale=1, maximum-scale=1');

		// If this is a compare, we need to directly load the compare windows
		$compareId = $this->input->get('compareid', '');

		// Do not allow user to access this page if he doesn't have access
		// Verify access (see function manager())
		if (!$post->canCreate()) {
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
		}

		// If there's no id provided, we will need to create the initial revision for the post.
		if (!$uid) {
			$post->create();
			$uid = $post->uid;
		}

		// check if user has edit blog post ability or not.
		if (!FH::isSiteAdmin() && !$post->isBlank() && !$post->isDraft() && !$post->canEdit() && !($post->isUnpublished() && $post->isNew()) ) {
			$this->info->set('COM_EB_NOT_ALLOWED_TO_EDIT_POST', 'error');
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
		}

		if (!$post->title) {
			$this->doc->setTitle(JText::_('COM_EASYBLOG_COMPOSER_POST_UNTITLED'));
		}

		$composer = EB::composer();

		// Detect the keep alive interval that we should use
		$keepAlive = (int) $this->jconfig->get('lifetime');
		$keepAlive = round($keepAlive / 2);
		$keepAlive = $keepAlive > 16 ? 16 : $keepAlive;
		$keepAlive = $keepAlive * 10000;

		$isWebview = EB::responsive()->isEBTabletWevbiew();

		$this->set('appearance', $composer->getAppearance());
		$this->set('keepAlive', $keepAlive);
		$this->set('composer', $composer);
		$this->set('post', $post);
		$this->set('compareId', $compareId);
		$this->set('isWebview', $isWebview);

		return parent::display('composer/default');
	}

	/**
	 * Retrieves a list of posts created by the user or on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts($tmpl = null)
	{
		// Ensure that the user is logged in.
		EB::requireLogin();

		// Ensure that the user has authoring permissions
		$user = EB::user();

		if (!$user->canCompose()) {
			die('Invalid request');
		}

		$lang = $this->input->getVar('code', null);
		$langid = $this->input->getVar('codeid', null);
		$search = $this->input->getVar('query', '');

		// Admin might want to display the featured blogs on all pages.
		$start = $this->input->get('start', 0, 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// conditions
		$options = array(
			'langcode' => $lang,
			'search' => ''
		);

		if ($search) {
			$options['search'] = $search;
		}

		$model = EB::model('Blog');
		$data = $model->getAssociationPosts($options);

		// Get the pagination
		$pagination = $model->getPagination();

		if ($data) {
			EB::cache()->insert($data);
		}

		if (EB::isFromAdmin()) {
			EB::loadLanguages();

			$pagination->setAdditionalUrlParam('option', 'com_easyblog');
			$pagination->setAdditionalUrlParam('view', 'composer');
			$pagination->setAdditionalUrlParam('layout', 'getPosts');
			$pagination->setAdditionalUrlParam('code', $lang);
			$pagination->setAdditionalUrlParam('codeid', $langid);
			$pagination->setAdditionalUrlParam('query', $search);
			$pagination->setAdditionalUrlParam('browse', true);
			$pagination->setAdditionalUrlParam('tmpl', 'component');
		}

		$posts = EB::formatter('list', $data, false);

		$this->set('posts', $posts);
		$this->set('langcode', $lang);
		$this->set('langid', $langid);
		$this->set('search', $search);
		$this->set('pagination', $pagination);

		parent::display('composer/posts/listing');
	}

	/**
	 * Retrieves a list of google docs
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function googleFileList($tmpl = null)
	{
		EB::requireLogin();

		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);

		// check if google import enabled or not.
		if (!$client->isEnabled()) {
			throw EB::exception('COM_EB_GOOGLEIMPORT_ERROR_FEATURE_DISABLED', 'error');
		}

		if (EB::isFromAdmin()) {
			// lets load frontend language file
			EB::loadLanguages();
		}

		$search = $this->input->get('search', '', 'default');
		$nextPageToken = $this->input->get('nextPageToken', '', 'default');

		$files = [];
		$nextPage = '';

		$data = $client->getGDriveFiles($this->my->id, $nextPageToken, $search);
		if ($data) {
			$files = $data->files;
			$nextPage = $data->nextPageToken;
		}

		// debug data
		// $data = [];

		// for($i = 1; $i <= 50; $i++) {

		// 	$obj = new stdClass();
		// 	$obj->id = $i;
		// 	$obj->title = 'This is a test document No. ' . $i;

		// 	$data[$i] = $obj;
		// }

		// $tmpIds = array_rand($data, 10);
		// foreach ($tmpIds as $idx) {
		// 	$files[] = $data[$idx];
		// }

		// $nextPage = 'dfafafdasfasfasf';
		// debug end

		$this->set('files', $files);
		$this->set('nextPage', $nextPage);
		$this->set('search', $search);

		return parent::display('googleimport/default/default');
	}

}
