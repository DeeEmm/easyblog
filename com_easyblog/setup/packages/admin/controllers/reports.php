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

class EasyBlogControllerReports extends EasyBlogController
{
	/**
	 * Deletes a reported blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the report id
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			return;
		}

		foreach ($ids as $id) {

			$id = (int) $id;

			$report = EB::table('Report');
			$report->load($id);

			$post = EB::post($report->obj_id);
			$post->delete();

			// Once the blog post is unpublished, delete the report since action was already performed.
			$report->delete();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=reports');
	}

	/**
	 * Unpublishes a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unpublish()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the report id
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			return;
		}

		// Get the blogs model
		$model = EB::model('Blogs');

		foreach ($ids as $id) {

			$id = (int) $id;

			$report = EB::table('Report');
			$report->load($id);

			$post = EB::post((int) $report->obj_id);
			$post->unpublish();

			// Once the blog post is unpublished, delete the report since action was already performed.
			$report->delete();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_UNPUBLISHED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=reports');
	}

	/**
	 * Allow caller to discard reports
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function discard()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('report');

		// Get a list of report ids.
		$ids = $this->input->get('cid', array(), 'array');

		foreach ($ids as $id) {
			$report = EB::table('Report');
			$report->load((int) $id);

			$report->delete();
		}

		$message 	= JText::_('COM_EASYBLOG_REPORTS_DISCARDED_SUCCESSFULLY');
		$this->info->set($message, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=reports');
	}
}
