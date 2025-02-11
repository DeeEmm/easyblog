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

class EasyBlogControllerPending extends EasyBlogController
{
	/**
	 * Removes a pending blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl
		$this->checkAccess('pending');

		// Get the list of ids to be deleted
		$ids = $this->input->get('ids', array(), 'array');

		if (!$ids) {
			// Do something
			exit;
		}

		foreach ($ids as $id) {
			$post = EB::post($id);
			$post->deletePending();
		}

		$this->info->set('COM_EASYBLOG_PENDING_POSTS_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs&layout=pending');
	}

	/**
	 * Rejects a post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function reject()
	{
		FH::checkToken();
		$this->checkAccess('pending');

		// Get a list of ids
		$ids = $this->input->get('ids', [], 'array');
		$message = $this->input->get('message', '', 'default');

		foreach ($ids as $id) {

			// Since the id consists of the post id and the uid, we cannot typecast it to integer
			$post = EB::post($id);
			$post->reject($message);
		}

		$message = JText::_('COM_EASYBLOG_BLOGS_BLOG_SAVE_REJECTED');

		$this->info->set($message, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs&layout=pending');
	}

	/**
	 * Approves a blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function approve()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl
		$this->checkAccess('pending');

		// Get a list of id's to approve
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			// Since the id consists of the post id and the uid, we cannot typecast it to integer
			$post = EB::post($id);
			$post->approve();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_BLOG_SAVE_APPROVED', 'success');
		$this->app->redirect('index.php?option=com_easyblog&view=blogs&layout=pending');
	}

}
