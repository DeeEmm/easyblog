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

class EasyBlogControllerPolls extends EasyBlogController
{
	public function __construct($options = [])
	{
		parent::__construct($options);

		$this->registerTask('apply', 'store');
		$this->registerTask('save', 'store');
		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Deletes the selected polls. This will be used on the backend and frontend.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function delete()
	{
		FH::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		$ids = $this->input->get('cid', [], 'array');

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=polls', false);

		if (EB::isFromAdmin()) {
			$return = 'index.php?option=com_easyblog&view=polls';
		}

		if (empty($ids)) {
			$this->info->set('No ids provided', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$poll = EB::polls($id);

			if (!$poll->id || !$poll->canDelete()) {
				continue;
			}

			$poll->delete();
		}

		$this->info->set(JText::_('COM_EB_POLL_DELETE_SUCCESS'), 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Perform publish/unpublish action on the selected polls. This will be used on the backend and frontend.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function togglePublish()
	{
		FH::checkToken();

		$ids = $this->input->get('cid', [], 'array');

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=polls', false);

		if (EB::isFromAdmin()) {
			$return = 'index.php?option=com_easyblog&view=polls';
		}

		if (empty($ids)) {
			$this->info->set('No ids provided', 'error');

			return $this->app->redirect($return);
		}

		$task = $this->getTask();

		foreach ($ids as $id) {
			$poll = EB::table('Polls');
			$poll->load($id);

			if (!$poll->id) {
				continue;
			}

			$state = $task == 'publish' ? EB_PUBLISHED : EB_UNPUBLISHED;
			$poll->setState($state);

			// Save the poll
			$poll->store();
		}

		$message = JText::_('COM_EB_POLLS_PUBLISHED_SUCCESS');

		if ($task == 'unpublish') {
			$message = JText::_('COM_EB_POLLS_UNPUBLISHED_SUCCESS');
		}

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}
}
