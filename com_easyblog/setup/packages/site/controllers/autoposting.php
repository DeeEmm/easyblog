<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerAutoPosting extends EasyBlogController
{
	/**
	 * Saves the autoposting settings
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function save()
	{
		FH::checkToken();
		EB::requireLogin();

		// Get the post data here
		$post = $this->input->getArray('post');

		$user = EB::user($this->my->id);

		// Bind oauth settings
		$user->bindOauth($post, $this->acl);

		// Prepare the redirection url
		$redirect = EB::_('index.php?option=com_easyblog&view=dashboard&layout=autoposting', false);

		EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_AUTOPOSTING_UPDATE_SUCCESS'), 'success');
		return $this->app->redirect($redirect);
	}
}
