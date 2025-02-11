<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerTemplates extends EasyBlogController
{
	/**
	 * Deletes a list of post templates
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		EB::loadLanguages();

		$isAjax = $this->doc->getType() == 'ajax';

		$ids = $this->input->get('ids', array(), 'array');

		$redirect = EB::_('index.php?option=com_easyblog&view=dashboard&layout=templates', false);

		if (!$ids) {
			$message = JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATES_INVALID_ID');

			if ($isAjax) {
				return $this->ajax->reject($message);
			} else {
				$this->info->set($message, 'error');
				return $this->app->redirect($redirect);
			}
		}

		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load((int) $id);

			// Do not delete blank template
			if ($template->isBlank()) {
				continue;
			}

			// Ensure that the user has access to delete this
			if ($template->canDelete()) {
				$template->delete();
			}
		}

		$message = JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATES_DELETED_SUCCESS');

		if ($this->doc->getType() != 'ajax') {
			$this->info->set($message, 'success');
			return $this->app->redirect($redirect);
		}

		// For ajax calls, we shouldn't do anything
		return $this->ajax->resolve($message);
	}

	/**
	 * Save post templates
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function save()
	{
		FH::checkToken();
		EB::requireLogin();

		// Ensure that the frontend language is loaded
		EB::loadLanguages();

		// Check for permission
		if (!$this->acl->get('create_post_templates') && !FH::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_BLOG_TEMPLATE_SAVE_ERROR'));
		}

		// We want to get the document data
		$document = $this->input->get('document', '', 'raw');
		$type = 'ebd';

		// Legacy templates
		if (!$document) {
			$document = $this->input->get('content', '', 'raw');
			$type = 'legacy';
		}

		$title = $this->input->get('template_title', '', 'default');

		// If there is a template id, we assume that the user want's to save the template
		$id = $this->input->get('template_id', 0, 'int');

		$lockAction = $this->input->get('lock', 0, 'int') ? 'lock' : 'unlock';

		if (!$title) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SAVE_TEMPLATE_TITLE_EMPTY'));
		}

		// We should not allow site admin to create a post template without a block
		// Because if the site admin creates and lock it, and the authors choose this locked post template
		// The default block will not respect it at all
		if ($type == 'ebd' && empty(json_decode($document)->blocks)) {
			return $this->ajax->reject(JText::_('COM_EB_SAVE_TEMPLATE_BLOCKS_EMPTY'));
		}

		$template = EB::table('PostTemplate');
		$template->load($id);

		$template->title = $title;
		$template->user_id = $template->id ? $template->user_id : $this->my->id;
		$template->created = EB::date()->toSql();
		$template->system = $this->input->get('system', false, 'bool');
		$template->data = $document;
		$template->doctype = $type;
		$template->screenshot = $template->screenshot ? $template->screenshot : '';

		$state = $template->store();

		if (!$state) {
			return $this->ajax->reject($template->getError());
		}

		// Determine if the site admin wants to lock the post template or not
		// If action is lock, means site admin wants to lock the post template
		// Else this means that site admin does not want to lock it OR want to unlock the previously saved locked post template
		$template->$lockAction();

		$message = 'COM_EASYBLOG_BLOG_TEMPLATE_SAVED_SUCCESS';

		if ($id) {
			$message = 'COM_EASYBLOG_BLOG_TEMPLATE_UPDATE_SUCCESS';
		}

		// Reload the template
		$template->load($template->id);

		return $this->ajax->resolve(JText::_($message), $template->id, $template->getEditLink(false));
	}

	/**
	 * Set publish state of post templates
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function publish()
	{
		EB::requireLogin();
		FH::checkToken();

		// Check for permission
		if (!$this->acl->get('create_post_templates') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_BLOG_TEMPLATE_SAVE_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load($id);

			// Ensure that the user has access to publish this template
			if ($template->canPublish()) {
				$template->published = 1;
				$template->store();
			}
		}

		$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_SUCCESSFULLY_PUBLISHED');

		$this->info->set($message, 'success');

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=templates', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Set unpublish state of post templates
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function unpublish()
	{
		EB::requireLogin();
		FH::checkToken();

		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load($id);

			if ($template->canPublish()) {
				$template->published = 0;
				$template->store();
			}
		}

		$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_SUCCESSFULLY_UNPUBLISHED');

		$this->info->set($message, 'success');

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=templates', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Duplicate selected post templates and save it as new template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function copy()
	{
		EB::requireLogin();
		FH::checkToken();

		// Check for permission
		if (!$this->acl->get('create_post_templates') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_BLOG_TEMPLATE_SAVE_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load($id);

			// Do not duplicate blank template
			if ($template->isBlank()) {
				continue;
			}

			$template->duplicate();
		}

		$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_SUCCESSFULLY_DUPLICATED');
		$this->info->set($message, 'success');

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=templates', false);

		return $this->app->redirect($redirect);
	}
}
