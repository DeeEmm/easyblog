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

class EasyBlogControllerBlocks extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
		$this->registerTask('apply', 'save');

		// Block templates
		$this->registerTask('publishTemplate', 'togglePublishTemplate');
		$this->registerTask('unpublishTemplate', 'togglePublishTemplate');
		$this->registerTask('applyFormTemplate', 'saveTemplate');
		$this->registerTask('saveFormTemplate', 'saveTemplate');
		$this->registerTask('setGlobalTemplate', 'toggleGlobalTemplate');
		$this->registerTask('removeGlobalTemplate', 'toggleGlobalTemplate');
	}

	/**
	 * Saves the block settings
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = EB::table('Block');
		$table->load($id);

		$published = $this->input->get('published', '', 'bool');

		// System block published state cannot be change
		if ($table->id && $table->published == EASYBLOG_COMPOSER_BLOCKS_NOT_VISIBLE) {
			$published = EASYBLOG_COMPOSER_BLOCKS_NOT_VISIBLE;
		}

		$table->published = $published;

		$params = $this->input->get('params', '', 'array');

		$table->params = json_encode($params);

		$table->store();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_BLOCKS_UPDATED', 'blocks', array(
			'link' => 'index.php?option=com_easyblog&view=blocks&layout=form&id=' . $table->id,
			'blockTitle' => $table->title
		));

		$task = $this->getTask();

		$message = 'Block saved successfully';
		$redirect = 'index.php?option=com_easyblog&view=blocks';

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easyblog&view=blocks&layout=form&id=' . $table->id;
		}

		$this->info->set($message, 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Toggles the publishing state of a block
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		FH::checkToken();

		// Default redirection url
		$redirect = 'index.php?option=com_easyblog&view=blocks';

		// Get the items to be published / unpublished
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_BLOCKS_INVALID_ID_PROVIDED', 'error');
			return $this->app->redirect($redirect);
		}

		// Get the current task
		$task = $this->getTask();

		$actionString = 'COM_EB_ACTIONLOGS_BLOCKS_PUBLISHED';
		$message = 'COM_EASYBLOG_BLOCKS_PUBLISHED_SUCCESSFULLY';

		if ($task == 'unpublish') {
			$actionString = 'COM_EB_ACTIONLOGS_BLOCKS_UNPUBLISHED';
			$message = 'COM_EASYBLOG_BLOCKS_UNPUBLISHED_SUCCESSFULLY';
		}

		foreach ($ids as $id) {
			$block = EB::table('Block');
			$block->load((int) $id);

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log($actionString, 'blocks', array(
				'link' => 'index.php?option=com_easyblog&view=blocks&layout=form&id=' . $block->id,
				'blockTitle' => $block->title
			));

			$block->$task();
		}

		$this->info->set(JText::_($message), 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Process blocks installation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function upload()
	{
		FH::checkToken();

		$redirect = 'index.php?option=com_easyblog&view=blocks&layout=install';

		// Get the zip file
		$file = $this->input->files->get('package', array(), 'raw');

		$blocks = EB::blocks();
		$state = $blocks->install($file);

		if (!$state || $state === false) {
			$this->info->set($blocks->getError(), 'error');
		} else {
			$this->info->set(JText::_('COM_EASYBLOG_BLOCKS_INSTALL_SUCCESS'), 'success');
		}

		return $this->app->redirect($redirect);
	}

	/**
	 * Save a block template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function saveTemplate()
	{
		// Check for request forgeries
		FH::checkToken();

		$this->checkAccess('blocks');

		// Get any return urls
		$return = $this->input->get('return', '', 'default');
		$return = $return ? base64_decode($return) : 'index.php?option=com_easyblog&view=blocks&layout=templates';

		$id = $this->input->get('id', 0, 'int');

		$template = EB::table('BlockTemplates');
		$template->load($id);

		$post = $this->input->post->getArray();

		$template->bind($post);

		// Save the template
		$template->store();

		$this->info->set('COM_EB_BLOCK_TEMPLATE_SAVED_SUCCESS', 'success');

		$task = $this->getTask();

		if ($task == 'applyFormTemplate') {
			$return = 'index.php?option=com_easyblog&view=blocks&layout=editTemplate&id=' . $template->id;
		}

		return $this->app->redirect($return);
	}

	/**
	 * Toggles the template publishing state
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function togglePublishTemplate()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user really has access to this section
		$this->checkAccess('blocks');

		// Default redirection
		$return = 'index.php?option=com_easyblog&view=blocks&layout=templates';

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect($return);
		}

		$task = $this->getTask();

		$published = $task == 'publishTemplate' ? 1 : 0;

		foreach ($ids as $id) {
			$template = EB::table('BlockTemplates');
			$template->load($id);

			$template->published = $published;

			$template->store();
		}

		$message = JText::_('COM_EB_BLOCK_TEMPLATES_SUCCESSFULLY_PUBLISHED');

		if ($task == 'unpublishTemplate') {
			$message = JText::_('COM_EB_BLOCK_TEMPLATES_SUCCESSFULLY_UNPUBLISHED');
		}

		$this->info->set($message, 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Toggles the global template status
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function toggleGlobalTemplate()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user really has access to this section
		$this->checkAccess('blocks');

		// Default redirection
		$return = 'index.php?option=com_easyblog&view=blocks&layout=templates';

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_BLOG_ID'), 'error');

			return $this->app->redirect($return);
		}

		$global = $this->getTask() == 'setGlobalTemplate' ? true : false;

		foreach ($ids as $id) {
			$template = EB::table('BlockTemplates');
			$template->load($id);

			$template->global = $global;

			$template->store();
		}

		$message = JText::_('COM_EB_BLOCK_TEMPLATES_SUCCESSFULLY_SET_AS_GLOBAL_TEMPLATE');

		if ($task == 'removeGlobalTemplate') {
			$message = JText::_('COM_EB_BLOCK_TEMPLATES_SUCCESSFULLY_REMOVED_FROM_GLOBAL_TEMPLATE');
		}

		$this->info->set($message, 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Deletes block templates from the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function deleteBlockTemplates()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl access
		$this->checkAccess('blocks');

		$return = 'index.php?option=com_easyblog&view=blocks&layout=templates';

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOG_ID', 'error');

			return $this->app->redirect($return);
		}


		foreach ($ids as $id) {
			$template = EB::table('BlockTemplates');
			$template->load($id);

			$template->delete();
		}

		$this->info->set('COM_EB_BLOCK_TEMPLATES_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Duplicate the block template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function copyTemplate()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user really has access to this section
		$this->checkAccess('blocks');

		// Default redirection
		$return = 'index.php?option=com_easyblog&view=blocks&layout=templates';

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$template = EB::table('BlockTemplates');
			$template->load($id);
			$template->duplicate();
		}

		$message = JText::_('COM_EB_BLOCK_TEMPLATES_SUCCESSFULLY_DUPLICATED');

		$this->info->set($message, 'success');
		return $this->app->redirect($return);

	}
}
