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

class EasyBlogModelModules extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.modules.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.modules.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the modules manifest list
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	private function buildQuery()
	{
		$db = EB::db();

		$query = [
			'SELECT * FROM `#__easyblog_packages`',
			'WHERE `group`=' . $db->Quote('modules')
		];

		// Search
		$search = $this->app->getUserStateFromRequest('com_easyblog.modules.search', 'search', '', 'string');

		if ($search) {
			$search = $db->getEscaped(trim(strtolower($search)));

			$query[] = 'AND (';
			$query[] = '`title` LIKE ' . $db->Quote('%' . $search . '%');
			$query[] = 'OR `element` LIKE ' . $db->Quote('%' . $search . '%');
			$query[] = ')';
		}

		$published = $this->app->getUserStateFromRequest('com_easyblog.modules.published', 'published', '', 'word');

		if ($published) {
			if ($published == 'installed') {
				$query[] = 'AND (';
				$query[] = '`state` = ' . $db->Quote(EB_PACKAGE_NEEDS_UPDATING);
				$query[] = 'OR `state` = ' . $db->Quote(EB_PACKAGE_INSTALLED);
				$query[] = ')';
			}

			if ($published == 'notinstalled') {
				$query[] = 'AND `state` = ' . $db->Quote(EB_PACKAGE_NOT_INSTALLED);
			}

			if ($published == 'updating') {
				$query[] = 'AND `state` = ' . $db->Quote(EB_PACKAGE_NEEDS_UPDATING);
			}
		}

		$query = implode(' ', $query);

		return $query;
	}

	/**
	 * Determines if the module manifest has been populated before
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function initialized()
	{
		$db = EB::db();

		$query = [
			'SELECT COUNT(1) FROM `#__easyblog_packages`',
			'WHERE `type`=' . $db->Quote('modules')
		];

		$db->setQuery($query);

		$initialized = $db->loadResult() > 0;

		return $initialized;
	}

	/**
	 * Retrieves the full manifest from the server
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function discoverManifest($apikey)
	{
		$connector = FH::connector(EB_SERVICE_PACKAGES_DISCOVER);
		$connector->addQuery('key', $apikey);
		$connector->addQuery('type', 'modules');
		$connector->setMethod('POST');
		$contents = $connector->execute()->getResult();

		$manifest = json_decode($contents);

		if ($manifest->state != 200) {
			return false;
		}

		foreach ($manifest->items as $module) {
			$package = EB::table('Package');
			$package->load([
				'group' => 'modules',
				'element' => $module->element
			]);

			$package->type = 'modules';
			$package->group = 'modules';
			$package->element = $module->element;
			$package->title = $module->name;
			$package->updated = '0000-00-00 00:00:00';
			$package->description = $module->description;
			$package->version = $module->version;
			$package->params = '';

			// Check if this module is installed
			$jmodule = $this->getJoomlaModule($module->element);
			$package->state = $jmodule ? true : false;

			if ($jmodule) {
				$jmodule->manifest_cache = json_decode($jmodule->manifest_cache);
				$package->state = version_compare($jmodule->manifest_cache->version, $package->version) === -1 ? EB_PACKAGE_NEEDS_UPDATING : $package->state;
			}

			$state = $package->store();
		}

		return $manifest;
	}

	/**
	 * Retrieves the record for #__extensions
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getJoomlaModule($element)
	{
		$db = EB::db();

		$query = [
			'select * from `#__extensions`',
			'where `type`=' . $db->Quote('module'),
			'and `element`=' . $db->Quote($element),
			'and `state` !=' . $db->Quote(-1) // Discovered extensions are not installed yet.
		];

		$db->setQuery($query);
		$module = $db->loadObject();

		return $module;
	}

	public function getModules()
	{
		if (empty($this->_data)) {
			$this->_data = $this->_getList($this->buildQuery(), $this->getState('limitstart'), $this->getState('limit'));

			if ($this->_data) {
				foreach ($this->_data as &$package) {
					$package->installed = false;

					if ($this->isInstalled($package->element)) {
						$jmodule = $this->getJoomlaModule($package->element);

						$jmodule->manifest_cache = json_decode($jmodule->manifest_cache);

						$package->installed = $jmodule->manifest_cache->version;
						$package->state = version_compare($package->installed, $package->version) === -1 ? EB_PACKAGE_NEEDS_UPDATING : $package->state;
					}
				}
			}
		}

		return $this->_data;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Load total number of rows
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->buildQuery());
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = EB::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Determines if a Joomla module is installed on the site given the element.
	 * ModuleHelper in Joomla does not seem to provide any helpers to detect this
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isInstalled($element)
	{
		$db = EB::db();

		$query = [
			'select count(1) from `#__extensions`',
			'where `type`=' . $db->Quote('module'),
			'and `element`=' . $db->Quote($element),
			'and `state` != ' . $db->Quote(-1)
		];

		$db->setQuery($query);
		$installed = $db->loadResult() > 0 ? EB_PACKAGE_INSTALLED : EB_PACKAGE_NOT_INSTALLED;

		return $installed;
	}
}
