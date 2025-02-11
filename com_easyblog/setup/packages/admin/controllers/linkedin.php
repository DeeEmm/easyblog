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

class EasyBlogControllerLinkedIn extends EasyBlogController
{
	/**
	 * Retrieves the authorization url and redirect accordingly
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function linkedinAuthorize($triggerDefaultScope = false)
	{
		$client = EB::oauth()->getClient('LinkedIn', array('backend' => true));

		// Generate the authorize url
		$url = $client->getAuthorizeURL(null, $triggerDefaultScope);

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
		$code = $this->input->get('code', '', 'default');
		$system = $this->input->get('system', false, 'bool');
		$state = $this->input->get('state', '', 'default');
		$errorCode = $this->input->get('error', '', 'default');

		$msg = JText::sprintf('COM_EB_AUTOPOSTING_LINKEDIN_AUTHORIZED_FAILED', $errorCode);
		$msgState = 'error';

		// Stored the generated token code
		if ($code) {
			$msg = JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_AUTHORIZED_SUCCESS');
			$msgState = 'success';

			$client = EB::oauth()->getClient('LinkedIn', array('backend' => true));

			// Set the authorization code
			$client->setAuthCode($code);

			// Get the access token
			$result = $client->getAccess();

			$table = EB::table('OAuth');

			$userId = $client->getUserIdFromState($state);

			if (!$userId) {
				$userId = $this->my->id;
			}

			if ($system) {
				$table->load(array('type' => 'linkedin', 'system' => 1));

				if (!$table->id) {
					$table->type = 'linkedin';
					$table->user_id = $userId;
					$table->system = 1;
				}
			} else {
				$table->load(array('type' => 'linkedin', 'user_id' => $userId, 'system' => 0));

				if (!$table->id) {
					$table->type = 'linkedin';
					$table->user_id = $userId;
					$table->system = 0;
				}
			}

			if ($result) {
				$accessToken = new stdClass();
				$accessToken->token  = $result->token;
				$accessToken->secret = $result->secret;

				// Set the access token now
				$table->access_token = json_encode($accessToken);

				// Set the params
				$table->params = json_encode($result);
				$table->expires = $result->expires;

				$state = $table->store();

				// now everything is set. lets migrate the data in oauth_posts with this new oauth record.
				if ($state) {
					$table->restoreBackup();
				}
			}
		}

		EB::info()->set($msg, $msgState);

		$redirect = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=linkedin';
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

		$table 	= EB::table('OAuth');

		// Determines if this request is a system request
		$system = $this->input->get('system', false, 'bool');

		// Determines if this request is to revoke a user's access
		$userId = $this->input->get('userId', null, 'default');

		$table = EB::table('OAuth');

		if ($system) {
			$table->load(array('type' => 'linkedin', 'system' => true));
		} else {
			$table->load(array('type' => 'linkedin', 'user_id' => $userId, 'system' => false));
		}

		// Get the return url
		$return = $this->input->get('return', '', 'default');
		$return = base64_decode($return);

		// Get the client
		$client = EB::oauth()->getClient('Linkedin');
		$client->setAccess($table->access_token);

		// Revoke the access
		$state = $client->revoke();

		if ($state) {
			$table->addBackup();
		}

		// Regardless of the state, delete the record.
		$table->delete();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_AUTOPOSTING_LINKEDIN_REVOKED', 'autoposting', array(
			'link' => 'index.php?option=com_easyblog&view=autoposting&layout=linkedin'
		));

		// If there's a problem revoking the app, just delete the record and let the user know
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN_SUCCESS_REVOKING_ACCESS'), 'success');


		$redirect = 'index.php?option=com_easyblog&view=autoposting&layout=linkedin';

		if ($return) {
			$redirect = $return;
		}

		$this->app->redirect($redirect);
	}

	/**
	 * Saves the google auto posting settings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the client id
		$post = $this->input->getArray('post');

		unset($post['task']);
		unset($post['option']);
		unset($post[EB::token()]);

		// Linkedin default scope permissions
		$defaultScopes = ['r_liteprofile', 'r_emailaddress', 'w_member_social'];

		if (!isset($post['integrations_linkedin_scope_permissions'])) {
			$post['integrations_linkedin_scope_permissions'] = $defaultScopes;
		} else {
			$selectedScopes = $post['integrations_linkedin_scope_permissions'];
			$post['integrations_linkedin_scope_permissions'] = array_merge($defaultScopes, $selectedScopes);
		}

		// Get the model so that we can store the settings
		$model = EB::model('Settings');
		$model->save($post);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_AUTOPOSTING_LINKEDIN_UPDATED', 'autoposting', array(
			'link' => 'index.php?option=com_easyblog&view=autoposting&layout=linkedin'
		));

		// Redirect the user
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_SAVE_SUCCESS'), 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=autoposting&layout=linkedin');
	}
}
