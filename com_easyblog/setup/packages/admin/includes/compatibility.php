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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Environment\Browser;

class EBUtility {

	/**
	 * Retrieves Joomla version
	 *
	 * @since	5.4.6
	 * @access	public
	 */
	public static function getJoomlaVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$jVerArr = explode('.', JVERSION);
			$version = $jVerArr[0] . '.' . $jVerArr[1];
		}

		return $version;
	}

	/**
	 * Determines if the current instance of Joomla is 3.1 and above
	 *
	 * @since	5.4.6
	 * @access	public
	 */
	public static function isJoomla31()
	{
		$state = false;

		if (EBUtility::getJoomlaVersion() >= '3.1' && !EBUtility::isJoomla4()) {
			$state = true;
		}

		return $state;
	}

	/**
	 * Determines if the current Joomla install is J4.0
	 *
	 * @since	5.4.6
	 * @access	public
	 */
	public static function isJoomla4()
	{
		static $isJoomla4 = null;

		if (is_null($isJoomla4)) {
			$currentVersion = EBUtility::getJoomlaVersion();
			$isJoomla4 = version_compare($currentVersion, '4.0') !== -1;

			return $isJoomla4;
		}

		return $isJoomla4;
	}
}

if (EBUtility::isJoomla4()) {
	class EBStringBase extends Joomla\String\StringHelper {}
}

if (!EBUtility::isJoomla4()) {
	class EBStringBase extends JString {}
}

class EBString extends EBStringBase
{
	/**
	 * Override str_ireplace function from joomla
	 * Refer this ticket, #1899.
	 *
	 * Since PHP's built-in str_ireplace is binary safe, it is fine to use with UTF-8 characters
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public static function str_ireplace($search, $replace, $subject, $count = null)
	{
		return str_ireplace($search, $replace, $subject, $count);
	}
}

class EBFactory
{
	/**
	 * Returns a query variable by name.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getURI($requestPath = false)
	{
		$uri = JUri::getInstance();

		// Gets the full request path.
		if ($requestPath) {
			$uri = $uri->toString(array('path', 'query'));
		}

		return $uri;
	}

	/**
	 * Render Joomla editor.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getEditor($editorType = null)
	{
		if (!$editorType) {

			$config = EB::config();
			$jConfig = EB::jConfig();

			// If use system editor, we should check if the configured editor exists or enabled.
			$editorType = $config->get('layout_editor');

			// if use build-in composer, we should check from the global configuration setting
			if ($editorType == 'composer') {
				$editorType = $jConfig->get('editor');
			}
		}

		if (EBUtility::isJoomla4()) {
			$editor = Joomla\CMS\Editor\Editor::getInstance($editorType);
		} else {
			$editor = JFactory::getEditor($editorType);

			if ($editorType == 'none') {
				JHtml::_('behavior.core');
			}
		}

		return $editor;
	}

	/**
	 * Returns a query variable by name.
	 *
	 * @since   5.4.6
	 * @access  public
	 */
	public static function getApplication()
	{
		if (EBUtility::isJoomla31()) {
			$app = JFactory::getApplication();
		}

		if (EBUtility::isJoomla4()) {
			$app = Joomla\CMS\Factory::getApplication();
		}

		return $app;
	}

	/**
	 * Returns a query variable by name.
	 *
	 * @since   5.4.6
	 * @access  public
	 */
	public static function isCli()
	{
		$app = self::getApplication();
		if (method_exists($app, 'isCli')) {
			return $app->isCli();
		}

		return false;
	}

}

class EBUserModel
{
	/**
	 * Load joomla's user forms
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function loadUserModel()
	{
		if (EBUtility::isJoomla31()) {
			require_once(JPATH_ADMINISTRATOR . '/components/com_users/models/user.php');
			$userModel = new UsersModelUser();
		}

		if (EBUtility::isJoomla4()) {
			$userModel = new Joomla\Component\Users\Administrator\Model\UserModel();
		}

		return $userModel;
	}
}

class EBArchive
{
	/**
	 * Load Joomla's Archive
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public static function load()
	{
		if (FH::isJoomla4()) {
			$archive = new Joomla\Archive\Archive();

			return $archive;
		}

		$archive = new JArchive();

		return $archive;
	}

	/**
	 * Perform extract method from Joomla Archive
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function extract($destination, $extracted)
	{
		$archive = self::load();

		if (!FH::isJoomla4()) {
			$state = $archive::extract($destination, $extracted);

			return $state;
		}

		$state = $archive->extract($destination, $extracted);

		return $state;
	}

	/**
	 * Get a file compression adapter from Joomla Archive
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function getAdapter($type)
	{
		$archive = self::load();

		if (!FH::isJoomla4()) {
			$adapter = $archive::getAdapter($type);

			return $adapter;
		}

		$adapter = $archive->getAdapter($type);

		return $adapter;
	}
}


class EBArrayHelper
{
	/**
	 * Utility function to map an object to an array
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function fromObject($data)
	 {
		if (EBUtility::isJoomla31()) {
			$data = JArrayHelper::fromObject($data);
		}

		if (EBUtility::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::fromObject($data);
		}

		return $data;
	 }

	/**
	 * Utility function to return a value from a named array or a specified default
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getValue($array, $name, $default = null, $type = '')
	{
		if (EBUtility::isJoomla31()) {
			$data = JArrayHelper::getValue($array, $name, $default, $type);
		}

		if (EBUtility::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::getValue($array, $name, $default, $type);
		}

		return $data;
	}
}

class EBRouter
{
	/**
	 * Determine whether the site enable SEF.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getMode()
	{
		static $mode = null;

		if (is_null($mode)) {
			$jConfig = EB::jConfig();
			$mode = $jConfig->get('sef');

			if (EB::isFromAdmin()) {
				$mode = false;
			}
		}

		return $mode;
	}
}

use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\Plugin\PluginHelper;

class EBCompat
{
	public static function getTwoFactorForms($otpConfig, $userId = null)
	{
		if (EBUtility::isJoomla4()) {
			$app = JFactory::getApplication();
			$model = $app->bootComponent('com_users')->getMVCFactory()->createModel('User', 'Administrator');
			$otpConfig = $model->getOtpConfig($userId);

			PluginHelper::importPlugin('twofactorauth');

			return $app->triggerEvent('onUserTwofactorShowConfiguration', array($otpConfig, $userId));
		}

		FOFPlatform::getInstance()->importPlugin('twofactorauth');

		$userId = JFactory::getUser($userId)->id;

		$contents = FOFPlatform::getInstance()->runPlugins('onUserTwofactorShowConfiguration', array($otpConfig, $userId));

		return $contents;
	}

	/**
	 * Retrieve Joomla's browser
	 *
	 * @since	5.4.3
	 * @access	public
	 */
	public static function getNavigator()
	{
		$navigator = null;

		if (is_null($navigator)) {

			if (EBUtility::isJoomla4()) {
				$navigator = Browser::getInstance();

				return $navigator;
			}

			$navigator = JBrowser::getInstance();
		}

		return $navigator;
	}

	/**
	 * Determines if the user is viewing the admin page
	 *
	 * @since	5.4.3
	 * @access	public
	 */
	public static function isAdmin()
	{
		if (EBUtility::isJoomla4()) {
			$app = JFactory::getApplication();
			$admin = $app->isClient('administrator');

			return $admin;
		}

		$app = JFactory::getApplication();
		$admin = $app->isAdmin();

		return $admin;
	}

	/**
	 * Renders color picker library from Joomla
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public static function renderColorPicker()
	{
		if (EBUtility::isJoomla4()) {
			HTMLHelper::_('jquery.framework');
			HTMLHelper::_('script', 'vendor/minicolors/jquery.minicolors.min.js', array('version' => 'auto', 'relative' => true));
			HTMLHelper::_('stylesheet', 'vendor/minicolors/jquery.minicolors.css', array('version' => 'auto', 'relative' => true));
			HTMLHelper::_('script', 'system/fields/color-field-adv-init.min.js', array('version' => 'auto', 'relative' => true));
			return;
		}

		JHTML::_('behavior.colorpicker');
	}

	/**
	 * Abstract method to load jQuery from Joomla
	 * 
	 * NOTE: Use FH::renderjQueryFramework() directly as this will be deprecated
	 *
	 * @since	5.4.3
	 * @access	public
	 */
	public static function renderjQueryFramework()
	{
		FH::renderjQueryFramework();
	}

	/**
	 * Generates the modal html that should be used
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public static function renderModalHtml()
	{

	}

	/**
	 * Renders modal library from Joomla
	 *
	 * NOTE: Use FH::renderModalLibrary() directly as this will be deprecated
	 * 
	 * @since	5.4.0
	 * @access	public
	 */
	public static function renderModalLibrary()
	{
		FH::renderModalLibrary();
	}
}

class EBPluginHelper
{
	/**
	 * Load all the plugin.
	 *
	 * @since   5.4.6
	 * @access  public
	 */
	public static function importPlugin($type, $plugin = null, $autocreate = true, DispatcherInterface $dispatcher = null)
	{
		if (EBUtility::isJoomla31()) {
			$data = JPluginHelper::importPlugin($type);
		}

		if (EBUtility::isJoomla4()) {
			$data = Joomla\CMS\Plugin\PluginHelper::importPlugin($type);
		}

		return $data;
	 }
}

class EBComponentHelper
{
	/**
	 * Checks if the component is enabled
	 *
	 * @since   5.4.6
	 * @access  public
	 */
	public static function isEnabled($option)
	{
		if (EBUtility::isJoomla31()) {
			jimport('joomla.application.component.helper');
			$isEnabled = JComponentHelper::isEnabled($option);
		}

		if (EBUtility::isJoomla4()) {
			$isEnabled = Joomla\CMS\Component\ComponentHelper::isEnabled($option);
		}

		return $isEnabled;
	 }
}

class EBFinderHelper
{
	/**
	 * Method to get extra data for a content before being indexed.
	 *
	 * @since   5.4.6
	 * @access  public
	 */
	public static function getContentExtras($item)
	{
		if (EBUtility::isJoomla31()) {
			require_once(JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php');
			$data = FinderIndexerHelper::getContentExtras($item);
		}

		if (EBUtility::isJoomla4()) {
			$data = Joomla\Component\Finder\Administrator\Indexer\Helper::getContentExtras($item);
		}

		return $data;
	 }
}

use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Result;

if (EBUtility::isJoomla4()) {
	class EBFinderBase extends Adapter{

	 	protected function index(Result $item)
		{
			$data = $this->proxyIndex($item);
			return $data;
		}

		protected function setup()
		{
			return parent::setup();
		}
	}
}

if (!EBUtility::isJoomla4()) {
	require_once(JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php');

	class EBFinderBase extends FinderIndexerAdapter{

		protected function index(FinderIndexerResult $item, $format = 'html')
		{
			$data = $this->proxyIndex($item, $format);
			return $data;
		}

		protected function setup()
		{
			return parent::setup();
		}
	}

}
