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

class EasyBlogControllerSettings extends EasyBlogController
{
	/**
	 * Saves the settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('setting');

		// Get the settings model
		$model = EB::model('Settings');

		// Get the post data from the form
		$post = $this->input->getArray('post');
		$data = [];

		$activeTab = $this->input->get('activeTab', '', 'default');

		// Get the current layout
		$page = $this->input->get('page', '', 'cmd');

		// Clean the input
		unset($post['task'], $post['option'], $post['page']);

		foreach ($post as $key => $value) {

			if (is_array($value)) {
				$value 	= implode('|', $value);
			}

			// If this is a google adsense settings, make sure it's formatted correctly.
			if ($key == 'integration_google_adsense_code') {
				$value 	= str_ireplace(';"', '', $value);
			}

			if ($key == 'integration_google_adsense_responsive_code') {
				$value = $this->input->get($key, '', 'raw');
			}

			// make sure the list length has the valid value
			if ($key == 'layout_listlength') {
				$value = $this->input->get($key, '', 'int');

				if ($value < 0) {
					$value = 0;
				}
			}

			$data[$key]	= $value;
		}

		if (!isset($post['cover_width_full']) && $page == 'layout') {
			$data['cover_width_full'] = 0;
		}

		if (!isset($post['cover_width_entry_full']) && $page == 'layout') {
			$data['cover_width_entry_full'] = 0;
		}

		// If there's a settings change for EasySocial's privacy, update all the blog post accordingly.
		if (isset($data['main_jomsocial_privacy']) && $data['main_jomsocial_privacy']) {
			$model->updateBlogPrivacy(20);
		}

		// Fix the blog description to allow raw html codes
		if (isset($data['main_description'])) {
			$data['main_description'] = $this->input->get('main_description', '', 'raw');
		}

		// Updated addthis custom code to allow html codes
		if (isset($data['social_addthis_customcode'])) {
			$data['social_addthis_customcode'] = $this->input->get('social_addthis_customcode', '', 'raw');
		}

		// Inherit pagination from joomla
		if (isset($data['listlength_inherit']) && $data['listlength_inherit']) {
			$data['layout_listlength'] = 0;
		}

		// Save custom logo for emails
		if (isset($data['custom_email_logo']) && $data['custom_email_logo']) {

			// Get logo
			$file = $this->input->files->get('email_logo', '');

			// Store logo
			if (!empty($file['tmp_name'])) {
				$model->updateLogo($file, 'email');
			}
		}

		// Process image watermarks if needed to
		$watermarkLogo = $this->input->files->get('watermark_logo');

		if (!empty($watermarkLogo['tmp_name'])) {
			$model->updateLogo($watermarkLogo, 'watermark');
		}

		// Process schema logo
		$schemaLogo = $this->input->files->get('schema_logo', '');

		if (!empty($schemaLogo['tmp_name'])) {
			$model->updateLogo($schemaLogo, 'schema');
		}

		// retrieve the agreement HTML content
		if (isset($data['main_subscription_agreement_message'])) {
			$data['main_subscription_agreement_message'] = $this->input->get('main_subscription_agreement_message', '', 'raw');
		}

		// Try to save the settings now
		$state = $model->save($data);

		$message = $state ? JText::_('COM_EASYBLOG_SETTINGS_STORE_SUCCESS') : JText::_('COM_EASYBLOG_SETTINGS_STORE_ERROR');
		$type = $state ? 'success' : 'error';

		// Set info
		$this->info->set($message, $type);

		// Clear the component's cache
		$cache = JFactory::getCache('com_easyblog');
		$cache->clean();

		$url = 'index.php?option=com_easyblog&view=settings&layout=' . $page;

		if ($activeTab) {
			$url .= '&active=' . $activeTab;
		}

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_SETTINGS_UPDATE', 'settings', array(
			'link' => $url,
			'section' => ucfirst($page)
		));

		$this->app->redirect($url);
	}

	/**
	 * Allows caller to save their api key
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function saveApi()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('setting');

		$model 	= EB::model('Settings');
		$key 	= $this->input->get('apikey', '', 'default');
		$from 	= $this->input->get('from', '', 'default');
		$return = $this->input->get('return', '', 'default');

		// Save the apikey
		$model->save(array('main_apikey' => $key));

		EB::info()->set(JText::_('COM_EASYBLOG_API_KEY_SAVED'), 'success');

		// If return is specified, respect that
		if (!empty($return)) {
			$return  = base64_decode($return);
			$this->app->redirect($return);
		}

		if (empty($from)) {
			$this->app->redirect( 'index.php?option=com_easyblog' , JText::_( '' ) );
		} else {
			$this->app->redirect( 'index.php?option=com_easyblog&view=updater' , JText::_( 'COM_EASYBLOG_API_KEY_SAVED' ) );
		}
	}

	/**
	 * Allows user to import settings file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function import()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('setting');

		// Get the file data
		$file = $this->input->files->get('file');

		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			$this->info->set('COM_EASYBLOG_SETTINGS_IMPORT_ERROR_FILE_INVALID', 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=settings');
		}

		// Get the path to the temporary file
		$path = $file['tmp_name'];
		$contents = file_get_contents($path);

		// Load the configuration
		$table = EB::table('Configs');
		$table->load(array('name' => 'config'));

		$table->params 	= $contents;

		$table->store();

		$this->info->set('COM_EASYBLOG_SETTINGS_IMPORT_SUCCESS', 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=settings');
	}

	/**
	 * Delete email logo
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function restoreLogo()
	{
		$this->checkAccess('setting');

		$type = $this->input->get('type', '', 'string');

		$model = EB::model('Settings');
		$model->restoreLogo($type);

		return $this->ajax->resolve();
	}
}
