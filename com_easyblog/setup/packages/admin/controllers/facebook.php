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

class EasyBlogControllerFacebook extends EasyBlogController
{
	/**
	 * Retrieves the authorization url and redirect accordingly
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function facebookAuthorize()
	{
		// Get the client
		$client = EB::oauth()->getClient('Facebook');

		// Get the redirection url
		$url = $client->getAuthorizeURL();

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
			$exists = $oauth->load(array('type' => 'facebook', 'system' => true));
		} else {
			$oauth->load(array('type' => 'facebook', 'user_id' => $userId, 'system' => false));
		}

		$oauth->type = 'facebook';
		$oauth->user_id = $userId;

		if ($system) {
			$oauth->system = true;
		}

		// Save the new record
		$state = $oauth->store();

		// Redirect to facebook's permissions page
		return $this->app->redirect($url);
	}

	/**
	 * Method to process redirections from google
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function grant()
	{
		// Get the oauth client
		$client = EB::oauth()->getClient('Facebook');

		// Get the code that facebook provided
		$code = $this->input->get('code', '', 'default');

		// Determines if this is a system request
		$system = $this->input->get('system', false, 'bool');

		// Exchange the code for an access token
		$result = $client->exchangeToken($code);

		// Load the Facebook oauth table
		$oauth = EB::table('OAuth');

		if ($system) {
			$oauth->load(array('type' => 'facebook', 'system' => true));
		} else {
			$oauth->load(array('type' => 'facebook', 'user_id' => $this->my->id, 'system' => false));
		}

		$oauth->created = EB::date()->toSql();
		$oauth->expires = $result->expires;
		$oauth->access_token = json_encode($result);

		$state = $oauth->store();

		// now everything is set. lets migrate the data in oauth_posts with this new oauth record.
		if ($state) {
			$oauth->restoreBackup();
		}

		// Since the page that is redirected to here is a popup, we need to close the window
		$this->info->set(JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_AUTHORIZED_SUCCESS'), 'success');

		$redirect = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=facebook';
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
			$table->load(array('type' => 'facebook', 'system' => true));
		} else {
			$table->load(array('type' => 'facebook', 'user_id' => $userId, 'system' => false));
		}

		// Get the return url
		$return = $this->input->get('return', '', 'default');
		$return = base64_decode($return);

		// Get the client
		$client = EB::oauth()->getClient('Facebook');
		$client->setAccess($table->access_token);

		// Revoke the access
		$state = $client->revoke();

		if ($state) {
			$table->addBackup();
		}

		// Regardless of the state, delete the record.
		$table->delete();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_AUTOPOSTING_FACEBOOK_REVOKED', 'autoposting', array(
			'link' => 'index.php?option=com_easyblog&view=autoposting&layout=facebook'
		));

		// If there's a problem revoking the app, just delete the record and let the user know
		if ($state === false) {
			$this->info->set(JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_ERROR_REVOKING_ACCESS'), 'error');
		} else {
			$this->info->set(JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_SUCCESS_REVOKING_ACCESS'), 'success');
		}

		$redirect = 'index.php?option=com_easyblog&view=autoposting&layout=facebook';

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
		$post 	= $this->input->getArray('post');

		unset($post['task']);
		unset($post['option']);
		unset($post[EB::token()]);

		if (isset($post['integrations_facebook_page_id'])) {

			$pages = $post['integrations_facebook_page_id'];

			// We need to merge them to be comma separated values
			$post['integrations_facebook_page_id'] = implode(',', $pages);
		}

		if (isset($post['integrations_facebook_group_id'])) {

			$groups = $post['integrations_facebook_group_id'];

			// Merge the array into string values
			$post['integrations_facebook_group_id'] = implode(',', $groups);
		}

		// If doesn't select any scope permission then store it as empty
		if (!isset($post['integrations_facebook_scope_permissions'])) {
			$post['integrations_facebook_scope_permissions'] = '';
		}

		// Get the model so that we can store the settings
		$model = EB::model('Settings');
		$model->save($post);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_AUTOPOSTING_FACEBOOK_UPDATED', 'autoposting', array(
			'link' => 'index.php?option=com_easyblog&view=autoposting&layout=facebook'
		));

		// Redirect the user
		EB::info()->set(JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_SAVE_SUCCESS'), 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=autoposting&layout=facebook');
	}
}
