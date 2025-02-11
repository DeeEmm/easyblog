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

class EasyBlogControllerAddonsList extends EasyBlogSetupController
{
	public function execute()
	{
		// $this->checkDevelopmentMode();

		$this->engine();

		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		$modulesExtractPath = SI_TMP . '/modules';
		$pluginsExtractPath = SI_TMP . '/plugins';

		// Get the modules list
		$modules = $this->getModulesList($path, $modulesExtractPath);

		// Get the plugins list
		$plugins = $this->getPluginsList($path, $pluginsExtractPath);

		$data = new stdClass();
		$data->modules = $modules;
		$data->plugins = $plugins;

		ob_start();
		include(dirname(__DIR__) . '/themes/steps/addons.list.php');
		$contents = ob_get_contents();
		ob_end_clean();

		$result = new stdClass();
		$result->html = $contents;
		$result->modulePath = $modulesExtractPath;
		$result->pluginPath = $pluginsExtractPath;

		// Since we combine maintenance page with this,
		// we need to get the scripts to execute as well
		$maintenance = $this->getMaintenanceScripts();

		$result->scripts = $maintenance['scripts'];
		$result->maintenanceMsg = $maintenance['message'];

		return $this->output($result);
	}

	private function getMaintenanceScripts()
	{
		$maintenance = EB::maintenance();

		// Get previous version installed
		$previous = $this->getPreviousVersion('scriptversion');

		$files = $maintenance->getScriptFiles($previous);

		$msg = JText::sprintf('COM_EASYBLOG_INSTALLATION_MAINTENANCE_NO_SCRIPTS_TO_EXECUTE');

		if ($files) {
			$msg = JText::sprintf('COM_EASYBLOG_INSTALLATION_MAINTENANCE_TOTAL_FILES_TO_EXECUTE', count($files));
		}

		$result = array('message' => $msg, 'scripts' => $files);

		return $result;
	}

	private function getPluginsList($path, $tmp)
	{
		$zip = $path . '/plugins.zip';

		$state = $this->ebExtract($zip, $tmp);

		// @TODO: Return errors
		if (!$state) {
			return false;
		}

		// Get a list of plugin groups
		$groups = JFolder::folders($tmp, '.', false, true);

		$plugins = array();

		foreach ($groups as $group) {
			$groupTitle = basename($group);

			// Get a list of items in each groups
			$items = JFolder::folders($group, '.', false, true);

			foreach ($items as $item) {
				$element = basename($item);
				$manifest = $item . '/' . $element . '.xml';

				// Read the xml file
				$parser = EB::getXml($manifest);

				if (!$parser) {
					continue;
				}
				$plugin = new stdClass();
				$plugin->element = $element;
				$plugin->group = $groupTitle;
				$plugin->title = (string) $parser->name;
				$plugin->version = (string) $parser->version;
				$plugin->description = (string) $parser->description;
				$plugin->description = trim($plugin->description);
				$plugin->disabled = false;

				if ($plugin->group == 'installer') {
					$plugin->disabled = true;
				}

				$plugins[] = $plugin;
			}
		}

		return $plugins;
	}

	private function getModulesList($path, $tmp)
	{
		$zip = $path . '/modules.zip';

		$state = $this->ebExtract($zip, $tmp);

		// @TODO: Return errors
		if (!$state) {
			return false;
		}

		// Get a list of modules
		$items = JFolder::folders($tmp, '.', false, true);

		$modules = array();
		$installedModules = array();

		// Get installed module.
		// We only do this for upgrade from 3.x
		if ($this->isUpgradeFrom3x()) {
			$installedModules = $this->getInstalledModules();
		}

		// Get previous version installed.
		// If previous version exists, means this is an upgrade
		$isUpgrade = $this->getPreviousVersion('scriptversion');

		foreach ($items as $item) {
			$element = basename($item);
			$manifest = $item . '/' . $element . '.xml';

			// Read the xml file
			$parser = EB::getXml($manifest);

			$module = new stdClass();
			$module->title = (string) $parser->name;
			$module->version = (string) $parser->version;
			$module->description = (string) $parser->description;
			$module->description = trim($module->description);
			$module->element = $element;
			$module->disabled = false;
			$module->checked = true;

			// we tick modules that are installed on the site
			if ($isUpgrade) {
				$module->checked = $this->isModuleInstalled($element);
			}

			// Check if the module already installed, put a flag
			// Disable this only if the module is checked.
			if (in_array($module->element, $installedModules)) {
				$module->disabled = true;
			}

			$modules[] = $module;
		}

		return $modules;
	}

	/**
	 * Determines if the module is installed on the site.
	 *
	 * @since   5.1
	 * @access  public
	 */
	private function isModuleInstalled($module)
	{
		$db = EB::db();

		// If module name is mod_easybloglatestblogs
		// We also need to check for mod_latestblogs (3.9)
		$names = array('mod_easyblogtopblogs' => 'mod_topblogs',
						'mod_easyblogteamblogs' => 'mod_teamblogs',
						'mod_easyblogsubscribers' => 'mod_subscribers',
						'mod_easyblogsearch' => 'mod_searchblogs',
						'mod_easybloglatestblogs' => 'mod_latestblogs',
						'mod_easyblogimagewall' => 'mod_imagewall',
						'mod_easyblogshowcase' => 'mod_showcase');

		if (array_key_exists($module, $names)) {
			$module = $db->Quote($module) . ', ' . $db->Quote($names[$module]);
		} else {
			$module = $db->Quote($module);
		}

		$query = array();
		$query[] = 'SELECT '. $db->quoteName('module') .' FROM ' . $db->quoteName('#__modules');
		$query[] = ' WHERE ' . $db->quoteName('module') . ' IN (' . $module . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result) {
			return true;
		}

		return false;
	}

	/**
	 * Get all installed modules from the site (only for 3.x upgrade)
	 *
	 * @since   5.1
	 * @access  public
	 */
	private function getInstalledModules()
	{
		$db = EB::db();

		$moduleNames = array('mod_easyblogarchive','mod_easyblogbio','mod_easyblogcalendar','mod_easyblogcategories','mod_easybloglatestblogger','mod_easybloglatestcomment','mod_easybloglist',
							'mod_easyblogmostactiveblogger','mod_easyblogmostcommentedpost','mod_easyblogmostpopularpost','mod_easyblognewpost','mod_easyblogpostmap','mod_easyblograndompost',
							'mod_easyblogrelatedpost','mod_easyblogsubscribe','mod_easyblogtagcloud','mod_easyblogwelcome','mod_imagewall','mod_latestblogs','mod_searchblogs','mod_showcase',
							'mod_subscribers','mod_teamblogs','mod_topblogs');


		$modules = '';
		foreach($moduleNames as $module){
			$modules .= ($modules) ? ',' . $db->Quote($module) : $db->Quote($module);
		}

		// jos_modules
		$query = array();
		$query[] = 'SELECT '. $db->quoteName('module') .' FROM ' . $db->quoteName('#__modules');
		$query[] = ' WHERE ' . $db->quoteName('module') . ' IN (' . $modules . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		// module names old => new
		$names = array('mod_topblogs' => 'mod_easyblogtopblogs',
						'mod_teamblogs' => 'mod_easyblogteamblogs',
						'mod_subscribers' => 'mod_easyblogsubscribers',
						'mod_searchblogs' => 'mod_easyblogsearch',
						'mod_latestblogs' => 'mod_easybloglatestblogs',
						'mod_imagewall' => 'mod_easyblogimagewall',
						'mod_showcase' => 'mod_easyblogshowcase');

		$modules = array();

		// try rename the variable to follow new modules name
		foreach ($result as $module) {
			if (array_key_exists($module->module, $names)) {
				$module->module = $names[$module->module];
			}
			$modules[] = $module->module;
		}

		return $modules;
	}

	private function isUpgradeFrom3x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {
			$isUpgrade = false;

			$db = JFactory::getDBO();

			$jConfig = JFactory::getConfig();
			$prefix = $jConfig->get('dbprefix');

			$query = "SHOW TABLES LIKE '%" . $prefix . "easyblog_configs%'";
			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result) {
				// This is an upgrade. let's check if the upgrade from 3.x or not.
				$query = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__easyblog_configs') . ' WHERE ' . $db->quoteName('name') . '=' . $db->Quote('scriptversion');
				$db->setQuery($query);

				$scriptversion = $db->loadResult();

				if ($scriptversion) {
					$scriptversion = explode('.', $scriptversion);

					// We know if the scriptversion is equal to 3, this is upgrade from version 3.x
					if ($scriptversion[0] == '3') {
						$isUpgrade = true;
					}
				}
			}
		}

		return $isUpgrade;
	}
}