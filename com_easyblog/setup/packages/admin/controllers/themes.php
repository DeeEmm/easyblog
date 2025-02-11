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

class EasyBlogControllerThemes extends EasyBlogController
{
	/**
	 * Saves the custom.css contents
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function saveCustomCss()
	{
		FH::checkToken();

		$model = EB::model('Themes');
		$path = $model->getCustomCssTemplatePath();

		$contents = $this->input->get('contents', '', 'raw');

		JFile::write($path, $contents);

		$this->info->set(JText::sprintf('COM_EASYBLOG_THEMES_CUSTOM_CSS_SAVE_SUCCESS', $path), 'success');

		$redirect = 'index.php?option=com_easyblog&view=themes&layout=custom';
		
		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_CUSTOMCSS_UPDATED', 'post', array(
			'themeTitle' => ucfirst($element)
		));

		return $this->app->redirect($redirect);
	}

	/**
	 * Saves theme settings
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function saveSettings()
	{
		FH::checkToken();

		$model = EB::model('Themes');
		$post = $this->input->post->getArray();
		
		$data = array();

		// Filter out data that we want to save only
		foreach ($post as $key => $value) {
			if (stristr($key, 'params_') === false) {
				continue;
			}

			$data[$key] = $value;
		}

		$element = $this->input->get('id', '', 'word');

		$table = EB::table('Configs');
		$table->name = $element;
		$table->params = json_encode($data);
		$table->store($element);

		$this->info->set('Theme settings saved successfully', 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=themes');
	}

	/**
	 * Saves the contents of a theme file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function saveFile()
	{
		FH::checkToken();

		$element = $this->input->get('element', '', 'cmd');
		$id = $this->input->get('id', '', 'default');
		$contents = $this->input->get('contents', '', 'raw');

		// Default redirection url
		$redirect = 'index.php?option=com_easyblog&view=themes&layout=editor&element=' . $element . '&id=' . $id;

		$model = EB::model('Themes');
		$file = $model->getFile($id, $element);

		// Save the file now
		$state = $model->write($file, $contents);

		if (!$state) {
			$this->info->set(JText::sprintf('COM_EASYBLOG_THEMES_SAVE_ERROR', $file->override), 'error');
			return $this->app->redirect($redirect);
		}

		// Document the changes
		$table = EB::table('ThemeOverrides');
		$table->load(array('file_id' => $file->override));
		$table->file_id = $file->override;
		$table->notes = $this->input->get('notes', '', 'default');
		$table->contents = $contents;
		$table->store();

		$this->info->set(JText::sprintf('COM_EASYBLOG_THEMES_SAVE_SUCCESS', $file->override), 'success');

		return $this->app->redirect($redirect);
	}

	/**
	 * Allows caller to revert a theme file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function revert()
	{
		FH::checkToken();

		$element = $this->input->get('element', '', 'cmd');
		$id = $this->input->get('id', '', 'default');
		$contents = $this->input->get('contents', '', 'raw');

		$model = EB::model('Themes');
		$file = $model->getFile($id, $element);

		// Default redirection url
		$redirect = 'index.php?option=com_easyblog&view=themes&layout=editor&element=' . $element . '&id=' . $id;

		// Save the file now
		$state = $model->revert($file);

		// Also delete the overrides table
		$table = EB::table('ThemeOverrides');
		$table->load(array('file_id' => $file->override));
		$table->delete();

		if (!$state) {
			$this->info->set(JText::sprintf('COM_EASYBLOG_THEMES_DELETE_ERROR', $file->override), SOCIAL_MSG_ERROR);
			return $this->app->redirect($redirect);
		}

		$this->info->set(JText::sprintf('COM_EASYBLOG_THEMES_DELETE_SUCCESS', $file->override), SOCIAL_MSG_SUCCESS);
		
		return $this->app->redirect($redirect);
	}

	/**
	 * Installs a new theme on the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function upload()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the file from the server.
		// Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
		$file 	= $this->input->files->get('package', '', 'raw');

		// Get themes model
		$model	= EB::model('Themes');
		$state 	= $model->install($file);

		$link = 'index.php?option=com_easyblog&view=themes';

		if (!$state) {
			EB::info()->set($model->getError(), 'error');
			$link = 'index.php?option=com_easyblog&view=themes&layout=install';
		} else {
			EB::info()->set(JText::_('COM_EASYBLOG_THEME_INSTALLED_SUCCESS'), 'success');
		}

		$this->app->redirect($link);
	}

	/**
	 * Make the provided theme a default theme for EasyBlog
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setDefault()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('theme');

		$element = $this->input->get('cid', '', 'array');
		$element = $element[0];

		if (!$element || !isset($element[0])) {

			EB::info()->set(JText::_('COM_EASYBLOG_THEME_INVALID_THEME_PROVIDED'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=themes');
		}

		// Legacy codes and should be removed soon
		$this->config->set('layout_theme', $element);

		// Get the configuration object
		$this->config->set('theme_site', $element);

		$table 	= EB::table('Configs');
		$table->load('config');

		$table->params 	= $this->config->toString('INI');
		$table->store();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_THEME_DEFAULT', 'post', array(
			'themeTitle' => ucfirst($element)
		));

		// Clear the component's cache
		$cache = JFactory::getCache('com_easyblog');
		$cache->clean();

		EB::info()->set(JText::sprintf('COM_EASYBLOG_THEME_SET_AS_DEFAULT', $element), 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=themes');
	}
}
