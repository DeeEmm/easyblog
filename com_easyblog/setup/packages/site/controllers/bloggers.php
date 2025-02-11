<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerBloggers extends EasyBlogController
{
	/**
	 * Search within EasyBlog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function suggest()
	{
		// Check for request forgeries
		FH::checkToken();

		$exclusion = $this->input->get('exclusion', '', 'default');

		$model = EB::model('Bloggers');
		$result = $model->getUsers(true, $exclusion);

		// If there's nothing, just return the empty object.
		if (!$result) {
			return $this->ajax->resolve(array());
		}

		$items = array();

		// Determines if we should use a specific input name
		$inputName = $this->input->get('inputName', '', 'default');

		foreach ($result as $user) {
			$user = EB::user($user);

			$template = EB::themes();
			$template->set('user', $user);
			$template->set('inputName', $inputName);

			$items[] = $template->output('site/authors/suggest/item');
		}

		return $this->ajax->resolve($items);
	}

	/**
	 * Retrieves the dashboard contents.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function downloadGDPR()
	{
		// Check for request forgeries
		FH::checkToken();

		$userId = $this->input->get('userId', 0, 'int');

		// Verify the request user is the same as loggin user.
		if ($this->my->id != $userId) {
			$theme = EB::themes();
			$theme->set('title', JText::_('COM_EASYBLOG_WARNING'));
			$theme->set('message', JText::_('COM_EB_GDPR_REQUEST_USER_NOT_SAME'));
			$output = $theme->output('site/dashboard/account/dialogs/gdpr.progress');
			
			return $this->ajax->resolve($output);
		}

		$table = EB::table('download');
		$table->load(array('userid' => $userId));

		if ($table->exists()) {
			$theme = EB::themes();
			$theme->set('title', JText::_('COM_EB_GDPR_REQUEST_BEING_PROCESS'));
			$theme->set('message', JText::_('COM_EB_GDPR_GATHERING_INFORMATION'));
			$output = $theme->output('site/dashboard/account/dialogs/gdpr.progress');
			
			return $this->ajax->resolve($output);
		}

		$params = array();

		$table->userid = $userId;
		$table->state = EASYBLOG_DOWNLOAD_REQ_NEW;
		$table->params = json_encode($params);
		$table->created = EB::date()->toSql();

		$table->store();

		$theme = EB::themes();
		$theme->set('title', JText::_('COM_EB_GDPR_REQUEST_BEING_PROCESS'));
		$theme->set('message', JText::_('COM_EB_GDPR_GATHERING_INFORMATION'));
		$output = $theme->output('site/dashboard/account/dialogs/gdpr.progress');
		
		return $this->ajax->resolve($output);
	}
}
