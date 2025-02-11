<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewAuth extends EasyBlogView
{
	public function display($tpl = null)
	{
		// Get type
		$type = $this->input->get('type', '', 'default');

		$method = $type . 'Authorize';

		return $this->$method();
	}

	/**
	 * Authorize linkedin oauth
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	public function linkedinAuthorize()
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

			$client = EB::oauth()->getClient('LinkedIn');

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

				if ($state) {
					// now everything is set. lets migrate the data in oauth_posts with this new oauth record.
					$table->restoreBackup();
				}

			}
		}

		EB::info()->set($msg, $msgState);

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=autoposting', false);
		return $this->app->redirect($return);
	}
}
