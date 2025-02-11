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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerTwitter extends EasyBlogController
{
	/**
	 * Retrieves the authorization url and redirect accordingly
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function twitterAuthorize()
	{
		// Get the client
		$client = EB::oauth()->getClient('Twitter');

		// Get the redirection url
		$token = $client->getRequestToken();
		$url = $client->getAuthorizeURL($token->token);

		// Determines if this is for the centralized section
		$system = $this->input->get('system', false, 'bool');

		// Get the user id
		$userId = $this->input->get('userId', null, 'default');

		if (is_null($userId)) {
			$userId = $this->my->id;
		}

		// Create a new table for record
		$oauth = EB::table('OAuth');

		if ($system) {
			$exists = $oauth->load(array('type' => 'twitter', 'system' => true));
		} else {
			$oauth->load(array('type' => 'twitter', 'user_id' => $userId, 'system' => false));
		}

		$oauth->type = 'twitter';
		$oauth->user_id = $userId;

		if ($system) {
			$oauth->system = true;
		}

		// Store the request tokens here
		$oauth->request_token = json_encode($token);
		$oauth->store();

		$this->app->redirect($url);
	}

	/**
	 * Method to process redirections from google
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function grant()
	{
		// Get the twitter client
		$client = EB::oauth()->getClient('Twitter');

		// Determines if this is a system request
		$system = $this->input->get('system', false, 'bool');

		// Now we need to load up the stored request tokens
		$table = EB::table('OAuth');
		$options = array('type' => 'twitter');

		if ($system) {
			$options['system'] = 1;
		} else {
			$options['user_id'] = $this->input->get('userId', null, 'default');
			$options['system'] = false;

			if (is_null($options['user_id'])) {
				$options['user_id'] = $this->my->id;
			}
		}

		$table->load($options);

		// Get the request tokens
		$requestTokens = json_decode($table->request_token);

		// Get the access token now
		$client->setRequestToken($requestTokens->token, $requestTokens->secret);

		// Get the access tokens now
		$result = $client->getAccess();

		$accessToken = new stdClass();
		$accessToken->token = $result->token;
		$accessToken->secret = $result->secret;

		// Set the access token now
		$table->access_token = json_encode($accessToken);

		// Set the params
		$table->params = $result->params;

		// Save it now
		$state = $table->store();

		// now everything is set. lets migrate the data in oauth_posts with this new oauth record.
		if ($state) {
			$table->restoreBackup();
		}

		// Since the page that is redirected to here is a popup, we need to close the window
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOSTING_TWITTER_AUTHORIZED_SUCCESS'), 'success');

		$redirect = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=twitter';
		return $this->app->redirect($redirect);
	}

	/**
	 * Revokes the access
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function revoke()
	{
		// Check for acl rules.
		$this->checkAccess('autoposting');

		// Determines if this request is a system request
		$system = $this->input->get('system', false, 'bool');

		// Determines if this request is to revoke a user's access
		$userId = $this->input->get('userId', null, 'default');

		$table = EB::table('OAuth');

		if ($system) {
			$table->load(array('type' => 'twitter', 'system' => true));
		} else {
			$table->load(array('type' => 'twitter', 'user_id' => $userId, 'system' => false));
		}

		// Get the client
		$client = EB::oauth()->getClient('Twitter');
		$client->setAccess($table->access_token);

		// Revoke the access
		$state	= $client->revoke();

		if ($state) {
			$table->addBackup();
		}

		// Regardless of the state, delete the record.
		$table->delete();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_AUTOPOSTING_TWITTER_REVOKED', 'autoposting', array(
			'link' => 'index.php?option=com_easyblog&view=autoposting&layout=twitter'
		));

		// If there's a problem revoking the app, just delete the record and let the user know
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_SUCCESS_REVOKING_ACCESS'), 'success');

		$redirect = 'index.php?option=com_easyblog&view=autoposting&layout=twitter';
		$this->app->redirect($redirect);
	}

	/**
	 * Saves the google auto posting settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the client id
		$post 	= $this->input->getArray('post');

		unset($post['task']);
		unset($post['option']);
		unset($post[EB::token()]);

		// Get the model so that we can store the settings
		$model 	= EB::model('Settings');
		$model->save($post);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_AUTOPOSTING_TWITTER_UPDATED', 'autoposting', array(
			'link' => 'index.php?option=com_easyblog&view=autoposting&layout=twitter'
		));

		// Redirect the user
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOSTING_TWITTER_SAVE_SUCCESS'), 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=autoposting&layout=twitter');
	}
}
