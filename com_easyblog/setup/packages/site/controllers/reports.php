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

class EasyBlogControllerReports extends EasyBlogController
{
	/**
	 * Allows caller to submit a report
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function submit()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the composite keys
		$id = $this->input->get('id', 0, 'int');
		$type = $this->input->get('type', '', 'cmd');

		// Initialize redirection link
		$redirect = EB::_( 'index.php?option=com_easyblog&view=entry&id=' . $id, false);

		// Check if guest is allowed to report or not.
		if ($this->my->guest && !$this->config->get('main_reporting_guests')) {
			$this->info->set('COM_EASYBLOG_CATEGORIES_FOR_REGISTERED_USERS_ONLY', 'error');

			return $this->app->redirect($redirect);
		}

		// Ensure that the report reason is not empty.
		$reason = $this->input->get('reason', '', 'default');

		if (!$reason) {
			EB::info()->set(JText::_('COM_EASYBLOG_REPORT_PLEASE_SPECIFY_REASON'), 'error');

			return $this->app->redirect($redirect);
		}

		$report = EB::table('Report');
		$report->obj_id = $id;
		$report->obj_type = $type;
		$report->reason = $reason;
		$report->created = EB::date()->toSql();
		$report->created_by = $this->my->id;
		$report->ip = @$_SERVER['REMOTE_ADDR'];

		$state = $report->store();

		if (!$state) {
			$this->info->set($report->getError());

			return $this->app->redirect($redirect);
		}

		// Notify the site admin when there's a new report made
		$post = EB::post($id);

		$report->notify($post);
		
		$message = JText::_('COM_EASYBLOG_THANKS_FOR_REPORTING');
		
		$this->info->set($message, 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Trash blog posts
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function trash()
	{
		// Check for tokens
		FH::checkToken();

		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		if (!$ids) {
			$ids = $this->input->get('id', 0, 'int');

			if ($ids) {
				$ids = array($ids);
			}
		}

		if (!$ids) {
			throw EB::exception('Invalid id provided', 500);
		}

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=reports', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$report = EB::table('Report');
			$report->load($id);

			$post = EB::post($report->obj_id);

			if (!$post->canDelete()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_BLOG'), 'error');
				return $this->app->redirect($return);
			}

			$post->trash();

			// Once the blog post is unpublished, delete the report since action was already performed.
			$report->delete();
		}

		EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_TRASH_SUCCESS'), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Unpublish blog posts
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function unpublish()
	{
		// Check for tokens
		FH::checkToken();

		// Build the return url
		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=reports', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Ensure that the user has access to publish items
		if ($this->my->guest) {
			throw EB::exception('No permissions to unpublish blog posts', 500);
		}

		// Get id's
		$ids = $this->input->get('ids', '', 'array');

		// Get the blogs model
		$model = EB::model('Blogs');

		foreach ($ids as $id) {
			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$report = EB::table('Report');
			$report->load($id);

			$post = EB::post((int) $report->obj_id);
			$post->unpublish();

			// Once the blog post is unpublished, delete the report since action was already performed.
			$report->delete();
		}

		$message = JText::_('COM_EASYBLOG_POSTS_UNPUBLISHED_SUCCESS');

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Discards report data
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function discard()
	{
		FH::checkToken();

		$return = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=reports', false);

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Ensure that the user has access to publish items
		if ($this->my->guest) {
			throw EB::exception('No permissions to discard report post', 500);
		}

		// Get id's
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$report = EB::table('Report');
			$report->load((int) $id);

			$report->delete();
		}

		$message = JText::_('COM_EB_REPORTS_DISCARDED_SUCCESSFULLY');

		$this->info->set($message, 'success');

		return $this->app->redirect($return);				
	}	
}
