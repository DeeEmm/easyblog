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

class EasyBlogControllerOAuth extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Requests a token from the respective oauth client
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function request()
	{
		// Ensure that the user needs to be logged in
		EB::requireLogin();

		// Get the client type
		$client = $this->input->get('client', '', 'cmd');

		// LinkedIn is now using different approach of authentication
		if ($client == 'linkedin') {
			return $this->requestLinkedin();
		}

		if ($client == EBLOG_OAUTH_GOOGLE) {
			return $this->requestGoogle();
		}

		// Default redirect url
		$url = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=autoposting', false);

		// Get the redirection url
		$redirect = $this->input->get('redirect', '', 'default');

		if ($redirect) {
			$redirect = '&redirect=' . $redirect;
		}

		// Oauth redirect URI
		$callback = JURI::root() . 'index.php?option=com_easyblog&task=oauth.grant&client=' . $client . $redirect;


		// Get the consumer
		$consumer = EB::oauth()->getClient($client);
		$consumer->setCallback($callback);

		// Generate a request token
		$request = $consumer->getRequestToken();

		// Ensure that we are getting the request tokens and secret
		if (!$request->token || !$request->secret) {
			$this->info->set(JText::_('COM_EASYBLOG_OAUTH_KEY_INVALID'), 'error');

			return $this->app->redirect($url, false);
		}

		$table = EB::table('OAuth');
		$table->user_id = $this->my->id;
		$table->type = $client;
		$table->created = EB::date()->toSql();
		$table->request_token = json_encode($request);

		// Store the tokens now
		$table->store();

		// Get the request permissions dialog url
		$url = $consumer->getAuthorizeURL($request->token, false, 'popup');

		return $this->app->redirect($url, false);
	}

	/**
	 * Authentication request for Google
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function requestGoogle()
	{
		EB::requireLogin();

		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);

		// debug data
		// echo '<script type="text/javascript">window.opener.doneLogin();window.close();</script>';
		// exit;
		// debug data

		// check if google import enabled or not.
		if (!$client->isEnabled()) {
			throw EB::exception('COM_EB_GOOGLEIMPORT_ERROR_FEATURE_DISABLED', 'error');
		}

		// default return url
		$returnUrl = $client->getReturnURL();

		$redirect = $this->input->get('redirect', '', 'default');
		if ($redirect) {
			$returnUrl = base64_decode($redirect);
		}

		// lets check if user already has the access token or not.
		$oauthAccessToken = $client->getOauthAccessToken($this->my->id);

		if ($oauthAccessToken) {

			$accessToken = EB::makeArray($oauthAccessToken);

			$obj = $client->updateToken($accessToken);

			if ($obj->access_token != $accessToken['access_token']) {
				// token renewed. lets store in db.

				$table = EB::table('Oauth');
				$table->load(array('user_id' => $this->my->id, 'type' => EBLOG_OAUTH_GOOGLE));

				$table->user_id = $this->my->id;
				$table->type = EBLOG_OAUTH_GOOGLE;
				$table->expires = $obj->expires;
				$table->access_token = json_encode($obj);

				// Store the tokens now
				$table->store();

			}

			echo '<script type="text/javascript">window.opener.doneLogin();window.close();</script>';
			exit;
		}

		$url = $client->getAuthorizeURL();
		return $this->app->redirect($url, false);
	}



	/**
	 * Authentication request for Google
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function grantGoogle()
	{
		EB::requireLogin();

		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);

		// check if google import enabled or not.
		if (!$client->isEnabled()) {
			throw EB::exception('COM_EB_GOOGLEIMPORT_ERROR_FEATURE_DISABLED', 'error');
		}

		$token = $client->getAccessToken();

		if ($token) {

			$table = EB::table('OAuth');
			$table->load(array('user_id' => $this->my->id, 'type' => EBLOG_OAUTH_GOOGLE));

			$table->user_id = $this->my->id;
			$table->type = EBLOG_OAUTH_GOOGLE;
			$table->created = EB::date()->toSql();
			$table->expires = $token->expires;
			$table->access_token = json_encode($token);

			// Store the tokens now
			$table->store();
		}

		echo '<script type="text/javascript">window.opener.doneLogin();window.close();</script>';
		exit;
	}

	/**
	 * Authentication request for LinkedIn
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	public function requestLinkedin()
	{
		$client = EB::oauth()->getClient('LinkedIn');
		$url = $client->getAuthorizeURL();

		return $this->app->redirect($url, false);
	}

	/**
	 * Responsible to receive the incoming redirection from the respective oauth sites
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function grant()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Default redirect url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=autoposting', false);

		$autopostClients = array('twitter', 'linkedin', 'facebook');

		// Get the client
		$client = $this->input->get('client', '', 'cmd');

		// this is to process google import authentication
		if ($client == 'google') {
			return $this->grantGoogle();
		}

		// Get the redirection url
		$redirect = $this->input->get('redirect', '', 'default');

		// Get the redirection url
		$redirectUri = !empty( $redirect ) ? '&redirect=' . $redirect : '';

		// Let's see if caller wants us to go to any specific location or not.
		if ($redirect) {
			$redirect = base64_decode($redirect);
		}

		// Load the oauth object
		$table = EB::table('OAuth');
		$table->loadByUser($this->my->id, $client);

		if (!$table->id) {
			$this->info->set('COM_EASYBLOG_OAUTH_UNABLE_TO_LOCATE_RECORD', 'error');

			return $this->app->redirect($return);
		}

		// Detect if there's any errors
		$denied = $this->input->get('denied', '', 'default');

		// When there's an error, delete the oauth data
		if ($denied) {
			$table->delete();

			$this->info->set('COM_EASYBLOG_OAUTH_DENIED_ERROR', 'error');
			return $this->app->redirect($return);
		}

		// Get the request token
		$request = json_decode($table->request_token);

		// Oauth redirect URI
		$callback = JURI::root() . 'index.php?option=com_easyblog&task=oauth.grant&client=' . $client . $redirect;

		// Get the client
		$consumer = EB::oauth()->getClient($client);
		$consumer->setCallback($callback);

		// Get the verifier
		$verifier = $consumer->getVerifier();

		if (!$verifier) {
			$table->delete();

			return $this->app->redirect($return);
		}

		// Get the access token
		$consumer->setRequestToken($request->token, $request->secret);
		$access = $consumer->getAccess($verifier);

		// Since there is a problem with the oauth authentication, we need to delete the existing record.
		if (!$access || !$access->token || !$access->secret) {

			$table->delete();

			$this->info->set('COM_EASYBLOG_OAUTH_ACCESS_TOKEN_ERROR', 'error');

			return $this->app->redirect($return);
		}

		// Once we have the token, we need to map it back
		$params = EB::registry();
		$params->set('token', $access->token);
		$params->set('secret', $access->secret);

		// Set the expiration date
		if (isset($access->expires)) {
			$table->expires = $access->expires;
		}

		$table->access_token = $params->toString();
		$table->params = $access->params;

		// Store the oauth table now
		$state = $table->store();

		if ($state && in_array($client, $autopostClients)) {
			// now everything is set. lets migrate the data in oauth_posts with this new oauth record.
			$table->restoreBackup();
		}

		if ($client != 'flickr') {
			$this->info->set(JText::sprintf('COM_EASYBLOG_OAUTH_SUCCESS_' . strtoupper($client)), 'success');
		}

		return $this->app->redirect($return, false);
	}

	/**
	 * Revokes the user's oauth access
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function revoke()
	{
		// Require the user to be logged in
		EB::requireLogin();

		$autopostClients = array('twitter', 'linkedin', 'facebook');

		// The default url
		$url = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=autoposting', false);

		// Get any redirection url
		$redirect = $this->input->get('redirect', '', 'default');

		// Get the oauth client
		$type = $this->input->get('client', '', 'cmd');

		// If redirect url is provided, we know for sure where to redirect the user to
		if ($redirect) {
			$url = base64_decode($redirect);
		}

		// Load up the oauth object
		$table = EB::table('OAuth');
		$table->loadByUser($this->my->id, $type);

		// Load up the oauth client and set the access
		$client = EB::oauth()->getClient($type);
		$client->setAccess($table->access_token);

		// Get the callback url
		$callback = EBR::getRoutedURL('index.php?option=com_easyblog&task=oauth.grant&client=' . $type, false, true);

		// Get the consumer and secret key
		$key = $this->config->get('integrations_' . $type . '_api_key');
		$secret = $this->config->get('integrations_' . $type . '_secret_key');

		// Try to revoke the app
		$state = $client->revoke();

		if ($state && in_array($type, $autopostClients)) {
			$table->addBackup();
		}

		// After revoking the access, delete the oauth record
		$table->delete();

		$this->info->set(JText::_('COM_EASYBLOG_APPLICATION_REVOKED_SUCCESSFULLY'), 'success');
		return $this->app->redirect($url, false);
	}
}
