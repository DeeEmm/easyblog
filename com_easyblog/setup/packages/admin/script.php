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

jimport('joomla.filesystem.file');

class com_EasyBlogInstallerScript
{
	/**
	 * Triggered after the installation is completed
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function postflight()
	{
		ob_start();
		include(__DIR__ . '/setup.html');

		$contents = ob_get_contents();
		ob_end_clean();

		echo $contents;
	}

	/**
	 * Triggered before the installation is complete
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function preflight()
	{
		// Ensure that this is Joomla 3.0
		$joomlaOutdated = version_compare(JVERSION, '3.0') === -1;

		if ($joomlaOutdated) {
			JFactory::getApplication()->enqueueMessage('EasyBlog requires a minimum of Joomla 3.0 to be installed', 'error');
			return false;
		}

		// During the preflight, we need to create a new installer file in the temporary folder
		$file = JPATH_ROOT . '/tmp/easyblog.installation';

		// Determines if the installation is a new installation or old installation.
		$obj = new stdClass();
		$obj->new = false;
		$obj->step = 1;
		$obj->status = 'installing';

		$contents = json_encode($obj);

		if (!JFile::exists($file)) {
			JFile::write($file, $contents);
		}

		// Disable plugins when upgrading from 3.x
		if ($this->isUpgradeFrom3x()) {
			$this->unPublishPlugins();

			// #1680 On some instances, there are missing subscription tables
			$this->checkMissingSubscriptionTables();
		}

		$this->unPublishSystemsPlugins();

		// now let check the eb config
		$this->checkEBVersionConfig();

		// Remove easyblog.30.xml and easyblog.16.xml (if any)
		$this->removeLegacyXMLFiles();
	}

	/**
	 * Resposible to remove legacy xml files (easyblog.30.xml and easyblog.16.xml)
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function removeLegacyXMLFiles()
	{
		$files = array('easyblog.30.xml', 'easyblog.16.xml', 'easyblog.30.xml.backup', 'easyblog.16.xml.backup');

		foreach ($files as $file) {
			$path = JPATH_ROOT . '/administrator/components/com_easyblog/' . $file;

			if (JFile::exists($path)) {
				JFile::delete($path);
			}
		}
	}

	/**
	 * Determines if a table exists on the site
	 *
	 * @since	5.2.10
	 * @access	public
	 */
	private function tableExists($name)
	{
		$jConfig = JFactory::getConfig();
		$prefix = $jConfig->get('dbprefix');

		$db = JFactory::getDBO();
		$query = "SHOW TABLES LIKE '%" . $prefix . $name . "%'";
		$db->setQuery($query);

		$result = $db->loadResult();

		if ($result) {
			return true;
		}

		return false;
	}

	/**
	 * There are instances on some upgrades where there are missing subscription tables
	 *
	 * @since	5.2.10
	 * @access	public
	 */
	private function checkMissingSubscriptionTables()
	{
		static $executed = false;

		if ($executed) {
			return;
		}

		$executed = true;

		$db = JFactory::getDBO();

		// Ensure that #__easyblog_post_subscription exists
		if (!$this->tableExists('easyblog_post_subscription')) {
			$query = "CREATE TABLE IF NOT EXISTS `#__easyblog_post_subscription` (
						`id` bigint(20) unsigned NOT NULL auto_increment,
						`post_id` bigint(20) unsigned NOT NULL,
						`user_id` bigint(20) unsigned NULL DEFAULT '0',
						`fullname` varchar(255) NULL,
						`email` varchar(100) NOT NULL,
						`created` datetime NOT NULL default '0000-00-00 00:00:00',
						PRIMARY KEY  (`id`),
						KEY `easyblog_post_subscription_post_id` (`post_id`),
						KEY `easyblog_post_subscription_user_id` (`user_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$db->setQuery($query);
			$db->execute();
		}

		// Ensure that #__easyblog_category_subscription exists
		if (!$this->tableExists('easyblog_category_subscription')) {
			$query = "CREATE TABLE IF NOT EXISTS `#__easyblog_category_subscription` (
						`id` bigint(20) unsigned NOT NULL auto_increment,
						`category_id` bigint(20) unsigned NOT NULL,
						`user_id` bigint(20) unsigned NULL DEFAULT '0',
						`fullname` varchar(255) NULL,
						`email` varchar(100) NOT NULL,
						`created` datetime NOT NULL default '0000-00-00 00:00:00',
						PRIMARY KEY  (`id`),
						KEY `easyblog_category_subscription_category_id` (`category_id`),
						KEY `easyblog_category_subscription_user_id` (`user_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$db->setQuery($query);
			$db->execute();
		}

		if (!$this->tableExists('easyblog_blogger_subscription')) {
			$query = "CREATE TABLE IF NOT EXISTS `#__easyblog_blogger_subscription` (
						`id` bigint(20) unsigned NOT NULL auto_increment,
						`blogger_id` bigint(20) unsigned NOT NULL,
						`user_id` bigint(20) unsigned NULL DEFAULT '0',
						`fullname` varchar(255) NULL,
						`email` varchar(100) NOT NULL,
						`created` datetime NOT NULL default '0000-00-00 00:00:00',
						PRIMARY KEY  (`id`),
						KEY `easyblog_blogger_subscription_blogger_id` (`blogger_id`),
						KEY `easyblog_blogger_subscription_user_id` (`user_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Responsible to check eb configs db version
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function checkEBVersionConfig()
	{
		// if there is the config table but no dbversion, we know this upgrade is coming from pior 5.0. lets add on dbversion into config table.
		if ($this->isUpgradeFrom3x()) {

			// get current installed eb version.
			$xmlfile = JPATH_ROOT. '/administrator/components/com_easyblog/easyblog.xml';

			// set this to version prior 3.8.0 so that it will execute the db script from 3.9.0 as well incase
			// this upgrade is from very old version.
			$version = '3.8.0';

			if (JFile::exists($xmlfile)) {
				$contents = file_get_contents($xmlfile);
				$parser = simplexml_load_string($contents);
				$version = $parser->xpath('version');
				$version = (string) $version[0];
			}

			$db = JFactory::getDBO();

			// ok, now we got the version. lets add this version into dbversion.
			$query = 'INSERT INTO ' . $db->quoteName('#__easyblog_configs') . ' (`name`, `params`) VALUES';
			$query .= ' (' . $db->Quote('dbversion') . ',' . $db->Quote($version) . '),';
			$query .= ' (' . $db->Quote('scriptversion') . ',' . $db->Quote($version) . ')';

			$db->setQuery($query);

			if (method_exists($db, 'query')) {
				$db->query();
			} else {
				$db->execute();
			}

		}
	}

	/**
	 * Determines if the installation is currently upgraded from 3.x
	 *
	 * @since	5.2.10
	 * @access	public
	 */
	private function isUpgradeFrom3x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {

			$isUpgrade = false;

			$db = JFactory::getDBO();

			$tableExists = $this->tableExists('easyblog_configs');

			if ($tableExists) {
				// this is an upgrade. lets check if the upgrade from 3.x or not.
				$query = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__easyblog_configs') . ' WHERE ' . $db->quoteName('name') . '=' . $db->Quote('dbversion');
				$db->setQuery($query);

				$exists = $db->loadResult();

				if (!$exists) {
					$isUpgrade = true;
				}
			}
		}

		return $isUpgrade;
	}

	/**
	 * Responsible to perform the uninstallation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function uninstall()
	{
		$this->unpublishModules();
		$this->unPublishPlugins();
	}

	/**
	 * Responsible to perform component updates
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function update()
	{
	}

	/**
	 * Unpublish EasyBlog system plugins to avoid installation issue.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function unPublishSystemsPlugins()
	{
		$db = JFactory::getDBO();

		$pluginNames = array(
				'system' => array('easyblogcomposer'),
				'user' => 'easyblogusers'
			);

		foreach ($pluginNames as $folder => $elements) {
			$tempElements = '';

			if (is_array($elements)) {
				foreach($elements as $element){
					$tempElements .= ($tempElements) ? ',' . $db->Quote($element) : $db->Quote($element);
				}
			} else {
				$tempElements = $db->Quote($elements);
			}

			$query = array();
			$query[] = 'UPDATE ' . $db->quoteName('#__extensions') . ' SET ' . $db->quoteName('enabled') . '=' . $db->Quote('0');
			$query[] = 'WHERE ' . $db->quoteName('folder') . '=' . $db->Quote($folder);
			$query[] = 'AND ' . $db->quoteName('element') . ' IN (' . $tempElements . ')';

			$query = implode(' ', $query);

			$db->setQuery($query);
			$state = false;

			if (method_exists($db, 'query')) {
				$state = $db->query();
			} else {
				$state = $db->execute();
			}

		}

		return true;
	}

	/**
	 * Unpublish EasyBlog plugins from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function unPublishPlugins()
	{
		$db = JFactory::getDBO();

		$pluginNames = [
				'easyblog' => ['pagebreak', 'autoarticle'],
				'community' => ['easyblog', 'easyblogtoolbar'],
				'finder' => 'easyblog',
				'phocapdf' => 'easyblog',
				'search' => ['easyblogcomment', 'easyblog'],
				'system' => ['easyblogredirect', 'blogurl', 'eventeasyblog', 'groupeasyblog', 'easyblogcomposer'],
				'user' => 'easyblogusers'
		];

		foreach ($pluginNames as $folder => $elements) {
			$tempElements = '';

			if (is_array($elements)) {
				foreach($elements as $element){
					$tempElements .= ($tempElements) ? ',' . $db->Quote($element) : $db->Quote($element);
				}
			} else {
				$tempElements = $db->Quote($elements);
			}

			// jos_modules
			$query = array();
			$query[] = 'UPDATE ' . $db->quoteName('#__extensions') . ' SET ' . $db->quoteName('enabled') . '=' . $db->Quote('0');
			$query[] = 'WHERE ' . $db->quoteName('folder') . '=' . $db->Quote($folder);
			$query[] = 'AND ' . $db->quoteName('element') . ' IN (' . $tempElements . ')';

			$query = implode(' ', $query);

			$db->setQuery($query);
			$state = false;

			if (method_exists($db, 'query')) {
				$state = $db->query();
			} else {
				$state = $db->execute();
			}

		}

		return true;
	}

	/**
	 * Unpublish EasyBlog modules from the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function unpublishModules()
	{
		// Get a list of all known modules
		$moduleNames = JFolder::folders(JPATH_ROOT . '/modules', 'mod_easyblog*');

		// No modules found. #2789
		if (empty($moduleNames) || !$moduleNames) {
			return true;
		}

		$db = JFactory::getDBO();
		$modules = '';

		foreach ($moduleNames as $module) {
			$modules .= ($modules) ? ',' . $db->Quote($module) : $db->Quote($module);
		}

		// jos_modules
		$query = [];
		$query[] = 'UPDATE ' . $db->quoteName('#__modules') . ' SET ' . $db->quoteName('published') . '=' . $db->Quote('0');
		$query[] = 'WHERE ' . $db->quoteName('module') . ' IN (' . $modules . ')';
		$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote('1');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$state = false;

		if (method_exists($db, 'query')) {
			$state = $db->query();
		} else {
			$state = $db->execute();
		}

		return true;
	}
}
