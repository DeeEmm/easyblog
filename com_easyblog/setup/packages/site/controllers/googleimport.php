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

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerGoogleimport extends EasyBlogController
{

	public function __construct($config = array()) 
	{
		if (EB::isFromAdmin()) {
			// lets load frontend language file
			EB::loadLanguages();
		}

		parent::__construct();
	}

	/**
	 * Deletes a revision from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function import()
	{
		// Check for token
		FH::checkToken();
		EB::requireLogin();

		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);

		if (!$client->isEnabled()) {
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_FEATURE_DISABLED'));
		}

		// // Load the revision
		$docId = $this->input->get('id', '', 'string');
		$postId = $this->input->get('postId', 0, 'int');
		$title = $this->input->get('title', '', 'string');
		$importStyle = $this->input->get('importStyle', 'inline', 'string');

		if (!$docId || !$title) {
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_INVALID_DATA'));
		}

		// debug code
		// $docId = '1WY7bYWm8VKuM1-fW8s5rYWBBEMYM4BH97ToI792CJtg';

		$content = $client->readGDriveFile($this->my->id, $docId);

		if ($content === false) {
			// failed retrieve the content from google.
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_IMPORT_CONTENT'));
		}

		$post = $client->importContent($postId, $title, $importStyle, $content);

		if ($post === false) {
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_SAVING_CONTENT'));
		}

		// all good. lets return the post edit url
		$link = $post->getEditLink(false);

		return $this->ajax->resolve($link);
	}

	/**
	 * Display dialog box to allow user to determine the import style.
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function confirmImportStyle()
	{
		// Check for token
		FH::checkToken();
		EB::requireLogin();

		$theme = EB::themes();
		$output = $theme->output('site/googleimport/dialogs/importstyle');
		return $this->ajax->resolve($output);
	}

	/**
	 * Display dialog box to confirm if user want to revoke their google access or not.
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function confirmRevoke()
	{
		// Check for token
		FH::checkToken();
		EB::requireLogin();

		$theme = EB::themes();
		$output = $theme->output('site/googleimport/dialogs/revoke');
		return $this->ajax->resolve($output);
	}

	/**
	 * To load more google docs items
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function loadmore()
	{
		FH::checkToken();
		EB::requireLogin();

		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);

		// check if google import enabled or not.
		if (!$client->isEnabled()) {
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_FEATURE_DISABLED'));
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
		// 	$obj->title = 'This is a loadmore document No. ' . $i;

		// 	$data[$i] = $obj;
		// }

		// $tmpIds = array_rand($data, 10);
		// foreach ($tmpIds as $idx) {
		// 	$files[] = $data[$idx];
		// }

		// $nextPage = 'dfafafdasfasfasf';
		// debug end


		$themes = EB::themes();
		$themes->set('files', $files);
		$themes->set('nextPage', $nextPage);

		$output = $themes->output('site/googleimport/default/default.filelist');

		return $this->ajax->resolve($output, $nextPage);
	}

	/**
	 * To load more google docs items
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function search()
	{
		FH::checkToken();
		EB::requireLogin();

		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);

		// check if google import enabled or not.
		if (!$client->isEnabled()) {
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_FEATURE_DISABLED'));
		}

		$search = $this->input->get('search', '', 'default');
		$nextPageToken = '';//$this->input->get('nextPageToken', '', 'default');

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
		// 	$obj->title = 'This is a search document No. ' . $i;

		// 	$data[$i] = $obj;
		// }

		// $tmpIds = array_rand($data, 10);
		// foreach ($tmpIds as $idx) {
		// 	$files[] = $data[$idx];
		// }

		// $nextPage = 'dfafafdasfasfasf';
		// debug end

		$themes = EB::themes();
		$themes->set('files', $files);
		$themes->set('nextPage', $nextPage);

		$output = $themes->output('site/googleimport/default/default.filelist');

		$empty = $files ? '' : JText::_('COM_EB_GOOGLEIMPORT_SEARCH_NOT_FOUND');

		return $this->ajax->resolve($output, $nextPage, $empty);
	}

	/**
	 * To load more google docs items
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getUserInfo()
	{
		EB::checkToken();
		EB::requireLogin();

		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);

		// check if google import enabled or not.
		if (!$client->isEnabled()) {
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_FEATURE_DISABLED'));
		}

		$userInfo = $client->getUserDetails($this->my->id);

		if (!$userInfo) {
			return $this->ajax->reject();
		}

		$name = $userInfo->name;
		$picture = $userInfo->picture ? $userInfo->picture : '';

		// debug data
		// $name = 'User Two';
		// $picture = 'https://lh3.googleusercontent.com/a-/AOh14Ghr4bB_zz-bxWCxvrZ90eNK4Jduv0F_BVrIkV1I=s96-c';

		$themes = EB::themes();
		$themes->set('name', $name);
		$themes->set('pictureUrl', $picture);
		$output = $themes->output('site/googleimport/composer/default.userinfo');

		return $this->ajax->resolve($output);
	}

	/**
	 * To load more google docs items
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function revoke()
	{
		FH::checkToken();
		EB::requireLogin();

		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);

		// check if google import enabled or not.
		if (!$client->isEnabled()) {
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_FEATURE_DISABLED'));
		}

		$state = $client->revoke($this->my->id);

		if (!$state) {
			return $this->ajax->reject(JText::_('COM_EB_GOOGLEIMPORT_ERROR_REVOKE_FAILED'));
		}

		return $this->ajax->resolve($state);
	}
}
