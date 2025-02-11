<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/consumer.php');

class EasyBlogClientGoogle extends GoogleOauth
{
	public $callback = '';
	public $_access_token = '';
	public $app = null;
	public $input = null;
	private $param = '';

	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->input = EB::request();

		$config = EB::config();
		$key = $config->get('integrations_googledoc_api_key');
		$secret = $config->get('integrations_googledoc_secret_key');

		parent::__construct($key, $secret, '');
	}

	public function isEnabled()
	{
		$config = EB::config();
		$key = $config->get('integrations_googledoc_api_key', '');
		$secret = $config->get('integrations_googledoc_secret_key', '');

		if ($config->get('integrations_googledoc', false) && $key && $secret) {
			return true;
		}

		return false;
	}

	public function setCallback($callback)
	{
		$this->callback = $callback;
	}

	public function getLoginUrl()
	{
		$returnUrl = $this->getReturnUrl();
		$redirect = base64_encode($returnUrl);

		$loginUrl = rtrim(JURI::base(), '/') . '/index.php?option=com_easyblog&task=oauth.request&client=' . EBLOG_OAUTH_GOOGLE . '&tmpl=component&redirect=' . $redirect;

		return $loginUrl;
	}

	public function getReturnUrl()
	{
		$returnUrl = rtrim(JURI::base(), '/') . '/index.php?option=com_easyblog&view=composer&layout=googleFileList&tmpl=component';
		return $returnUrl;
	}

	/**
	 * Method to get the google authorication url
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getAuthorizationURL($token)
	{
		$url = parent::getAuthorizeURL();

		return $url;
	}

	/**
	 * Method to get the updated access token
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function updateToken($token) {

		// get google api client
		$client = parent::getClient();

		$client->setAccessToken($token);

		if ($client->isAccessTokenExpired()) {
			if ($client->getRefreshToken()) {
				$token = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			}
		}

		$obj = new stdClass();
		$obj->access_token = $token['access_token'];
		$obj->refresh_token = $token['refresh_token'];
		$obj->token_type = $token['token_type'];
		$obj->scope = $token['scope'];
		$obj->created = $token['created'];
		$obj->expires_in = $token['expires_in'];

		$date = EB::date($token['created'] + $token['expires_in']);
		$obj->expires = $date->toSql();

		return $obj;
	}

	/**
	 * Method to get the list of documents from gdrive
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getUserDetails($userId)
	{
		$data = false;
		$token = $this->getOauthAccessToken($userId);

		if ($token) {
			$accessToken = EB::makeArray($token);
			$data = parent::getUserInfo($accessToken);
		}

		return $data;
	}

	/**
	 * Method to revoke user's access token
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function revoke($userId)
	{
		$state = false;
		$token = $this->getOauthAccessToken($userId);

		if ($token) {
			$accessToken = EB::makeArray($token);
			$state = parent::revokeAccessToken($accessToken);
		}

		if ($state) {
			// remove local access token
			$this->removeOauthAccessToken($userId);
		}

		return $state;
	}

	/**
	 * Method to get the list of documents from gdrive
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getGDriveFiles($userId, $pageToken = '', $search = '')
	{
		$files = [];
		$token = $this->getOauthAccessToken($userId);

		if ($token) {
			$accessToken = EB::makeArray($token);
			$files = parent::getDriveFiles($accessToken, $pageToken, $search);
		}

		return $files;
	}

	/**
	 * Method to get the read a file from gdrive
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function readGDriveFile($userId, $docId)
	{
		$content = false;
		$token = $this->getOauthAccessToken($userId);

		// debug 
		// $token = 'sdfaafasfasf';

		if ($token) {
			$accessToken = EB::makeArray($token);
			$content = parent::readDriveFile($accessToken, $docId);

			// debug
			// read the sample html file imported form google
			// $filePath = JPATH_ROOT . '/tmp/for_googleimport_testing.html';
			// $content = JFile::read($filePath);
		}

		return $content;
	}

	/**
	 * Method to get the updated access token
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getOauthAccessToken($userId)
	{
		$table = EB::table('Oauth');
		$table->load(array('user_id' => $userId, 'type' => EBLOG_OAUTH_GOOGLE));

		$token = $table->access_token;

		if ($token) {
			$accessToken = json_decode($token);

			// make sure the tokan as value
			if ($accessToken->access_token) {
				return $accessToken;
			}
		}

		return false;
	}

	/**
	 * Method to get the delete local access token
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function removeOauthAccessToken($userId)
	{
		$table = EB::table('Oauth');
		$table->load(array('user_id' => $userId, 'type' => EBLOG_OAUTH_GOOGLE));

		if ($table->id) {
			$table->delete();
		}

		return true;
	}

	/**
	 * Method to retrieve helper class
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getHelper()
	{
		$file = __DIR__ . '/helper.php';
		require_once($file);

		$className = 'EasyBlogClientGoogleHelper';
		$obj = new $className();

		return $obj;
	}

	/**
	 * Method to import the content as blog post
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function importContent($postId, $title, $importStyle, $content)
	{
		// $stylingMethod = 'inline'; // inline (with styling) : plain (no styling)
		// $stylingMethod = 'plain'; // inline (with styling) : plain (no styling)

		$stylingMethod = $importStyle ? $importStyle : 'inline';
		if (!in_array($importStyle, ['inline', 'plain'])) {
			$stylingMethod = 'inline';
		}

		// lets load the current post id as draft post
		$post = EB::post($postId);

		$my = EB::user();
		$catModel = EB::model('Category');
		$helper = $this->getHelper();

		// the imported post will be imported as legacy post.
		$post->doctype = 'legacy';

		// Set the category
		$post->category_id = $catModel->getDefaultCategoryId();

		// Cheap fix
		$post->categories = array($post->category_id);

		// Set the author
		$post->created_by = $my->id;

		// The blog post should always be site wide
		$post->source_id = 0;
		$post->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;

		// Set the blog post's language
		$post->language = '*';

		// get current datetime;
		$dateTime = EB::date()->toSql();

		// Set the creation date to the current date
		$post->created = $dateTime;
		$post->modified = $dateTime;
		$post->publish_up = $dateTime;

		// Determines if the blog should be new
		// since this is a draft post, we should always set it to true
		$post->isnew = true;

		// always save as draft
		$post->published = EASYBLOG_POST_DRAFT;

		// Bind the title
		$post->title = $title;
		$post->intro = $helper->processContent($post, $content, $stylingMethod);


		$error = false;

		try {

			$saveOptions = array(
							'validateData' => false,
							'checkAcl' => false
							);

			$post->save($saveOptions);

		} catch(EasyBlogException $exception) {
			// do nothing.
			$error = true;
		}

		return $error ? false : $post;
	}
}
