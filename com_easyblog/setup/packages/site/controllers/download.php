<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerDownload extends EasyBlogController
{
	/**
	 * Allow user to download zip file used in gdpr
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function gdpr()
	{
		// Check for request forgeries
		FH::checkToken();

		if (! $this->config->get('gdpr_enabled')) {
			return EB::exception(JText::_('COM_EB_GDPR_DOWNLOAD_DISABLED'), EASYBLOG_MSG_INFO);
		}

		// Get the composite keys
		$data = $this->input->get('id', '', 'BASE64');
		$password = $this->input->get('password', '', 'default');
		$return = $this->input->get('return', '', 'BASE64');

		$redirect = EB::_( 'index.php?option=com_easyblog&view=latest', false);

		if (!$data || !$password) {
			throw EB::exception(JText::_('COM_EB_INVALID_TOKEN_PROVIDED'), 404);
		}

		$keys = base64_decode($data);

		$key = explode('|', $keys);

		$id = $key[0];
		$userId = $key[1];
		$created = $key[2];

		$download = EB::table('download');
		$download->load($id);

		$user = JFactory::getUser($userId);

		if (!$user->id || !$download->id || $download->state != EASYBLOG_DOWNLOAD_REQ_READY || $download->userid != $userId || $download->created != $created) {
			throw EB::exception(JText::_('COM_EB_INVALID_TOKEN_PROVIDED'), 404);
		}

		// authenticate user
		$username = $user->username;

		// Populate the data array:
		$data = array();
		$data['username'] = $username;
		$data['password'] = $password;
		$data['secretkey'] = '';

		// Get the log in options.
		$options = array();
		$options['remember'] = false;
		$options['return'] = '';
		$options['silent'] = true;

		// Get the log in credentials.
		$credentials = array();
		$credentials['username']  = $data['username'];
		$credentials['password']  = $data['password'];
		$credentials['secretkey'] = $data['secretkey'];

		// perform user login here.
		$state = $this->app->login($credentials, $options);

		if ($state) {
			$download->showArchiveDownload();
		}

		$return = base64_decode($return);

		$this->info->set('COM_EB_GDPR_DOWNLOAD_VERIFY_FAILED', 'error');
		return $this->app->redirect($return);
	}

}
