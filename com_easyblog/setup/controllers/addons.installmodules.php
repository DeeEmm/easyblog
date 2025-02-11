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

class EasyBlogControllerAddonsInstallModules extends EasyBlogSetupController
{
	public function execute()
	{
		$this->engine();

		$path = $this->input->get('path', '', 'default');
		$modules = $this->input->get('modules', [], 'array');

		// Try to install the modules now.
		$state = $this->install($modules, $path);

		// Configure Stackideas Toolbar module
		// Dont do this on fresh install
		$isUpgrade = $this->getPreviousVersion('scriptversion');

		if ($isUpgrade) {
			$this->processToolbarModule();
		}

		$this->setInfo('The selected modules have been installed on the site.', true);
		return $this->output();
	}

	public function install($modules, $path)
	{
		if ($this->isDevelopment()) {
			$this->setInfo('ok', true);
			return $this->output();
		}

		if (empty($modules)) {
			return true;
		}

		foreach ($modules as $module) {
			// Construct the absolute path to the module
			$absolutePath = $path . '/' . $module;

			// Get Joomla's installer instance
			$installer = new JInstaller();

			// Allow overwriting existing modules
			$installer->setOverwrite(true);

			// Prevent any output from the installer
			ob_start();
			// Install the module
			$state = $installer->install($absolutePath);
			ob_end_clean();

			if (!$state) {
				continue;
			}

			$db = EB::db();

			$query = [];
			$query[] = 'UPDATE ' . $db->qn('#__extensions') . ' SET ' . $db->qn('access') . '=' . $db->Quote(1);
			$query[] = 'WHERE ' . $db->qn('type') . '=' . $db->Quote('module');
			$query[] = 'AND ' . $db->qn('element') . '=' . $db->Quote($module);
			$query[] = 'AND ' . $db->qn('access') . '=' . $db->Quote(0);

			$query = implode(' ', $query);

			$db->setQuery($query);
			$db->Query();

			// Check if this module already exists on module_menu
			$query = [];
			$query[] = 'SELECT a.' . $db->qn('id') . ', b.' . $db->qn('moduleid') . ' FROM ' . $db->qn('#__modules') . ' AS a';
			$query[] = 'LEFT JOIN ' . $db->qn('#__modules_menu') . ' AS b ON a.' . $db->qn('id') . ' = b.' . $db->qn('moduleid');
			$query[] = 'WHERE a.' . $db->qn('module') . ' = ' . $db->Quote($module);
			$query[] = 'AND b.' . $db->qn('moduleid') . ' IS NULL';

			$query = implode(' ', $query);
			$db->setQuery($query);

			$result = $db->loadObjectList();

			if (!$result) {
				continue;
			}

			foreach ($result as $row) {
				$mod = new stdClass();
				$mod->moduleid = $row->id;
				$mod->menuid = 0;

				$db->insertObject('#__modules_menu', $mod);
			}
		}

		return true;
	}

	public function processToolbarModule()
	{
		$db = EB::db();

		// Get all published Easyblog Toolbar modules
		$query = array();
		$query[] = 'SELECT a.*, b.' . $db->qn('moduleid') . ' FROM ' . $db->qn('#__modules') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__modules_menu') . ' AS b ON a.' . $db->qn('id') . ' = b.' . $db->qn('moduleid');
		$query[] = 'WHERE a.' . $db->qn('module') . ' = ' . $db->Quote('mod_easyblogtoolbar');
		$query[] = 'AND a.' . $db->qn('published') . ' = ' . $db->Quote(1);

		$query = implode(' ', $query);
		$db->setQuery($query);
		$results = $db->loadObjectList();

		// if nothing means the user never use easyblogtoolbar module
		if (!$results) {
			return;
		}

		// Get stackideas toolbar record
		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__modules');
		$query[] = 'WHERE ' . $db->qn('module') . '=' . $db->Quote('mod_stackideas_toolbar');

		$query = implode(' ', $query);
		$db->setQuery($query);
		$toolbarMod = $db->loadObject();

		foreach ($results as $module) {
			// Copy Stackideas Toolbar to replace this
			$jMod = JTable::getInstance('Module');
			$jMod->load($toolbarMod->id);

			// Reset the ID so that it creates new copy
			$jMod->id = 0;
			$jMod->title = $module->title;
			$jMod->position = $module->position;
			$jMod->ordering = $module->ordering;
			$jMod->showtitle = $module->showtitle;
			$jMod->language = $module->language;
			$jMod->published = 1;

			$jMod->store();

			$mod = new stdClass();
			$mod->moduleid = $jMod->id;
			$mod->menuid = 0;

			$db->insertObject('#__modules_menu', $mod);

			// lastly, unpublish the Easyblog Toolbar module
			$query = array();
			$query[] = 'UPDATE ' . $db->qn('#__modules') . ' SET ' . $db->qn('published') . '=' . $db->Quote(0);
			$query[] = 'WHERE ' . $db->qn('id') . '=' . $db->Quote($module->id);

			$query = implode(' ', $query);
			$db->setQuery($query);
			$db->Query();
		}
	}

}
