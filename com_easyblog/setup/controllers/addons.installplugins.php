<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerAddonsInstallPlugins extends EasyBlogSetupController
{
	public function execute()
	{
		$this->engine();

		$path = $this->input->get('path', '', 'default');
		$plugins = $this->input->get('plugins', [], 'array');

		// Try to install the plugins now
		$state = $this->installPlugins($plugins, $path);

		$this->setInfo('The selected plugins have been installed on the site.', true);
		return $this->output();
	}

	/**
	 * Installation of plugins on the site
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function installPlugins($plugins, $path)
	{
		if ($this->isDevelopment()) {
			$this->setInfo('ok', true);
			return $this->output();
		}

		if (empty($plugins)) {
			return true;
		}

		foreach ($plugins as $plugin) {
			$plugin = (object) $plugin;

			$group = $plugin->group;
			$element = $plugin->element;

			// Construct the absolute path
			$absolutePath = $path . '/' . $group . '/' . $element;

			// Get Joomla's installer instance
			$installer = JInstaller::getInstance();

			// Allow overwriting of existing plugins
			$installer->setOverwrite(true);

			$db = EB::db();
			$group = strtolower($group);
			$element = strtolower($element);

			// Check if auto article plugin already exists
			$query = [];
			$query[] = 'SELECT * FROM ' . $db->quoteName('#__extensions');
			$query[] = 'WHERE ' . $db->quoteName('folder') . '=' . $db->Quote($group);
			$query[] = 'AND ' . $db->quoteName('element') . '=' . $db->Quote($element);

			$query = implode(' ', $query);
			$db->setQuery($query);

			$result = $db->loadObject();

			$isAutoArticlePluginInstallBefore = false;
			$isAutoArticlePluginEnabled = false;

			// Retrieve the current plugin whether got enable it or not
			if ($result) {
				$isAutoArticlePluginInstallBefore = true;
				$isAutoArticlePluginEnabled = $result->enabled;
			}

			// Prevent any output from the installer
			ob_start();

			// Install the plugin now
			$state = $installer->install($absolutePath);

			ob_end_clean();

			// Ensure that the plugins are published
			if ($state) {
				$options = ['folder' => $group, 'element' => $element];

				$plugin = JTable::getInstance('Extension');
				$plugin->load($options);

				// set the state to 0 means 'installed'.
				$plugin->state = 0;
				$plugin->enabled = true;

				if ($group == 'easyblog' && $element == 'autoarticle') {

					// We should disable auto article plugin if that is not install before
					$plugin->enabled = false;

					// We should respect back this plugin enable state if the plugin installed before
					if ($isAutoArticlePluginInstallBefore) {
						$plugin->enabled = $isAutoArticlePluginEnabled;
					}
				}

				$plugin->store();
			}
		}

		return $state;
	}
}