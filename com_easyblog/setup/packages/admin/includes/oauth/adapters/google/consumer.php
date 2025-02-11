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

EB::autoload();

class GoogleOauth
{

	public $callback = '';
	public $_access_token = '';

	private $uid = '';

	private $clientId = '';
	private $clientSecret = '';

	private $type = 'google';


	/**
	 * Exchanges the verifier code with the access token.
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function __construct($clientId, $clientSecret, $callback)
	{
		$this->input = JFactory::getApplication()->input;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->callback	= $callback;
	}

	/**
	 * Exchanges the verifier code with the access token.
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getAccessToken($verifier = '')
	{
		$client = $this->getClient();

		// authenticate code from google.
		$code = $this->input->get('code', '', 'default');

		if ($code) {
			$token = $client->fetchAccessTokenWithAuthCode($code);

			// $client->setAccessToken($token['access_token']);
			$client->setAccessToken($token);
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


	public function getRequestToken()
	{
		$client = $this->getClient();
		$token = $client->getAccessToken();

		return $token;
	}

	/**
	* Get the authorize URL
	*
	* @returns a string
	*/
	public function getAuthorizeURL($token='')
	{
		$client = $this->getClient();

		$url = $client->createAuthUrl();
		return $url;
	}

	/**
	 * Retrieves the client library
	 *
	 * @since	3.3.0
	 * @access	public
	 */
	public function getClient()
	{
		$client = new Google_Client();
		$client->setClientId($this->clientId);
		$client->setClientSecret($this->clientSecret);

		// set the scope here:
		$client->addScope([Google\Service\Oauth2::USERINFO_PROFILE, Google\Service\Drive::DRIVE_READONLY]);

		// set access_type:
		$client->setAccessType('offline');

		// set this to force to return with a refresh token.
		$client->setApprovalPrompt('force');

		// We use non sef for the redirection
		$redirect = rtrim(JURI::base(), '/') . '/index.php?option=com_easyblog&task=oauth.grant&client=google';
		$client->setRedirectUri($redirect);

		// Do not display any authentication or consent screens. 
		// $client->setPrompt('none');

		return $client;
	}

	/**
	 * Retrieves google signed in user name
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getUserInfo($token)
	{
		$client = $this->getClient();
		$client->setAccessToken($token);

		if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
			$token = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			$client->setAccessToken($token);
		}

		$oauth2 = new Google_Service_Oauth2($client);
		$userInfo = $oauth2->userinfo->get();

		$obj = new stdClass();
		$obj->name = $userInfo->name;
		$obj->picture = ($userInfo->picture) ? $userInfo->picture : '';

		return $obj;
	}

	/**
	 * Revoke google access token
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function revokeAccessToken($token)
	{
		$client = $this->getClient();
		$client->setAccessToken($token);

		if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
			$token = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			$client->setAccessToken($token);
		}

		$state = $client->revokeToken();
		return $state;
	}

	/**
	 * Retrieves file lists from gdrive
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getDriveFiles($token, $pageToken = '', $search = '')
	{
		$client = $this->getClient();
		$client->setAccessToken($token);

		if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
			$token = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			$client->setAccessToken($token);
		}

		$service = new Google_Service_Drive($client);
		$optParams = [
		  "pageSize" => EB_GOOGLEIMPORT_ITEMS_LIMIT,
		  "fields" => "nextPageToken, files(id, name)",
		  // "q" => "name contains 'stackideas' and (mimeType = 'application/vnd.google-apps.document')",
		  // "q" => "(mimeType = 'application/vnd.google-apps.document')",
		  "orderBy" => "modifiedTime desc",
		  "supportsAllDrives" => true
		];

		if ($pageToken) {
			$optParams['pageToken'] = $pageToken;
		}

		$condition = [];
		if ($search) {
			$condition[] = "name contains '" . EB::string()->escape($search) . "'";

		}
		$condition[] = "(mimeType = 'application/vnd.google-apps.document')";

		$optParams["q"] = implode(' and ', $condition);

		// "kind": "drive#file",
		// "id": "1lqPkA0uaTMNpp3PNEjQTZNrORehQV4Rzi4YOayiEU6c",
		// "name": "JungleWurld.com - Easy Social Customization Work - 2021 (EDITS V1)",
		// "mimeType": "application/vnd.google-apps.document"

		$results = $service->files->listFiles($optParams);
		$pageToken = $results->getNextPageToken();

		$files = [];
		if (count($results->getFiles()) > 0) {
			foreach ($results->getFiles() as $file) {
				// $files[] = $file;

				$obj = new stdClass();
				$obj->id = $file->getId();
				$obj->title = $file->getName();

				$files[] = $obj;
				
			}
		}

		$obj = new stdClass();

		$obj->files = $files;
		$obj->nextPageToken = $pageToken;

		return $obj;
	}

	/**
	 * read the file content from gdrive
	 *
	 * @since	3.3.0
	 * @access	public
	 */
	public function readDriveFile($token, $docId)
	{
		$client = $this->getClient();
		$client->setAccessToken($token);

		if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
			$token = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			$client->setAccessToken($token);
		}

		// now we need to know how to download the content.
		// test document ID: 1x5tXL6E7KId0C-xP-TqQjEEHxPf1nnKnZgJSwWtyXNE


		// $service = new Google_Service_Docs($client);
		// $doc = $service->documents->get($docId);
		// $content = $doc->getBody();


		$service = new Google_Service_Drive($client);
		// $file = $service->files->get($docId);

		// as html
		$response = $service->files->export(
			$docId,
			'text/html'
		);

		// as pdf
		// $response = $service->files->export(
		// 	$docId,
		// 	'application/pdf'
		// );

		// as docx
		// $response = $service->files->export(
		// 	$docId,
		// 	'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		// );


		$content = false;

		if ($response->getStatusCode() === 200) {
			$content = (string) $response->getBody();

			// lets try to extract the text using readability lib.
			// $content = $this->getFullContents($content);

			if (!$content) {
				// if empty content, just return false.
				$content = false;
			}
		}

		// debug: save it into a file.
		// $filePath = JPATH_ROOT . '/tmp/aaa_' . EB::date()->toUnix() . '.html';
		// $filePath = JPATH_ROOT . '/tmp/aaa_' . EB::date()->toUnix() . '.pdf';
		// $filePath = JPATH_ROOT . '/tmp/aaa_' . EB::date()->toUnix() . '.docx';
		// JFile::write($filePath, $content);


		// debug
		// $tmpContent = '<html><body><p>hello this is a test.</p></body></html>';
		// JFile::write($filePath, $tmpContent);

		return $content;
	}
}
