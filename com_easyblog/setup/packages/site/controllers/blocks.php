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

class EasyBlogControllerBlocks extends EasyBlogController
{
	/**
	 * Save a block template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function saveTemplate()
	{
		FH::checkToken();

		// Check for permission
		if (!FH::isSiteAdmin() && !$this->acl->get('create_block_templates')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$blocksLib = EB::blocks();

		$id = $this->input->get('id', 0, 'int');
		$block = $this->input->get('block', '', 'raw');
		$title = $this->input->get('title', '', 'default');
		$description = $this->input->get('description', '', 'default');
		$global = $this->input->get('global', '1', 'default');

		$table = EB::table('BlockTemplates');
		$table->load($id);

		$isUpdating = $id && $table->id ? true : false;

		if ($isUpdating && !$table->isOwner()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$table->user_id = EB::user()->id;
		$table->block_id = 0;
		$table->title = $title;
		$table->description = $description;
		$table->data = $block;
		$table->global = $global;
		$table->created = EB::date()->toSql();
		$table->published = 1;

		$table->store();

		// Extract block data
		$template = new stdClass();
		$template->id = $table->id;
		$template->title = $table->title;

		$blocks = json_decode($table->data);
		$html = '';

		foreach ($blocks as $block) {
			$html .= $blocksLib->renderEditableBlock($block, true, false, $template->id);
		}

		$template->block = $html;

		$theme = EB::themes();
		$theme->set('block', $template);
		$output = $theme->output('site/composer/blocks/templates');

		return $this->ajax->resolve(JText::_('COM_EB_BLOCK_TEMPLATE_SAVE_SUCCESS'), $output, $template, $isUpdating);
	}

	/**
	 * Method to update an existing template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function updateTemplate()
	{
		FH::checkToken();

		// Check for permission
		if (!$this->acl->get('create_block_templates') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');
			return $this->app->redirect($redirect);
		}

		$id = $this->input->get('id', 0, 'int');

		$template = EB::table('BlockTemplates');
		$template->load($id);

		if (!$template->isOwner()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');
			return $this->app->redirect($redirect);
		}

		$post = $this->input->post->getArray();

		$template->bind($post);

		// Save the template
		$template->store();

		$message = JText::_('COM_EB_BLOCK_TEMPLATES_UPDATED_SUCCESSFULLY');

		$this->info->set($message, 'success');

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplates', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Deletes a list of block templates
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function deleteTemplate()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for permission
		if (!FH::isSiteAdmin() && !$this->acl->get('create_block_templates')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$isAjax = $this->doc->getType() == 'ajax';

		$ids = $this->input->get('ids', array(), 'array');

		$redirect = EB::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplates', false);

		if (!$ids) {
			$message = JText::_('COM_EB_DASHBOARD_BLOCK_TEMPLATES_INVALID_ID');

			if ($isAjax) {
				return $this->ajax->reject($message);
			} else {
				$this->info->set($message, 'error');
				return $this->app->redirect($redirect);
			}
		}

		foreach ($ids as $id) {
			$template = EB::table('BlockTemplates');
			$template->load((int) $id);

			// Ensure that the user has access to delete this
			if ($template->canDelete()) {
				$template->delete();
			}
		}

		$message = JText::_('COM_EB_DASHBOARD_BLOCK_TEMPLATES_DELETED_SUCCESS');

		if ($this->doc->getType() != 'ajax') {
			$this->info->set($message, 'success');
			return $this->app->redirect($redirect);
		}

		// For ajax calls, we shouldn't do anything
		return $this->ajax->resolve($message);
	}

	/**
	 * Set publish state of block templates
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function publishTemplate()
	{
		EB::requireLogin();
		FH::checkToken();

		// Check for permission
		if (!$this->acl->get('create_block_templates') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');
			return $this->app->redirect($redirect);
		}

		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$template = EB::table('BlockTemplates');
			$template->load($id);

			// Ensure that the user has access to publish this template
			if ($template->canPublish()) {
				$template->published = 1;
				$template->store();
			}
		}

		$message = JText::_('COM_EB_BLOCK_TEMPLATES_SUCCESSFULLY_PUBLISHED');

		$this->info->set($message, 'success');

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplates', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Set unpublish state of block templates
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function unpublishTemplate()
	{
		EB::requireLogin();
		FH::checkToken();

		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$template = EB::table('BlockTemplates');
			$template->load($id);

			if ($template->canPublish()) {
				$template->published = 0;
				$template->store();
			}
		}

		$message = JText::_('COM_EB_BLOCK_TEMPLATES_SUCCESSFULLY_UNPUBLISHED');

		$this->info->set($message, 'success');

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplates', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Duplicate selected block templates and save it as new template
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function copyTemplate()
	{
		EB::requireLogin();
		FH::checkToken();

		// Check for permission
		if (!$this->acl->get('create_block_templates') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NOT_ALLOWED', 'error');
			return $this->app->redirect($redirect);
		}

		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$template = EB::table('BlockTemplates');
			$template->load($id);

			$template->duplicate();
		}

		$message = JText::_('COM_EB_POST_TEMPLATES_SUCCESSFULLY_DUPLICATED');
		$this->info->set($message, 'success');

		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplates', false);

		return $this->app->redirect($redirect);
	}
}
