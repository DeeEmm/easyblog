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

require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/utils.php');

if (!defined('EASYBLOG_COMPONENT_CLI')) {
	require_once(__DIR__ . '/compatibility.php');
	require_once(__DIR__ . '/router.php');
	require_once(EBLOG_ROOT . '/router.php');
}

use Joomla\CMS\Language\LanguageHelper;
use Foundry\Libraries\Scripts;

class EB
{
	public static $headersLoaded = [];
	private static $categoryTheme = null;

	public static function fd()
	{
		static $fd = null;

		if (is_null($fd)) {
			EB::initFoundry();

			$fd = new FoundryLibrary('com_easyblog', 'eb', 'EasyBlog');
		}

		return $fd;
	}

	/**
	 * Initializes Foundry
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function initFoundry()
	{
		require_once(JPATH_LIBRARIES . '/foundry/foundry.php');
	}

	/**
	 * Check if foundry plugin enabled or not.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function isFoundryEnabled()
	{
		static $isEnabled = null;

		if (is_null($isEnabled)) {

			$isEnabled = true;

			if (!JFile::exists(JPATH_LIBRARIES . '/foundry/foundry.php')) {
				$isEnabled = false;
			}

			// TODO: check if the foundry plugin enabled or not.
			if (!JPluginHelper::isEnabled('system', 'foundry')) {
				$isEnabled = false;
			}
		}

		// passed. do nothing.
		return $isEnabled;
	}

	/**
	 * Method to display Joomla's core alert
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function raiseWarning($errCode, $msg)
	{
		if (class_exists('JError')) {
			return JError::raiseWarning($errCode, JText::_($msg));
		}

		return JFactory::getApplication()->enqueueMessage(JText::_($msg), 'error');
	}

	/**
	 * Renders the autoload file for other libraries from composer
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function autoload()
	{
		return EB::fd()->autoload();
	}

	/**
	 * Initializes EasyBlog's javascript framework
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function init($section = 'site')
	{
		// Determines if we should compile the javascripts on the site
		$app = JFactory::getApplication();
		$input = $app->input;
		$compile = $input->get('compile', false, 'bool');

		if (FH::isSiteAdmin() && $compile) {

			// Determines if we need to minify the js
			$minify = $input->get('minify', false, 'bool');

			// Get section if not default one
			$section = $input->get('section', $section, 'cmd');

			// Get the compiler
			$compiler = EB::compiler();
			$result = array();

			// Compile with jquery.easyblog.js
			$result['standard'] = $compiler->compile($section, $minify);

			// Compile with jquery.js
			$result['basic'] = $compiler->compile($section, $minify, false);

			if ($result !== false) {
				header('Content-type: text/x-json; UTF-8');
				echo json_encode($result);
				exit;
			}
		}

		// If this is a non html view, skip this altogether
		$doc = JFactory::getDocument();

		if ($doc->getType() !=='html') {
			return;
		}

		if (!isset(self::$headersLoaded[$section])) {

			// Load scripts from foundry
			Scripts::init();

			// Attach scripts on the page
			$scripts = EB::scripts($section);
			$scripts->attach();

			self::$headersLoaded[$section] = true;
		}

		return self::$headersLoaded[$section];
	}

	/**
	 * Custom implementation of EasySocial integrations
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function easysocial()
	{
		static $easysocial = null;

		if (is_null($easysocial)) {
			self::load(__FUNCTION__);

			$easysocial = new EasyBlogEasySocial();
		}

		return $easysocial;
	}

	/**
	 * Custom implementation of EasyDiscuss integrations
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function easydiscuss()
	{
		static $easydiscuss = null;

		if (is_null($easydiscuss)) {
			self::load(__FUNCTION__);

			$easydiscuss = new EasyBlogEasyDiscuss();
		}

		return $easydiscuss;
	}

	/**
	 * If the current user is a super admin, allow them to change the environment via the query string
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function checkEnvironment()
	{
		if (!FH::isSiteAdmin()) {
			return;
		}

		$app = JFactory::getApplication();
		$environment = $app->input->get('eb_env', '', 'word');
		$allowed = array('production', 'development');

		// Nothing has changed
		if (!$environment || !in_array($environment, $allowed)) {
			return;
		}

		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/defaults/configuration.ini';
		$contents = file_get_contents($file);
		$contents = preg_replace('/main_environment=(.*)/', 'main_environment=' . $environment, $contents);

		JFile::write($file, $contents);

		// We also need to update the database value
		$config = EB::table('Configs');
		$config->load(array('name' => 'config'));

		$params = $config->getParams();
		$params->set('main_environment', $environment);

		$config->params = $params->toString();
		$config->store();

		EB::info()->set('Updated system environment to <b>' . $environment . '</b> mode', 'success');
		return $app->redirect('index.php?option=com_easyblog');
	}

	/**
	 * Retrieves the cdn url for the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getCdnUrl()
	{
		$config = EB::config();
		$url = $config->get('cdn_url');

		if (!defined('EASYBLOG_COMPONENT_CLI')) {
			$app = JFactory::getApplication();

			// We do not want to render cdn urls for admins
			if (EB::isFromAdmin()) {
				return false;
			}
		}

		if (!$url) {
			return false;
		}

		// Ensure that the url contains http:// or https://
		if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
			$url = '//' . $url;
		}

		return $url;
	}

	/**
	 * Load EasyBlog's ACL
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function acl($userId = '')
	{
		static $acl = [];

		if (!$userId) {
			$userId = JFactory::getuser()->id;
		}

		if (!isset($acl[$userId])) {

			require_once(dirname(__FILE__) . '/acl/acl.php');

			$acl[$userId] = EasyBlogAcl::getRuleSet($userId);
		}

		return $acl[$userId];
	}

	/**
	 * Deprecated. Use FH::minifyCss($css)
	 *
	 * @deprecated	6.0.0
	 */
	public static function minifyCSS($css)
	{
		return FH::minifyCss($css);
	}

	/**
	 * Proxy for media manager
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function mediamanager($source = EBLOG_MEDIA_SOURCE_LOCAL)
	{
		require_once(dirname(__FILE__) . '/mediamanager/mediamanager.php');

		$media = new EBMM($source);

		return $media;
	}

	/**
	 * Creates a new stylesheet instance
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function stylesheet($location, $name = null, $useOverride = false)
	{
		static $stylesheet = [];

		if (!isset($stylesheet[$location][$name])) {
			require_once(__DIR__ . '/stylesheet/stylesheet.php');

			$stylesheet[$location][$name] = new EasyBlogStylesheet($location, $name, $useOverride);
		}

		return $stylesheet[$location][$name];
	}

	/**
	 * Creates a new instance of the exception library.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function exception($message = '', $type = EASYBLOG_MSG_ERROR, $silent = false, $customErrorCode = null)
	{
		require_once(dirname(__FILE__) . '/exception/exception.php');

		$exception = new EasyBlogException($message, $type, $silent, $customErrorCode);

		return $exception;
	}

	/**
	 * Deprecated since EB 6.0 as we no longer use it
	 *
	 * @deprecated	6.0
	 */
	public static function isBloggerMode()
	{
		return false;
	}

	/**
	 * Deprecated. Use FH::getCurrentLanguageTag()
	 *
	 * @deprecated	6.0.0
	 */
	public static function getLanguageTag()
	{
		return FH::getCurrentLanguageTag();
	}

	/**
	 * Deprecated. Use FH::getJoomlaLanguages()
	 *
	 * @deprecated	6.0.0
	 */
	public static function getJoomlaLanguages()
	{
		return FH::getJoomlaLanguages();
	}

	/**
	 * Deprecated. Use FH::getLanguages()
	 *
	 * @deprecated	6.0.0
	 */
	public static function getLanguages($selected = '', $subname = true)
	{
		return FH::getLanguages($selected, $subname);
	}

	/**
	 * Retrieve the current language tag set
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getMomentLanguage()
	{
		static $tag = false;

		if (!$tag) {
			$tag = JFactory::getLanguage()->getTag();

			if ($tag == 'zh-TW') {
				$tag = 'zh-tw';
			} else {
				$tag = explode('-', $tag);

				$tag = $tag[0];

				if ($tag == 'en') {
					$tag = 'en-gb';
				}
			}

		}

		return $tag;
	}

	/**
	 * Deprecated. Use FH::isMultiLingual
	 *
	 * @deprecated	6.0.0
	 */
	public static function isMultiLingual()
	{
		return FH::isMultiLingual();
	}


	/**
	 * Proxy for post library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function reactions(EasyBlogPost $post)
	{
		require_once(__DIR__ . '/reactions/reactions.php');

		$lib = new EasyBlogReactions($post);

		return $lib;
	}

	/**
	 * Render the modules library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function modules($module)
	{
		require_once(__DIR__ . '/modules/modules.php');

		$lib = new EasyBlogModules($module);
		return $lib;
	}

	/**
	 * Render the scripts library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function scripts($location = 'site')
	{
		require_once(__DIR__ . '/scripts/scripts.php');

		$lib = new EasyBlogScripts($location);
		return $lib;
	}

	/**
	 * Get's the database object.
	 *
	 * @since	3.7
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function db()
	{
		static $db = null;

		if (!$db) {
			$db = new EasyBlogDbJoomla();
		}

		return $db;
	}

	/**
	 * Loads the date helper object
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function date($current = '', $offset = null)
	{
		return new \Foundry\Libraries\Date($current, $offset);
	}

	/**
	 * Load's EasyBlog's settings object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function config()
	{
		static $config	= null;

		if (is_null($config)) {

			// Load up default configuration file
			$file = EBLOG_ADMIN_ROOT . '/defaults/configuration.ini';
			$raw = file_get_contents($file);

			$registry = new JRegistry($raw);

			if (!defined('EASYBLOG_COMPONENT_CLI')) {
				// Get config stored in db
				$table = EB::table('Configs');
				$table->load('config');

				// Load the stored config as a registry
				$stored	= new JRegistry($table->params);

				$registry->merge($stored);

				if (!$stored->get('main_blocked_words')) {
					$registry->set('main_blocked_words', '');
				}

				// Get any config from current active menu item and override it
				if (!EBFactory::isCli()) {
					$menu = JFactory::getApplication()->getMenu()->getActive();
					if ($menu) {
						$params = $menu->getParams();

						$properties = $params->toArray();

						foreach ($properties as $key => $val) {

							// Get the prefix of the params
							$subKey = explode('_', $key);

							// anything start with ebconfig_ is consider as global config.
							// eg : ebconfig_main_title
							if ($subKey[0] == 'ebconfig') {

								// Value is not inherited from settings
								if ($val != '-1') {

									// Remove the prefix
									array_shift($subKey);

									// Glue back the param name
									$name = implode('_', $subKey);

									// Re-assign the override value to the global config
									$registry->set($name, $val);
								}
							}
						}
					}
				}
			}

			$config = $registry;
		}

		return $config;
	}

	/**
	 * Renders the info object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function info()
	{
		static $instance = null;

		require_once(dirname(__FILE__) . '/info/info.php');

		if (is_null($instance)) {

			$info = new EasyBlogInfo();

			$instance = $info;
		}

		return $instance;
	}

	/**
	 * Renders Joomla's configuration object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function jconfig()
	{
		return FH::jconfig();
	}

	/**
	 * Returns a new registry object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function registry($contents = '', $isFile = false)
	{
		$registry = new JRegistry($contents, $isFile);

		return $registry;
	}

	/**
	 * Retrieves the token
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function token()
	{
		return FH::token();
	}

	/**
	 * Central method to retrieve the post styles from the params
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function getPostStyles($params)
	{
		$style = (object) [
			'row' => 'row',
			'column' => 'grid',
			'post' => 'default',
			'columns' => 3
		];

		$config = EB::config();
		$currentTheme = $config->get('theme_site');

		if ($currentTheme === 'nickel') {
			$config = EB::config();

			$style->row = 'column';
			$style->column = 'masonry';
			$style->post = 'nickel';
			$style->columns = (int) $params->get('post_nickel_column', $config->get('listing_post_nickel_column'));

			if ($style->columns === 1) {
				$style->row = 'row';
				$style->column = 'grid';
			}

			return $style;
		}

		if ($config->get('theme_site') !== 'wireframe') {
			return $style;
		}

		$style->row = $params->get('row_style', 'row');
		$style->column = $params->get('column_style', 'grid');
		$style->post = $params->get('layout_style', 'default');
		$style->columns = $params->get('card_column', 3);

		return $style;
	}

	/**
	 * Retrieves the meta key for the current operating system
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function getMetaKey()
	{
		static $key = null;

		if (is_null($key)) {
			// Get current viewer's operating system
			$navigator = EBCompat::getNavigator();
			$platform = $navigator->getPlatform();

			$key = $platform == 'mac' ? '⌘' : 'ctrl';
		}

		return $key;
	}

	/**
	 * Loads a library dynamically
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function __callStatic($method, $arguments)
	{
		self::load($method);

		$class = 'EasyBlog' . ucfirst($method);

		$obj = new $class($arguments);

		return $obj;
	}

	/**
	 * Single point of entry for static calls.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function call($className , $method , $args = array() )
	{
		// We always want lowercased items.
		$item			= strtolower($className);
		$obj			= false;

		$path			= dirname(__FILE__) . '/' . $item . '/' . $item . '.php';

		require_once( $path );

		$class 	= 'EasyBlog' . ucfirst( $className );

		if (!class_exists($class)) {
			return false;
		}

		if (!method_exists($class, $method)) {
			return false;
		}

		return call_user_func_array(array($class, $method), $args);
	}

	/**
	 * Retrieves the blog image object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function blogimage($path, $uri, $storageType = 'joomla')
	{
		require_once(dirname(__FILE__) . '/blogimage/blogimage.php');

		$image = new EasyBlogBlogImage($path, $uri, $storageType);

		return $image;
	}

	/**
	 * Retrieves the category library
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function category()
	{
		require_once(dirname(__FILE__) . '/category/category.php');

		$category 	= new EasyBlogCategory();

		return $category;
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @since	4.0
	 * @access	public
	 **/
	public static function helper($helper)
	{
		static $obj	= array();

		if (!isset($obj[$helper])) {

			$file = dirname(__FILE__) . '/' . strtolower($helper) . '/' . strtolower($helper) . '.php';

			require_once($file);
			$class = 'EasyBlog' . ucfirst($helper);

			$obj[$helper] = new $class();
		}

		return $obj[$helper];
	}

	/**
	 * Retrieve the limit defined for each views settings given from specific key
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function getViewLimit($name = 'limit', $view = 'listing')
	{
		$app = JFactory::getApplication();
		$config = EB::config();

		$active = $app->getMenu()->getActive();
		$params = null;

		if ($active) {
			$params = $active->getParams();
		}

		$limit = -1;

		// Check if menu item is exists
		if (is_object($active)) {
			$limit = $params->get($name, '-1');
		}

		// Inherit from default view settings
		if ($limit == '-1') {
			$key = $view . '_' . $name;
			$limit = $config->get($key, '-2');
		}

		// Inherit from EB global settings
		if ($limit == '-2') {
			$key = 'layout_listlength';
			$limit = $config->get($key);
		}

		// Inherit from Joomla itself
		if ($limit == '-3' || !$limit) {
			$limit = EB::jconfig()->get('list_limit');
		}

		if ($limit == '-4' && $params) {
			$limit = (int) $params->get('specifylimit', 10);
			if ($limit < 1) {
				$limit = 10;
			}
		}

		// if the limit is still negative value, then we need to return
		// a correct value
		if ($limit < 0) {
			$limit = 10;
		}

		return $limit;
	}

	/**
	 * Retrieve the limit defined in the settings given the specific key
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getLimit($key = 'listlength')
	{
		$app = JFactory::getApplication();

		// Get the default limit
		$default = EB::jconfig()->get('list_limit');

		if (EB::isFromAdmin()) {
			return $default;
		}

		// Get the current active menu
		$active = $app->getMenu()->getActive();
		$limit  = -2;

		if (is_object($active)) {
			$params = $active->getParams();
			$limit = $params->get('limit', '-2');
		}

		// if menu did not specify the limit, then we use easyblog setting.
		if ($limit == '-2') {

			// Use default configurations.
			$config = EB::config();

			// @rule: For compatibility between older versions
			if ($key == 'listlength') {
				$key = 'layout_listlength';
			} else {
				$key = 'layout_pagination_' . $key;
			}

			$limit = $config->get($key);
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if( $limit == '0' || $limit == '-1' || $limit == '-2' || $limit == '-3' || $limit == '-4') {

			return $default;
		}

		return $limit;
	}

	/**
	 * Pagination for EasyBlog
	 *
	 * @since	1.2
	 * @access	public
	 */
	public static function pagination($total, $limitstart, $limit, $prefix = '')
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		$signature = serialize(array($total, $limitstart, $limit, $prefix));

		if (empty($instances[$signature])) {

			require_once(dirname(__FILE__) . '/pagination/pagination.php');

			$pagination	= new EasyBlogPagination($total, $limitstart, $limit, $prefix);

			$instances[$signature] = &$pagination;
		}

		return $instances[$signature];
	}

	/**
	 * Retrieves the default placeholder image
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPlaceholderImage($protocol = false, $type = '')
	{
		static $images = [];

		$idx = $type ? $type : 'default';

		if (!isset($images[$idx])) {

			$defaultJoomlaTemplate = EB::getCurrentTemplate();

			$overrideFilename = 'placeholder.png';
			$defaultFilename = 'placeholder-image.png';

			if ($type) {
				$overrideFilename = 'placeholder-' . $type . '.png';
				$defaultFilename = 'placeholder-' . $type . '.png';
			}

			$file = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/images/' . $overrideFilename;

			// Default placeholder image.
			$default = rtrim(JURI::root(), '/') . '/components/com_easyblog/themes/wireframe/images/' . $defaultFilename;

			if (JFile::exists($file)) {
				$default = rtrim(JURI::root(), '/') . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/images/' . $overrideFilename;
			}

			$images[$idx] = $default;
		}

		return $images[$idx];
	}

	/**
	 * Retrieves the default AMP placeholder image
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function getAmpPlaceholderImage()
	{
		static $ampImage = null;

		if (is_null($ampImage)) {

			$defaultJoomlaTemplate = EB::getCurrentTemplate();
			$overridePath = '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/images/amp-placeholder.png';

			// Default amp placeholder image.
			$default = rtrim(JURI::root(), '/') . '/components/com_easyblog/themes/wireframe/images/amp-placeholder-image.png';

			if (JFile::exists(JPATH_ROOT . $overridePath)) {
				$default = rtrim(JURI::root(), '/') . $overridePath;
			}

			$ampImage = $default;
		}

		return $ampImage;
	}

	/**
	 * Retrieves local version
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getLocalParser()
	{
		static $parser = null;

		if (is_null($parser)) {
			$contents = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_easyblog/easyblog.xml');
			$parser = EB::getXml($contents, false);
		}

		return $parser;
	}

	/**
	 * Retrieves the installed version of EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getLocalVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$parser	= EB::getLocalParser();

			if (!$parser) {
				$version = false;
				return $version;
			}

			$version = (string) $parser->version;
		}

		return $version;
	}

	/**
	 * Method to retrieve a table object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function table($name)
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/tables/table.php');

		$table = EasyBlogTable::getInstance($name);

		return $table;
	}


	/**
	 * Method to retrieve a EasyBlogUser object
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function user( $ids = null , $debug = false )
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/user/user.php';
		include_once($path);

		return EasyBlogUser::factory($ids, $debug);
	}


	/**
	 * Load the request library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function request()
	{
		require_once(dirname(__FILE__) . '/request/request.php');

		$request 	= new EasyBlogRequest();

		return $request;
	}

	/**
	 * Method to retrieve a model
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function model($name)
	{
		static $models = array();

		$index = $name;

		if (!isset($models[$index])) {

			// Include the base model
			require_once(EB_MODELS . '/model.php');

			$file = strtolower($name);
			$path = JPATH_ROOT . '/administrator/components/com_easyblog/models/' . $file . '.php';

			require_once($path);

			$class = 'EasyBlogModel' . ucfirst($name);

			$config = [
				'fd' => EB::fd()
			];

			$obj = new $class($config);
			$models[$index] = $obj;
		}

		return $models[$index];
	}

	/**
	 * Deprecated. Use EB::config
	 *
	 * @deprecated 	4.0
	 */
	public static function getConfig()
	{
		return EB::config();
	}

	/**
	 * Determines if the user is logged into the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function isLoggedIn()
	{
		return FH::isLoggedIn();
	}

	/**
	 * Use FH::getSiteName() instead
	 *
	 * @deprecated	6.0.0
	 */
	public static function showSiteName()
	{
		return FH::getSiteName();
	}

	/**
	 * Use FH::isFromAdmin() instead
	 *
	 * @deprecated	6.0.0
	 */
	public static function isFromAdmin()
	{
		return FH::isFromAdmin();
	}

	/**
	 * Use FH::isSiteAdmin() instead
	 *
	 * @deprecated	6.0.0
	 */
	public static function isSiteAdmin($id = null)
	{
		return FH::isSiteAdmin($id);
	}

	/**
	 * Determines if the user is subscribed to any part of the notification in EasyBlog
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function getSubscription($email, $type = EBLOG_SUBSCRIPTION_SITE, $uid = null)
	{
		static $cache = [];

		$index = md5($email . $uid . $type);

		if (!isset($cache[$index])) {
			$options = [
				'email' => $email,
				'utype' => 'site'
			];

			if ($type !== EBLOG_SUBSCRIPTION_SITE) {
				$options['uid'] = $uid;
			}

			$cache[$index] = EB::table('Subscriptions');

			if ($email) {
				$cache[$index]->load($options);
			}
		}

		return $cache[$index];
	}

	/**
	 * Determines if the user is a team admin
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isTeamAdmin()
	{
		static $admins = array();

		$my	= JFactory::getUser();

		if ($my->guest) {
			return false;
		}

		if (FH::isSiteAdmin()) {
			return true;
		}

		if (!isset($admins[$my->id])) {

			$model = EB::model('TeamBlogs');
			$admins[$my->id] = $model->checkIsTeamAdmin($my->id);
		}

		return $admins[$my->id];
	}

	/**
	 * Use EB::gallery()->removeGalleryCodes($text) instead
	 *
	 * @deprecated	5.1
	 */
	public static function removeGallery($text)
	{
		return EB::gallery()->removeGalleryCodes($text);
	}

	/**
	 * Verifies the password for a post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function verifyBlogPassword($crypt, $id)
	{
		if (!empty($crypt) && !empty($id)) {
			$session = JFactory::getSession();
			$password = $session->get('PROTECTEDBLOG_'.$id, '', 'EASYBLOG');

			if ($crypt == $password) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Formats microblog posts. Use EB::quickpost()->getAdapter('source')->format($blog);
	 *
	 * @deprecated	4.0
	 */
	public static function formatMicroblog(&$row)
	{
		$adapter = EB::quickpost()->getAdapter($row->posttype);

		if ($adapter === false) {
			return;
		}

		$adapter->format($row);
	}

	/**
	 * This method searches for built in tags and strips them off. This should only be used
	 * when you are trying to output some data that doesn't contain html tags.
	 */
	public static function stripEmbedTags( $content )
	{
		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content	= str_ireplace( '&quot;' , '"' , $content );

		$pattern	= array('/\{video:.*?\}/',
							'/\{"video":.*?\}/',
							'/\[embed=.*?\].*?\[\/embed\]/'
							);

		$replace    = array('','','');


		return preg_replace( $pattern , $replace , $content );
	}

	/**
	 * Requires the user to be logged in
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function requireLogin()
	{
		$my = JFactory::getUser();
		$app = JFactory::getApplication();

		if ($my->guest || $my->block) {

			$config = EB::config();
			$provider = $config->get('main_login_provider');

			// Get the current URI which you trying to access
			$currentUri = EBR::getCurrentURI();
			$returnURL = '?return=' . base64_encode($currentUri);

			$url = EB::_('index.php?option=com_easyblog&view=login', false) . $returnURL;

			if ($provider == 'easysocial' && EB::easysocial()->exists()) {
				$url = ESR::login(array(), false) . $returnURL;
			}

			return $app->redirect($url);
		}
	}

	/**
	 * Trigger Joomla events
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function triggerEvent( $event , &$row , &$params , $limitstart )
	{
		$dispatcher = EB::dispatcher();
		$events = array(
							'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
							'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
							'easyblog.commentCount'		=> 'onGetCommentCount',
							'prepareContent'			=> 'onContentPrepare',
							'afterDisplayTitle'			=> 'onContentAfterTitle',
							'beforeDisplayContent'		=> 'onContentBeforeDisplay',
							'afterDisplayContent'		=> 'onContentAfterDisplay',
							'beforeSave'				=> 'onContentBeforeSave'
					);

		// Need to make this behave like how Joomla category behaves.
		if (!isset($row->catid)) {
			$row->catid	= $row->category_id;
		}

		// In the event that there is no such triggers, skip this
		if (!isset($events[$event])) {
			return false;
		}

		$result = $dispatcher->trigger($events[$event] , array('easyblog.blog', &$row, &$params, $limitstart));

		// Remove unwanted fields.
		unset($row->catid);

		return $result;
	}

	/**
	 * Retrieves plain links (Non SEF)
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getExternalLink($link, $xhtml = false)
	{
		$uri	= JURI::getInstance();
		$domain	= $uri->toString( array('scheme', 'host', 'port'));

		return $domain . '/' . ltrim(EBR::_( $link, $xhtml , null, true ), '/');
	}

	/**
	 *
	 *
	 * @deprecated	4.0
	 * @access	public
	 */
	public static function isFeatured($type, $id)
	{
		$model 	= EB::model('Featured');
		return $model->isFeatured($type, $id);
	}

	/**
	 * Standard method to process uploaded avatars
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function uploadMediaAvatar($mediaType, $mediaTable, $isFromBackend = false, $inputName = 'Filedata')
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		$config = EB::config();
		$acl = EB::acl();


		// required params
		$layout_type = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$view_type = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$default_avatar_type = ($mediaType == 'category') ? 'default_category.png' : 'default_team.png';

		if (!$isFromBackend && $mediaType == 'category' && !$acl->get('upload_cavatar')) {
			$url = 'index.php?option=com_easyblog&view=dashboard&layout='.$layout_type;
			EB::info()->set( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
			return $mainframe->redirect(EBR::_($url, false));
		}

		$avatar_config_path	= ($mediaType == 'category') ? $config->get('main_categoryavatarpath') : $config->get('main_teamavatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');

		$upload_path = JPATH_ROOT . '/' . $avatar_config_path;
		$rel_upload_path = $avatar_config_path;

		$err = null;
		$file = EB::request()->files->get($inputName, '');

		// Check whether the upload folder exist or not. if not create it.
		if (!JFolder::exists($upload_path) && !JFolder::create($upload_path)) {

			// Redirect
			if(! $isFromBackend) {
				EB::info()->set('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER', 'error');
				return $mainframe->redirect( EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false) );
			}

			return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$layout_type, false), JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
		}

		//makesafe on the file
		$file['name'] = $mediaTable->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			$target_file_path = $upload_path;
			$relative_target_file = $rel_upload_path.DIRECTORY_SEPARATOR.$file['name'];
			$target_file = JPath::clean($target_file_path . DIRECTORY_SEPARATOR. JFile::makeSafe($file['name']));

			if (!EB::image()->canUpload($file, $error)) {
				if (!$isFromBackend) {
					EB::info()->set($err, 'error');
					return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}

				return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::_($err), 'error');
			}

			if (0 != (int)$file['error']) {
				if (!$isFromBackend) {
					EB::info()->set($file['error'], 'error');
					return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}

				return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), $file['error'], 'error');
			}

			// Rename the file 1st.
			$oldAvatar	= (empty($mediaTable->avatar)) ? $default_avatar_type : $mediaTable->avatar;
			$tempAvatar	= '';

			if ($oldAvatar != $default_avatar_type) {
				$session = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt = JFile::getExt(JPath::clean($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DIRECTORY_SEPARATOR . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar, $tempAvatar);
			}

			if (JFile::exists($target_file)) {

				//rename back to the previous one.
				if ($oldAvatar != $default_avatar_type) {
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					EB::info()->set(JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
			}

			if (JFolder::exists($target_file)) {

				//rename back to the previous one.
				if ($oldAvatar != $default_avatar_type) {
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					EB::info()->set( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
			}

			$image = EB::imagelib();
			$image->load($file['tmp_name']);

			$width = EBLOG_AVATAR_LARGE_WIDTH;
			$height = EBLOG_AVATAR_LARGE_HEIGHT;

			// Based on this 9511b4c commit, we keep the original size of category avatar
			// But we also need to resize it so that the PNG's transparency is kept. #1288

			// Commenting this out temporary as the avatar is required 1:1 ratio for its latest style #2623
			// if ($mediaType === 'category') {
			// 	$width = $image->getWidth();
			// 	$height = $image->getHeight();
			// }

			$image->resize($width, $height, false);
			$image->save($target_file);

			// Now we update the user avatar. If needed, we remove the old avatar.
			if ($oldAvatar != $default_avatar_type && file_exists($tempAvatar)) {
				JFile::delete($tempAvatar);
			}

			return JFile::makeSafe($file['name']);
		}

		return $default_avatar_type;
	}

	/**
	 * Renders module in a template
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function renderModule($position, $attributes = array(), $content = null)
	{
		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');

		$buffer = '';
		$modules = JModuleHelper::getModules($position);

		// Use a standard module style if no style is provided
		if (!isset($attributes['style'])) {
			$attributes['style']	= 'xhtml';
		}

		foreach ($modules as $module) {
			$theme = EB::themes();

			$theme->set('position', $position);
			$theme->set('output', $renderer->render($module, $attributes, $content));

			$buffer .= $theme->output('site/layout/modules/placeholder');
		}

		return $buffer;
	}

	/**
	 * Loads a library file
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function load($file)
	{
		$file = __DIR__ . '/' . strtolower($file) . '/' . strtolower($file) . '.php';

		require_once($file);

		return true;
	}

	/**
	 * Loads the default languages for EasyBlog
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function loadLanguages($path = JPATH_ROOT)
	{
		return FH::loadLanguage('com_easyblog', $path);
	}

	/**
	 * Deprecated
	 *
	 * @deprecated	5.1
	 */
	public static function loadModuleCss() {}

	/**
	 * Loads the necessary router file for com_content
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public static function loadContentRouter()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			if (FH::isJoomla4()) {
				JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

				$loaded = true;
				return;
			}

			require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Given a username, retrieve the user's id.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getUserId($username)
	{
		$db = EB::db();

		$query	= 'SELECT `id` FROM `#__easyblog_users` WHERE `permalink`=' . $db->Quote($username);
		$db->setQuery( $query );
		$result	= $db->loadResult();

		if (empty($result)) {
			$query	= 'SELECT `id` FROM `#__users` WHERE `username`=' . $db->Quote( $username );
			$db->setQuery( $query );
			$result = $db->loadResult();
		}

		return $result;
	}

	/**
	 * Alternative to setMeta
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function setMetaData($keywords, $description)
	{

		if (!$keywords || !$description) {
			$menu = JFactory::getApplication()->getMenu()->getActive();
			$params = new JRegistry($menu->params);

			if (!$keywords) {
				$keywords = $params->get('menu-meta_keywords', '');
			}

			if (!$description) {
				$description = $params->get('menu-meta_description', '');
			}
		}

		if ($keywords) {
			$doc->setMetadata('keywords', $keywords);
		}

		if ($description) {
			$doc->setMetadata('description', $description);
		}
	}

	/**
	 * Allows caller to set the meta
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public static function setMeta($id, $type, $defaultViewDesc = '', $pagination = null)
	{
		$doc = JFactory::getDocument();
		$config = EB::config();
		$jConfig = EB::jconfig();

		$app = JFactory::getApplication();
		$robotsMenu = '';

		// Try to load the meta for the content
		$meta = EB::table('Meta');
		$meta->load(array('content_id' => $id, 'type' => $type));

		// If the category was created without any meta, we need to automatically fill in the description
		if ($type == META_TYPE_CATEGORY && !$meta->id) {

			$category = '';
			if (EB::cache()->exists($id, 'category')) {
				$category = EB::cache()->get($id, 'category');
			} else {
				$category = EB::table('Category');
				$category->load($id);
			}

			$doc->setMetadata('description', strip_tags($category->description));
		}

		// If the blogger was created, try to get meta from blogger biography/title
		if ($type == META_TYPE_BLOGGER) {

			$author = '';
			if (EB::cache()->exists($id, 'author')) {
				$author = EB::cache()->get($id, 'author');
			} else {
				$author = EB::table('Profile');
				$author->load($id);
			}

			$biography = EB::string()->escape(strip_tags($author->biography));

			$doc->setMetadata('description', $biography);

			if (!empty($author->biography)) {
				$meta->description = $biography;
			}

			if (!$meta->keywords && !empty($author->title)) {
				$meta->keywords = $author->title;
			}
		}

		// Automatically fill meta keywords
		if ($type == META_TYPE_POST && (($config->get('main_meta_autofillkeywords') && empty($meta->keywords) )|| ($config->get( 'main_meta_autofilldescription')))) {

			// Retrieve data from cache
			$post = EB::post();
			$post->load($id);

			$category = $post->getPrimaryCategory();
			$keywords = array($category->getTitle());

			if ($config->get('main_meta_autofillkeywords') && empty($meta->keywords)) {

				$tags = $post->getTags();

				foreach ($tags as $tag) {
					$keywords[] = $tag->getTitle();
				}

				$meta->keywords = implode(',', $keywords);
			}

			// Automatically fill meta description
			if ($config->get('main_meta_autofilldescription') && empty($meta->description)) {

				// Force always truncate the content for the meta truncation to prevent both sentences stick together issue
				$content = $post->getIntro(EASYBLOG_STRIP_TAGS, true);

				// Remove newlines
				$content = str_ireplace("\n", "", $content);
				$content = trim($content);

				// Set description into meta headers
				$meta->description	= EBString::substr($content , 0 , $config->get( 'main_meta_autofilldescription_length'));
				$meta->description	= EB::string()->escape($meta->description);
				$meta->description = str_ireplace('&amp;', '&', $meta->description);
				$meta->description = str_replace('&quot;', '', $meta->description);

			}

			// Remove JFBConnect codes.
			if ($meta->description) {
				$pattern = '/\{JFBCLike(.*)\}/i';
				$meta->description = preg_replace($pattern , '' , $meta->description);
			}
		}

		// Check if the descriptin or keysword still empty or not. if yes, try to get from joomla menu.
		if (empty($meta->description) && empty($meta->keywords)) {
			$active = JFactory::getApplication()->getMenu()->getActive();

			if ($active) {
				$params = $active->getParams();

				$description = $params->get('menu-meta_description', '');
				$keywords = $params->get('menu-meta_keywords', '');
				$robotsMenu = $params->get('robots', $jConfig->get('robots'));

				// Retrieve the original meta robots from SEO listing backend
				$metaIndexingOrig = isset($meta->indexing) ? $meta->indexing : '';

				if (!empty($description) || !empty($keywords)) {
					$meta = new stdClass();
					$meta->description = EB::string()->escape($description);
					$meta->keywords = $keywords;

					// if the menu robots and global setting return to null then fall back to SEO configuration
					if (!$robotsMenu) {
						$meta->indexing = $metaIndexingOrig;
					}

					if ($robotsMenu === null) {
						$meta->indexing = null;
					}
				}
			}
		}

		// Only inject this in frontend and html type doc
		if (!EB::isFromAdmin() && $doc->getType() == 'html') {
			$key = $config->get('facebook_instant_article_id', '');

			if ($key != '') {
				$doc->addCustomTag('<meta property="fb:pages" content="' . $key . '" />');
			}
		}

		if (!$meta) {
			return;
		}

		// If there's no meta description, try to get it from Joomla's settings
		if (!$meta->description && $defaultViewDesc) {

			$globalMetaDesc = EB::jconfig()->get('MetaDesc');

			$meta->description = $defaultViewDesc;

			if ($globalMetaDesc) {
				$meta->description .= ' - ' . $globalMetaDesc;
			}
		}

		// Need to append the pagination number under meta description for prevent duplicate description content
		if ($pagination && is_object($pagination)) {
			$page = $pagination->get('pages.current');

			// Append the current page if necessary
			if ($page > 1 && $meta->description) {
				$paginationTitle = JText::sprintf('COM_EASYBLOG_PAGE_NUMBER', $page) . ' - ';
				$meta->description = $paginationTitle . $meta->description;
			}
		}

		if ($meta->keywords) {
			$doc->setMetadata('keywords', $meta->keywords);
		}

		if ($meta->description) {
			$doc->setMetadata('description', $meta->description);
		}

		// Admin probably disabled indexing
		if (isset($meta->indexing) && !$meta->indexing && $meta->indexing !== null) {
			$doc->setMetadata('robots', 'noindex,follow');

		// If there is active menu and unable to return any data from the meta table
		// Then retrieve the robots data from this active menu item
		} elseif ($robotsMenu && ((isset($meta->id) && !$meta->id) || (!isset($meta->robots)))) {
			$doc->setMetadata('robots', $robotsMenu);
		}
	}

	/**
	 * Deprecated. Use EB::comment()->getLikesAuthors
	 *
	 * @deprecated	5.1
	 */
	public static function getLikesAuthors($contentId, $type, $userId)
	{
		return EB::comment()->getLikesAuthors($contentId, $type, $userId);
	}

	/**
	 * We cannot rely on $app->getTemplate() because we need to explicitly get the current default front end template.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getCurrentTemplate()
	{
		static $template = null;

		if (is_null($template)) {
			$model = EB::model('Themes');
			$template = $model->getDefaultJoomlaTemplate();
		}

		return $template;
	}


	/**
	 * Retrieve easyblog custom themes
	 *
	 * @since 5.1.0
	 * @access public
	 */
	public static function getCustomThemes($template)
	{
		$path = JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/themes';

		if (!JFolder::exists($path)) {
			return false;
		}

		$folders = JFolder::folders($path);

		return $folders;
	}



	/**
	 * Given a page title, this method would try to find any existing menu items that are tied to the current page view.
	 * * If a page title is tied, it will then use the page title defined in the menu.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPageTitle($default = '', $useMenuForTitle = true)
	{
		$config = EB::config();
		$app = JFactory::getApplication();
		$itemid = $app->input->get('Itemid', '');
		$originalTitle = $default;
		$pageTitleSeparator = JText::_('COM_EB_PAGE_TITLE_SEPARATOR');

		// @task: If we can't detect the item id, just return the default page title that was passed in.
		if (!$itemid) {
			return $default;
		}

		// Prepare Joomla's site title if necessary.
		$jConfig = EB::jConfig();
		$addTitle = $jConfig->get('sitename_pagetitles');
		$siteTitle = $jConfig->get('sitename');

		// Only add Joomla's site title if it was configured to.
		if ($addTitle) {
			// If the user does not want to append anything
			if (!$config->get('main_pagetitle_autoappend')) {
				$default = $siteTitle;
			} else {
				if ($addTitle == 1) {
					// There is a case where the site title is same as the $default
					// Entry 1 - My Easyblog - My Easyblog
					if ($siteTitle != $default) {
						$default = $siteTitle . $pageTitleSeparator . $default;
					} else {
						$default = $siteTitle;
					}
				}

				if ($addTitle == 2) {
					$default = $default . $pageTitleSeparator . $siteTitle;
				}
			}
		}

		// @task: Let's find the menu item.
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getItem($itemid);

		// @task: If configured to not append the blog title on the page, do not set any page title.
		if (!$config->get('main_pagetitle_autoappend') && $default == $config->get('main_title')) {
			return $siteTitle;
		}

		// If menu item cannot be found anywhere, just use the default
		if (!$item) {
			return $default;
		}

		// check if the current menu item is belong to current view or not.
		$app = JFactory::getApplication();
		$xQuery = $item->query;
		$currentView = $app->input->get('view', '', 'cmd');

		if (isset($xQuery['option']) && $xQuery['option'] == 'com_easyblog'
			&& isset($xQuery['view']) && $xQuery['view'] == $currentView) {

			// @task: Let's get the page title from the menu.
			$params = $item->getParams();
			$title = $params->get('page_title', '');

			// If a title is found, just use the configured title.
			if ($title && $useMenuForTitle) {
				return $title;
			}

		}

		return $default;
	}

	/**
	 * Generate page title for post entry page
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function getPagePostTitle($postTitle)
	{
		$config = EB::config();
		$jConfig = EB::jConfig();

		$pageTitleSeparator = JText::_('COM_EB_PAGE_TITLE_SEPARATOR');
		$pageTitle = $postTitle;

		// Get the page title
		$title = EB::getPageTitle(JText::_($config->get('main_title')));

		// Prepare Joomla's site title if necessary.
		$jPageTitleFormat = $jConfig->get('sitename_pagetitles');

		if ($config->get('main_pagetitle_autoappend_entry') && $title) {

			$pageTitle = $postTitle . $pageTitleSeparator . $title;

			// after post title render site title
			if ($jPageTitleFormat == 2) {
				$pageTitle = $postTitle . $pageTitleSeparator . $title;
			}

			// before post title render site title
			if ($jPageTitleFormat == 1) {
				$pageTitle = $title . $pageTitleSeparator . $postTitle;
			}
		}

		return $pageTitle;
	}

	// this function used to show the login form
	public static function showLogin($return='')
	{
		$my = JFactory::getUser();

		if ($my->id == 0) {
			$comUserOption	= 'com_users';
			$tasklogin		= 'user.login';
			$tasklogout		= 'user.logout';
			$viewRegister	= 'registration';
			$inputPassword	= 'password';

			if (empty($return)) {
				$currentUri = EBFactory::getURI(true);
				$uri = base64_encode($currentUri);
			} else {
				$uri = $return;
			}

			$theme = EB::themes();
			$theme->set('return', $uri);
			$theme->set('comUserOption', $comUserOption);
			$theme->set('tasklogin', $tasklogin);
			$theme->set('tasklogout', $tasklogout);
			$theme->set('viewRegister', $viewRegister);
			$theme->set('inputPassword', $inputPassword);

			echo $theme->output('site/login/default');
		}
	}

	/**
	 * Generates a html code for category selection in backend
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function populateCategoryFilter($eleName, $catId = '', $attributes = '', $defaultText = 'COM_EASYBLOG_SELECT_A_CATEGORY', $className = '')
	{
		$model = EB::model('Category');
		$categories = $model->generateCategoryFilterList();

		$options = "";

		if ($categories) {

			$selected = !$catId ? ' selected="selected"' : '';
			$options .= '<option value="0"' . $selected . '>' . JText::_($defaultText) . '</option>';


			foreach ($categories as $category) {

				$selected = $category->id == $catId ? ' selected="selected"' : '';

				$space = '';
				$sup = '';

				if ($category->depth > 0) {

					$sup	= '<sup>|_</sup>';

					for ($d = 0; $d < $category->depth; $d++) {
						$space .= '&nbsp;&nbsp;&nbsp;';
					}
				}

				$options .= '<option value="' . $category->id . '"' . $selected . '>' . $space . $sup . JText::_($category->title) . '</option>';
			}
		}

		$html = '';
		$html .= '<select name="' . $eleName . '" id="' . $eleName .'" class="' . $className . '" ' . $attributes . '>';
		$html .= $options;
		$html .= '</select>';

		return $html;
	}

	/*
	 * Generates viewable categories used in search filter
	 *
	 * @since	6.0
	 * @access	public
	 */
	public static function getCategoriesForFilters($parentId = null)
	{
		$catModel = EB::model('Categories');
		$parentCat = $catModel->getCategoriesForFilters($parentId);

		return $parentCat;
	}


	/**
	 * Generates a html code for category selection.
	 *
	 * @access	public
	 */
	public static function populateCategories($parentId, $userId, $outType, $eleName, $default, $isWrite = false, $isPublishedOnly = false, $isFrontendWrite = false, $exclusion = array(), $attributes = '', $defaultText = 'COM_EASYBLOG_SELECT_PARENT_CATEGORY', $customClass = '')
	{
		$catModel = EB::model('Category');
		$parentCat = null;

		if (!empty($userId)) {
			$parentCat = $catModel->getParentCategories($userId, 'blogger', $isPublishedOnly, $isFrontendWrite, $exclusion);
		} else if (!empty($parentId)) {
			$parentCat = $catModel->getParentCategories($parentId, 'category', $isPublishedOnly, $isFrontendWrite, $exclusion);
		} else {
			$parentCat = $catModel->getParentCategories('', 'all', $isPublishedOnly, $isFrontendWrite, $exclusion);
		}

		$ignorePrivate = true;

		if ($outType == 'link') {
			$ignorePrivate = false;
		}

		// Now let's do a loop to find it's child categories.
		if (!empty($parentCat)) {
			for ($i = 0; $i < count($parentCat); $i++) 	{
				$parent =& $parentCat[$i];

				//reset
				$parent->childs = null;

				EB::buildNestedCategories($parent->id, $parent, $ignorePrivate, $isPublishedOnly, $isFrontendWrite, $exclusion);
			}
		}

		if ($isWrite) {
			$defaultCatId = EB::getDefaultCategoryId();
			$default = (empty($default)) ? $defaultCatId : $default;
		}

		$formEle = '';

		if ($outType == 'select' && $isWrite) {
			$selected = !$default ? ' selected="selected"' : '';
			$formEle .= '<option value="0"' . $selected . '>' . JText::_('COM_EASYBLOG_SELECT_A_CATEGORY') . '</option>';
		}

		if (!$isWrite) {
			$formEle .=	'<option value="0">' . JText::_($defaultText) . '</option>';
		}

		if ($parentCat) {
			foreach ($parentCat as $category) {
				if ($outType == 'popup') {
					$formEle .= '<div class="category-list-item" id="'.$category->id.'"><a href="javascript:void(0);" onclick="eblog.dashboard.selectCategory(\''. $category->id . '\')">' . $category->title . '</a>';
					$formEle .= '<input type="hidden" id="category-list-item-'.$category->id.'" value="'.$category->title.'" />';
					$formEle .= '</div>';
				} else {
					$selected = ($category->id == $default) ? ' selected="selected"' : '';
					$formEle .= '<option value="'.$category->id.'" ' . $selected. '>' . JText::_($category->title) . '</option>';
				}

				EB::accessNestedCategories($category, $formEle, '0', $default, $outType);
			}
		}

		$className = 'form-control';

		if ($customClass) {
			$className = $customClass;
		}

		if ($outType == 'select') {

			$includeDivGroup = true;

			// fix uikit themes;
			if ($className == 'uk-select') {
				$includeDivGroup = false;
			}

			$theme = EB::themes();
			$theme->set('name', $eleName);
			$theme->set('formElement', $formEle);
			$theme->set('className', $className);
			$theme->set('attributes', $attributes);
			$theme->set('includeDivGroup', $includeDivGroup);

			return $theme->output('site/helpers/form/categories.select');
		}

		$html = '';
		$html .= '<select name="' . $eleName . '" id="' . $eleName .'" class="' . $className . '" ' . $attributes . '>';
		$html .= $formEle;
		$html .= '</select>';

		return $html;
	}


	public static function buildNestedCategories($parentId, &$parent, $ignorePrivate = false, $isPublishedOnly = false, $isWrite = false, $exclusion = array())
	{
		$my = JFactory::getUser();

		$childs = array();

		//lets try to get from cache if there is any
		if (EB::cache()->exists($parentId, 'cats')) {
			$data = EB::cache()->get($parentId, 'cats');

			if (isset($data['child'])) {
				$childs = $data['child'];
			} else {
				return false;
			}
		} else {
			$catModel = EB::model( 'Categories');
			$childs = $catModel->getChildCategories($parentId, $isPublishedOnly, $isWrite , $exclusion );
		}

		if (!$childs) {
			return false;
		}

		$items = array();

		foreach($childs as $child) {
			$items[$child->id] = $child;
		}

		$parent->childs = array();

		$catLib = EB::category();
		$catLib::addChilds($parent, $items);

		return false;
	}


	public static function accessNestedCategories($arr, &$html, $deep='0', $default='0', $type='select', $linkDelimiter = '')
	{
		if (isset($arr->childs) && is_array($arr->childs)) {
			$sup	= '<sup>|_</sup>';
			$space	= '';
			$ld		= (empty($linkDelimiter)) ? '>' : $linkDelimiter;

			if($type == 'select' || $type == 'popup') {
				$deep++;
				for($d=0; $d < $deep; $d++)
				{
					$space .= '&nbsp;&nbsp;&nbsp;';
				}
			}

			for ($j	= 0; $j < count($arr->childs); $j++) {
				$child	= $arr->childs[$j];

				$cat = EB::table('Category');
				$cat->bind($child);

				if($type == 'select') {
					$selected	= ($child->id == $default) ? ' selected="selected"' : '';

					$html	.= '<option value="'.$child->id.'" ' . $selected . '>' . $space . $sup . JText::_($child->title)  . '</option>';
				} else if($type == 'popup') {
					$html	.= '<div class="category-list-item" id="'.$child->id.'">' . $space . $sup . '<a href="javascript:void(0);" onclick="eblog.dashboard.selectCategory(\''. $child->id. '\')">' . JText::_($child->title) . '</a>';
					$html	.= '<input type="hidden" id="category-list-item-'.$child->id.'" value="'. JText::_($child->title) .'" />';
					$html	.= '</div>';
				} else {
					$str	= '<a href="' . $cat->getPermalink() . '">' . $cat->getTitle() . '</a>';
					$html	.= (empty($html)) ? $str : $ld . $str;
				}

				if ($type == 'select') {
					EB::accessNestedCategories($child, $html, $deep, $default, $type, $linkDelimiter);
				}
			}
		}

		return false;
	}



	public static function accessNestedCategoriesId($arr, &$newArr)
	{
		if(isset($arr->childs) && is_array($arr->childs)) {

			for ($j	= 0; $j < count($arr->childs); $j++) {
				$child		= $arr->childs[$j];
				$newArr[]	= $child->id;
				EB::accessNestedCategoriesId($child, $newArr);
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Retrieve backward linkage from a child category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function populateCategoryLinkage($childId)
	{
		$arr = array();
		$category = EB::table('Category');
		$category->load($childId);

		$obj = new stdClass();
		$obj->id = $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[] = $obj;

		if ((!empty($category->parent_id))) {
			EB::accessCategoryLinkage($category->parent_id, $arr);
		}

		$arr = array_reverse($arr);
		return $arr;

	}

	public static function accessCategoryLinkage($childId, &$arr)
	{
		$category	= EB::table('Category');
		$category->load($childId);

		$obj = new stdClass();
		$obj->id = $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;



		$arr[] = $obj;

		if ((!empty($category->parent_id))) {
			EB::accessCategoryLinkage($category->parent_id, $arr);
		} else {
			return false;
		}
	}


	/**
	 * Get post title by ID
	 */
	public static function getPostTitle($id)
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->nameQuote('title') . ' FROM ' . $db->nameQuote('#__easyblog_post') . ' WHERE id = ' . $db->Quote($id);
		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function storeSession($data, $key, $ns = 'COM_EASYBLOG')
	{
		$mySess	= JFactory::getSession();
		$mySess->set($key, $data, $ns);
	}

	public static function getSession($key, $ns = 'COM_EASYBLOG')
	{
		$data = null;
		$mySess	= JFactory::getSession();
		if ($mySess->has($key, $ns)) {
			$data = $mySess->get($key, '', $ns);
			$mySess->clear($key, $ns);
			return $data;
		} else {
			return $data;
		}
	}

	public static function clearSession($key, $ns = 'COM_EASYBLOG')
	{
		$mySess = JFactory::getSession();
		if($mySess->has($key, $ns))
		{
			$mySess->clear($key, $ns);
		}
		return true;
	}

	/**
	 * Renders the ajax library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function ajax()
	{
		static $ajax = null;

		if (!$ajax) {

			require_once(__DIR__ . '/ajax/ajax.php');

			$ajax = new EasyBlogAjax(EB::fd());
		}

		return $ajax;
	}

	/**
	 * Helper to sanitize comma separated values
	 *
	 * @since	5.2.12
	 * @access	public
	 */
	public static function sanitizeCsv($string, $type = 'integer')
	{
		// If there's no value, there is nothing to sanitize
		if (!$string) {
			return;
		}

		$data = explode(',', $string);

		if ($data) {

			// Cleanup the data now
			foreach ($data as &$item) {
				settype($item, $type);
			}

			$data = implode(',', $data);
		}

		return $data;
	}

	/**
	 * Deprecated. Use FH::getJoomlaVersion()
	 *
	 * @deprecated	6.0.0
	 */
	public static function getJoomlaVersion()
	{
		return FH::getJoomlaVersion();
	}

	/**
	 * Deprecated. Use FH::isJoomla31()
	 *
	 * @deprecated	6.0.0
	 */
	public static function isJoomla31()
	{
		return FH::isJoomla31();
	}

	/**
	 * Deprecated. Use FH::isJoomla4()
	 *
	 * @deprecated	6.0.0
	 */
	public static function isJoomla4()
	{
		return FH::isJoomla4();
	}

	/**
	 * Deprecated but we still require this function
	 * so that user who perform upgrade from older version (5.2.x) will not hit error
	 *
	 * @deprecated	6.0.0
	 */
	public static function isJoomla40()
	{
		return self::isJoomla4();
	}

	/**
	 * Retrieves all usergroups ids on the site
	 *
	 * @since   5.2.6
	 * @access  public
	 */
	public static function getUsergroupsIds($idOnly = false)
	{
		$db = EB::db();

		$column = $idOnly ? 'a.`id`' : 'a.*, COUNT(DISTINCT(b.`id`)) AS `level`';

		$query 	= 'SELECT ' . $column . ' FROM ' . $db->quoteName('#__usergroups') . ' AS a';
		$query .= ' LEFT JOIN ' . $db->quoteName('#__usergroups') . ' AS b';
		$query .= ' ON a.`lft` > b.`lft` AND a.`rgt` < b.`rgt`';
		$query .= ' GROUP BY a.`id`, a.`title`, a.`lft`, a.`rgt`, a.`parent_id`';
		$query .= ' ORDER BY a.`lft` ASC';

		$db->setQuery($query);
		$groups = $idOnly ? $db->loadColumn() : $db->loadObjectList();

		return $groups;
	}

	/**
	 * Retrieves a list of super users from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getSAUsersIds()
	{
		$db = EB::db();

		$query	= 'SELECT a.`id`, a.`title`';
		$query	.= ' FROM `#__usergroups` AS a';
		$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query	.= ' GROUP BY a.id';
		$query	.= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$saGroup = array();
		foreach($result as $group)
		{
			if(JAccess::checkGroup($group->id, 'core.admin'))
			{
				$saGroup[]  = $group;
			}
		}


		//now we got all the SA groups. Time to get the users
		$saUsers = array();
		if(count($saGroup) > 0)
		{
			foreach($saGroup as $sag)
			{
				$userArr = JAccess::getUsersByGroup($sag->id);
				if(count($userArr) > 0)
				{
					foreach($userArr as $user)
					{
						$saUsers[] = $user;
					}
				}
			}
		}

		return $saUsers;
	}

	/**
	 * Retrieves the default super administrator user id
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getDefaultSAIds()
	{
		$saUsers = EB::getSAUsersIds();
		$saUserId = $saUsers[0];

		return $saUserId;
	}

	/**
	 * Determines the image size to be used
	 *
	 * @since	5.4.3
	 * @access	public
	 */
	public static function getCoverSize($configKey)
	{
		static $cache = [];

		if (!isset($cache[$configKey])) {
			$config = EB::config();
			$mobile = EB::responsive()->isMobile();

			// Default
			$cache[$configKey] = $config->get($configKey);

			if ($mobile) {
				$key = $configKey . '_mobile';
				$cache[$configKey] = $config->get($key);
			}

			// Backward compatibility
			$cache[$configKey] = in_array($cache[$configKey], self::getDeprecatedImageSizes()) ? 'thumbnail' : $cache[$configKey];
		}

		return $cache[$configKey];
	}

	/**
	 * Retrieve the deprecated image sizes
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function getDeprecatedImageSizes()
	{
		return ['icon', 'small', 'medium'];
	}

	public static function getFirstImage( $content )
	{
		//try to search for the 1st img in the blog
		$img		= '';
		$pattern	= '#<img[^>]*>#i';
		preg_match( $pattern , $content , $matches );

		if( $matches )
		{
			$img	= $matches[0];
		}


		//image found. now we process further to get the absolute image path.
		if(! empty($img) )
		{
			//get the img src

			$pattern = '/src\s*=\s*"(.+?)"/i';
			preg_match($pattern, $img, $matches);
			if($matches)
			{
				$imgPath	= $matches[1];
				$imgSrc		= EB::image()->rel2abs($imgPath, JURI::root());

				return $imgSrc;
			}
		}

		return false;
	}

	/**
	 * Deprecated. No longer in use since 6.0
	 *
	 * @deprecated	6.0.0
	 */
	public static function getBloggerTheme()
	{
		return false;
	}

	public static function getUserGids($userId = '', $implode = false)
	{
		$user = '';

		if (empty($userId)) {
			$user = JFactory::getUser();
		} else {
			$user = JFactory::getUser($userId);
		}

		$grpId = array();

		if ($user->id == 0) {
			$grpId	= JAccess::getGroupsByUser(0, false);
		} else {
			$grpId	= JAccess::getGroupsByUser($user->id, false);
		}

		if (empty($grpId)) {
			//this case shouldn't happen but it happened. sigh.
			$grpId[] = '1';
		}

		if ($implode) {
			$gids = '';
			if (count($grpId) > 0) {
				foreach ($grpId as $id) {
					$gids .= (empty($gids)) ? $id : ',' . $id;
				}
			}
			return $gids;
		}

		return $grpId;

	}

	/**
	 * Retrieves Joomla's cache object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getCache()
	{
		$jconfig = EB::jconfig();

		$options = array(
			'defaultgroup'	=> '',
			'storage' 		=> $jconfig->get('cache_handler', ''),
			'caching'		=> true,
			'cachebase'		=> $jconfig->get('cache_path', JPATH_SITE . '/cache')
		);

		$cache = JCache::getInstance('', $options);

		return $cache;
	}



	public static function getAccessibleCategories( $parentId = 0 )
	{
		$db			= EB::db();
		$my			= JFactory::getUser();

		$gids		= '';
		$catQuery	= 	'select distinct a.`id`, a.`private`';
		$catQuery	.=  ' from `#__easyblog_category` as a';
		$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0');

		if( $my->id != 0 )
		{
			$catQuery	.=  ' OR a.`private` = ' . $db->Quote('1');
		}

		$gid	= array();
		if( $my->id == 0 )
		{
			$gid	= JAccess::getGroupsByUser(0, false);
		}
		else
		{
			$gid	= JAccess::getGroupsByUser($my->id, false);
		}

		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}
		}

		$catQuery	.= ' OR a.`id` IN (';
		$catQuery	.= '     SELECT c.`category_id` FROM `#__easyblog_category_acl` as c ';
		$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
		$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
		$catQuery	.= ')';

		if ($parentId) {
			$catQuery   .= ' AND a.parent_id = ' . $db->Quote($parentId);
		}

		$db->setQuery($catQuery);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * This function returns array of private categories respected the category acl view.
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public static function getPrivateCategories()
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$query = array();

		$gid = EB::getUserGids();
		$gids = '';

		foreach ($gid as $id) {
			$gids .= $db->Quote($id);
			$gids .= next($gid) ? ', ' : '';
		}

		if ($my->id == 0) {
			$query[] = 'SELECT DISTINCT a.`id`, a.`private`';
			$query[] = 'FROM `#__easyblog_category` as a';
			$query[] = 'LEFT JOIN `#__easyblog_category_acl` as b';
			$query[] = 'ON a.`id` = b.`category_id`';
			$query[] = 'AND b.`acl_id` = ' . $db->Quote(CATEGORY_ACL_ACTION_VIEW);
			$query[] = 'WHERE a.`private` != ' . $db->Quote('0');

			if (count($gid) > 0) {
				$query[] = 'AND b.`category_id` NOT IN (';
				$query[] = 'SELECT c.`category_id` FROM `#__easyblog_category_acl` as c';
				$query[] = 'WHERE c.`acl_id` = ' . $db->Quote(CATEGORY_ACL_ACTION_VIEW);
				$query[] = 'AND c.`content_id` IN (' . $gids . ')';
				$query[] = ')';
			}
		} else {
			// @task: Do not exclude anything for admin.
			if (FH::isSiteAdmin()) {
				return array();
			}

			$query[] = 'SELECT `id` FROM `#__easyblog_category` as a';
			$query[] = 'WHERE NOT EXISTS (';
			$query[] = 'SELECT b.`category_id` FROM `#__easyblog_category_acl` as b';
			$query[] = 'WHERE b.`category_id` = a.`id`';
			$query[] = 'AND b.`acl_id` = ' . $db->Quote(CATEGORY_ACL_ACTION_VIEW);
			$query[] = 'AND b.`type` = ' . $db->Quote('group');
			$query[] = 'AND b.`content_id` IN (' . $gids . ')';
			$query[] = ')';
			$query[] = 'AND a.`private` = ' . $db->Quote(CATEGORY_PRIVACY_ACL);
		}

		$query = implode(' ', $query);

		// echo '-- ' . $my->name . ' new<br/>';
		// echo '<code>' . str_replace('#_', 'jos', $query) . ';</code>';exit;

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		$excludeCats = array();

		if (!$categories) {
			return $excludeCats;
		}

		foreach ($categories as &$category) {
			// ->childs is required to build nested category.
			$category->childs = null;

			EB::buildNestedCategories($category->id, $category);

			$catIds = array();
			$catIds[] = $category->id;

			EB::accessNestedCategoriesId($category, $catIds);

			$excludeCats = array_merge($excludeCats, $catIds);
		}

		return $excludeCats;

	}

	public static function getDefaultCategoryId()
	{
		$db = EB::db();

		$gid = EB::getUserGids();
		$gids = '';
		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}
		}

		$query	= 'SELECT a.id';
		$query	.= ' FROM `#__easyblog_category` AS a';
		$query	.= ' WHERE a.`published` = ' . $db->Quote( '1' );
		$query	.= ' AND a.`default` = ' . $db->Quote( '1' );
		$query	.= ' and a.id not in (';
		$query	.= ' 	select id from `#__easyblog_category` as c';
		$query	.= ' 	where not exists (';
		$query	.= '			select b.category_id from `#__easyblog_category_acl` as b';
		$query	.= '				where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
		$query	.= '				and b.type = ' . $db->Quote('group');
		$query	.= '				and b.content_id IN (' . $gids . ')';
		$query	.= '		)';
		$query	.= '	and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		$query	.= '	)';
		$query	.= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote( '0' ) . ' AND e.`parent_id` = ' . $db->Quote( '0' ) . ' )';
		$query	.= ' ORDER BY a.`lft` LIMIT 1';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return ( empty( $result ) ) ? '0' : $result ;
	}

	public static function isBlogger( $userId )
	{
		if( empty( $userId ) )
			return false;

		$acl = EB::acl($userId);
		if ($acl->get('add_entry')) {
			return true;
		} else {
			return false;
		}

	}

	public static function getUniqueFileName($originalFilename, $path)
	{
		$ext			= JFile::getExt($originalFilename);
		$ext			= $ext ? '.'.$ext : '';
		$uniqueFilename	= JFile::stripExt($originalFilename);

		$i = 1;

		while (JFile::exists($path.DIRECTORY_SEPARATOR.$uniqueFilename.$ext)) {
			$uniqueFilename	= JFile::stripExt($originalFilename) . '_' . $i . '_' . EB::date()->format("Ymd-His");
			$i++;
		}

		//remove the space into '-'
		$uniqueFilename = str_ireplace(' ', '-', $uniqueFilename);

		return $uniqueFilename.$ext;
	}

	/**
	 * Retrieves the current language
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getCurrentLanguage()
	{
		static $language = null;

		if (EB::isFromAdmin()) {
			return false;
		}

		if (is_null($language)) {

			$language = false;

			// When language filter is enabled, we need to detect the appropriate contents
			$multiLanguage = JFactory::getApplication()->getLanguageFilter();

			if ($multiLanguage) {
				$language = JFactory::getLanguage()->getTag();
			}
		}

		return $language;
	}

	public static function getCategoryMenuBloggerId()
	{
		$app = JFactory::getApplication();

		$itemId	= $app->input->get('Itemid', 0);
		$menu = $app->getMenu();
		$menuparams	= $menu->getParams( $itemId );
		$catBloggerId = $menuparams->get('cat_bloggerid', '');

		return $catBloggerId;
	}

	/**
	 * Adds canonical URL to satisfy google bots in case they think that it's a duplicated content
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function addCanonicalURL( $extraFishes = array() )
	{
		if (empty( $extraFishes ))
		{
			return;
		}

		$juri = JURI::getInstance();

		foreach( $extraFishes as $fish )
		{
			$juri->delVar( $fish );
		}

		$preferredURL	= $juri->toString();

		jimport('joomla.filter.filterinput');
		$inputFilter	= JFilterInput::getInstance();
		$preferredURL	= $inputFilter->clean($preferredURL, 'string');

		$document	= JFactory::getDocument();
		$document->addHeadLink( $preferredURL, 'canonical', 'rel');
	}

	/**
	 * Generates the unsubscribe link for emails
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getUnsubscribeLink($subdata, $external=false, $isAllType = false, $email = '')
	{
		$easyblogItemId	= EBR::getItemId( 'latest' );

		if ($isAllType && $email) {

			$types = array();
			$ids = array();
			$tokens = array();

			foreach($subdata as $type => $id) {
				$types[] = $type;

				$tmpId = explode('|', $id);
				$ids[] = $tmpId[0];
				$tokens[] = md5($tmpId[0] . $tmpId[1]);
			}

			$stype = implode(',', $types);
			$sid = implode(',', $ids);
			$stoken = implode(',', $tokens);

			$unsubdata = base64_encode("type=".$stype."\r\nsid=".$sid."\r\nuid=".$email."\r\ntoken=".$stoken);

		} else {
			$unsubdata = base64_encode("type=".$subdata->type."\r\nsid=".$subdata->id."\r\nuid=".$subdata->user_id."\r\ntoken=".md5($subdata->id.$subdata->created));
		}

		return EBR::getRoutedURL('index.php?option=com_easyblog&task=subscription.unsubscribe&data='.$unsubdata.'&Itemid=' . $easyblogItemId, false, $external);
	}

	/**
	 * Retrieves the editor except composer page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getEditor($nameOnly = false)
	{
		$config = EB::config();
		$jConfig = EB::jConfig();

		// If use system editor, we should check if the configured editor exists or enabled.
		$selectedEditor = $config->get('layout_editor');

		// if use build-in composer, we should check from the global configuration setting
		if ($selectedEditor == 'composer') {
			$selectedEditor = $jConfig->get('editor');
		}

		// Test if the plugin is enabled
		$enabled = JPluginHelper::isEnabled('editors', $selectedEditor);

		// If the editor isn't enabled, we need to intelligently find one that is enabled.
		if (!$enabled) {

			$model = EB::model('Settings');
			$randomEditor = $model->getAvailableEditor();

			if (!$randomEditor) {
				// No editors enabled on the site. WTF?
				EB::info()->set(JText::_('COM_EASYBLOG_NO_EDITORS_ENABLED_ON_SITE'), 'error');

				return false;
			}

			// Use the random enabled editor
			$selectedEditor = $randomEditor;

			// Show some error message that the configured editor isn't available.
			EB::info()->set(JText::sprintf('COM_EASYBLOG_SELECTED_EDITOR_NOT_ENABLED', $selectedEditor, $randomEditor), 'error');
		}

		if ($nameOnly) {
			return $selectedEditor;
		}

		$editor = EBFactory::getEditor($selectedEditor);

		return $editor;
	}

	/**
	 * Renders the edit profile link in EasyBlog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getEditProfileLink($forceEBLink = false, $external = false)
	{
		$link = EBR::getRoutedURL('index.php?option=com_easyblog&view=dashboard&layout=profile', false, $external);

		if ($forceEBLink) {
			return $link;
		}

		$config = EB::config();

		if ($config->get('integrations_easysocial_editprofile') && EB::easysocial()->exists()) {
			$link = ESR::profile(array('layout' => 'edit'));
		}

		return $link;
	}

	/**
	 * Deprecated. Use FH::isRegistrationEnabled()
	 *
	 * @deprecated	6.0.0
	 */
	public static function isRegistrationEnabled()
	{
		return FH::isRegistrationEnabled();
	}

	/**
	 * Renders the registration link within EasyBlog extension
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getRegistrationLink()
	{
		$config = EB::config();
		$provider = $config->get('main_login_provider');

		$link = JRoute::_('index.php?option=com_users&view=registration');


		if ($provider == 'easysocial' && EB::easysocial()->exists()) {
			$link = ESR::registration();
		}

		if ($provider == 'cb') {
			$link = JRoute::_('index.php?option=com_comprofiler&task=registers');
		}

		if ($provider == 'jomsocial') {
			$link = JRoute::_('index.php?option=com_community&view=register');
		}

		return $link;
	}

	/**
	 * Generates the login link within EasyBlog extension
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getLoginLink($returnURL = '')
	{
		$config = EB::config();

		if (!empty($returnURL)) {
			$return = '&return=' . $returnURL;
		}

		$default = EBR::_('index.php?option=com_users&view=login' . $return);

		// Default link
		$link = $default;
		$loginProvider = $config->get('main_login_provider');

		if ($loginProvider == 'easysocial') {
			$easysocial = EB::easysocial();

			if ($easysocial->exists()) {
				$link = ESR::login(array('return' => $return));
			}
		}

		if ($loginProvider == 'cb') {
			$app = JFactory::getApplication();
			$menu = $app->getMenu();

			// check the CB is it got this login menu created
			$menuItem = $menu->getItems('link', 'index.php?option=com_comprofiler&view=login', true);

			$link = JRoute::_('index.php?option=com_comprofiler&task=login' . $return);

			if (!empty($menuItem->id)) {
				$link = JRoute::_('index.php?option=com_comprofiler&view=login&Itemid=' . $menuItem->id . $return, false);
			}
		}

		if ($loginProvider == 'easyblog') {
			$link = JRoute::_('index.php?option=com_easyblog&view=login' . $return);
		}

		return $link;
	}

	/**
	 * Generates the reset password link within EasyBlog extension
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getResetPasswordLink()
	{
		$config = EB::config();
		$provider = $config->get('main_login_provider');

		$link = JRoute::_('index.php?option=com_users&view=reset');

		if ($provider == 'easysocial' && EB::easysocial()->exists()) {
			$link = ESR::account(array('layout' => 'forgetPassword'));
		}

		if ($provider == 'cb') {
			$link = JRoute::_('index.php?option=com_comprofiler&task=lostpassword');
		}

		return $link;
	}

	/**
	 * Generates the remind username link
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getRemindUsernameLink()
	{
		static $link = null;

		if (!is_null($link)) {
			return $link;
		}

		$config = EB::config();
		$provider = $config->get('main_login_provider');
		$link = JRoute::_('index.php?option=com_users&view=remind');

		if ($provider == 'easysocial' && EB::easysocial()->exists()) {
			$link = ESR::account(array('layout' => 'forgetUsername'));
		}

		return $link;
	}

	/**
	 *Retrieves a list of category id that should be included
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getCategoryInclusion($categories)
	{
		if (!$categories) {
			return '';
		}

		// No filtering applied since the value is 'all'
		if (!empty($categories) && $categories == 'all') {
			return '';
		}

		// No filtering applied since the value is 'all'
		if (is_array($categories) && in_array('all', $categories)) {
			return '';
		}

		if (is_array($categories)) {
			return $categories;
		}

		$inclusion = explode(',', $categories);

		return $inclusion;
	}


	public static function uniqueLinkSegments($urls = '')
	{
		// if empty or sh404sef enabled, we dont do any process to the links. #1594
		if (!$urls || EBR::isSh404Enabled()) {
			return $urls;
		}

		$container  = explode('/', $urls);
		$container	= array_unique($container);
		$urls = implode('/', $container);

		return $urls;
	}

	/**
	 * Deprecated. Use EB::themes
	 *
	 * @since	6.0.0
	 */
	public static function template($options = [])
	{
		return EB::themes($options);
	}

	/**
	 * Creates a new theme instance
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function themes($options = [])
	{
		EB::load('themes');

		$theme = new EasyBlogThemes($options);

		return $theme;
	}

	/**
	 * Deprecated. Use FH::getTwoFactorMethods
	 *
	 * @deprecated	6.0.0
	 */
	public static function getTwoFactorMethods()
	{
		return FH::getTwoFactorMethods();
	}

	/**
	 * Prepares the HTML to render two factor forms on the page
	 *
	 * @since	5.4.3
	 * @access	public
	 */
	public static function getTwoFactorForms($otpConfig, $userId = null)
	{
		return EBCompat::getTwoFactorForms($otpConfig, $userId);
	}

	/**
	 * Loads the user model from Joomla
	 *
	 * @since	5.4.3
	 * @access	public
	 */
	public static function getJoomlaUserModel()
	{
		static $model = null;

		if (is_null($model)) {

			if (FH::isJoomla4()) {
				$app = JFactory::getApplication();
				$model = $app->bootComponent('com_users')->getMVCFactory()->createModel('User', 'Administrator', array('ignore_request' => true));

				return $model;
			}

			JLoader::register('UsersModelUser', JPATH_ADMINISTRATOR . '/components/com_users/models/user.php');

			$model = new UsersModelUser;
		}

		return $model;
	}

	/**
	 * Legacy method to get excluded categories
	 *
	 * @since	6.0.11
	 * @access	public
	 */
	public static function getExcludedLegacyCategories()
	{
		$config = EB::config();
		$tmp = $config->get('layout_exclude_categories', null);

		if ($tmp) {
			$tmp = explode(',', $tmp);

			foreach ($tmp as &$categoryId) {
				$categoryId = (int) $categoryId;
			}
		}

		return $tmp;
	}

	/**
	 * Deprecated. Use EB::themes() instead
	 *
	 * @deprecated	6.0.0
	 */
	public static function getTemplate($theme = false, $options = [])
	{
		return EB::themes($options);
	}

	/**
	 * Determines if the provided theme is a legacy theme
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function isLegacyTheme($theme)
	{
		$file 	= EBLOG_THEMES . '/' . $theme . '/config.json';

		$exists = JFile::exists($file);

		return !$exists;
	}

	/**
	 * Loads a list of services
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function loadServices()
	{
		$files = JFolder::files(JPATH_ROOT . '/components/com_easyblog/services', '.', false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));

		if (!$files) {
			return;
		}

		foreach ($files as $file) {
			require_once($file);
		}

	}

	/**
	 * Deprecated. Use FH::checkToken()
	 *
	 * @deprecated	6.0.0
	 */
	public static function checkToken()
	{
		return FH::checkToken();
	}

	/**
	 * Content formatter for the blogs
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function formatter($type, $items, $cache = true, $options = array())
	{
		require_once(dirname(__FILE__) . '/formatter/formatter.php');

		$formatter 	= new EasyBlogFormatter($type, $items, $cache, $options);

		return $formatter->execute();
	}

	/**
	 * Converts to sef links
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function _($link, $xhtml = true)
	{
		require_once(dirname(__FILE__) . '/router.php');

		return EBR::_($link, $xhtml);
	}

	/**
	 * Detects if the folder exist based on the path given. If it doesn't exist, create it.
	 *
	 * @since	4.0
	 * @param	string	$path		The path to the folder.
	 * @return	boolean				True if exists (after creation or before creation) and false otherwise.
	 */
	public static function makeFolder( $path )
	{
		jimport('joomla.filesystem.folder');

		// If folder exists, we don't need to do anything
		if (JFolder::exists($path)) {
			return true;
		}

		// Folder doesn't exist, let's try to create it.
		if (JFolder::create($path)) {
			return true;
		}

		return false;
	}

	/**
	 * Converts an argument into an array.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function makeArray($item, $delimeter = null)
	{
		// If this is already an array, we don't need to do anything here.
		if (is_array($item)) {
			return $item;
		}

		// Test if source is a SocialRegistry/JRegistry object
		if ($item instanceof JRegistry) {
			return $item->toArray();
		}

		// Test if source is an object.
		if (is_object($item)) {
			return EBArrayHelper::fromObject($item);
		}

		if (is_integer($item)) {
			return array($item);
		}

		// Test if source is a string.
		if (is_string($item)) {

			if ($item == '') {
				return array();
			}

			// Test for comma separated values.
			if (!is_null($delimeter) && stristr($item, $delimeter) !== false) {
				$data = explode($delimeter, $item);

				return $data;
			}

			// Test for JSON array string
			$pattern = '#^\s*//.+$#m';
			$item = trim(preg_replace($pattern, '', $item));

			if ((substr($item, 0, 1) === '[' && substr($item, -1, 1) === ']')) {
				return json_decode($item);
			}

			// Test for JSON object string, but convert it into array
			if ((substr($item, 0, 1) === '{' && substr($item, -1, 1) === '}')) {
				$result = json_decode($item);

				return EBArrayHelper::fromObject($result);
			}

			return array($item);
		}

		return false;
	}

	/**
	 * Proxy for post library
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function post($uid = null, $revisionId = null)
	{
		require_once(dirname(__FILE__) . '/post/post.php');

		$post = new EasyBlogPost($uid, $revisionId);

		return $post;
	}

	/**
	 * Proxy for document library
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function document($json=null)
	{
		require_once(dirname(__FILE__) . '/document/document.php');

		$document = new EasyBlogDocument($json);

		return $document;
	}

	/**
	 * Proxy for location library.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function location($provider = null)
	{
		require_once(dirname(__FILE__) . '/location/location.php');

		$service = new EasyBlogLocation($provider);

		return $service;
	}

	/**
	 * Determines if language association is enabled
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isAssociationEnabled()
	{
		static $enabled = null;

		if (is_null($enabled)) {

			// Default to disabled
			$enabled = JLanguageMultilang::isEnabled();

			if ($enabled) {
				$langFilter = JPluginHelper::getPlugin('system', 'languagefilter');
				if ($langFilter) {
					$params = new JRegistry(JPluginHelper::getPlugin('system', 'languagefilter')->params);
					$enabled = (boolean) $params->get('item_associations', true);
				}
			}
		}

		return $enabled;
	}

	/**
	 * determined if falang activated.
	 *
	 * @since	5.0.42
	 * @access	public
	 */
	public static function isFalangActivated()
	{
		static $_cache = null;

		if (is_null($_cache)) {

			$pluginInstalled = JPluginHelper::getPlugin('system', 'falangdriver');
			$pluginEnabled = JPluginHelper::isEnabled('system', 'falangdriver');

			$exists = JFile::exists(JPATH_ADMINISTRATOR . '/components/com_falang/classes/FalangManager.class.php');
			$_cache = false;

			if ($pluginInstalled && $pluginEnabled && $exists) {
				$_cache = true;
			}
		}

		return $_cache;
	}

	/**
	 * cache for post related items.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function cache()
	{
		static $cache = null;

		if (!$cache) {
			require_once(__DIR__ . '/cache/cache.php');

			$cache = new EasyBlogCache();
		}

		return $cache;
	}

	/**
	 * math lib
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function math()
	{
		static $math = null;

		if (!$math) {
			require_once(__DIR__ . '/math/math.php');

			$math = new EasyBlogMath();
		}

		return $math;
	}

	/**
	 * function to the column collation that compatible with Joomla 3.5 jos_users table collation.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getUsersTableCollation($from)
	{
		static $collationType = array();

		if (!isset($collationType[$from])) {

			// Default value
			$collationType[$from] = '';

			$jVersion = FH::getJoomlaVersion();

			if (version_compare($jVersion, '3.5', '>=')) {

				$jConfig = EB::jconfig();
				$dbType = $jConfig->get('dbtype');

				if ($dbType == 'mysql' || $dbType == 'mysqli' || $dbType == 'pdomysql') {
					$db = EB::db();
					$dbversion = $db->getVersion();
					$dbversion = (float) $dbversion;

					if (version_compare($dbversion, '5.1', '>=')) {

						$prefix = $db->getPrefix();

						$tableName = $prefix . 'users';

						$query = "SHOW TABLE STATUS WHERE `Name` = " . $db->Quote($tableName);
						$db->setQuery($query);
						$result = $db->loadObject();

						$collation = $result->Collation;

						if (strpos($collation, 'mb4_') !== false) {

							if ($from == 'eb') {
								$tableName2 = $prefix . 'easyblog_subscriptions';

								$query = "SHOW TABLE STATUS WHERE `Name` = " . $db->Quote($tableName2);
								$db->setQuery($query);
								$result = $db->loadObject();

								$collation2 = $result->Collation;

								if (strpos($collation2, 'mb4_') !== false) {
									$collationType[$from] = 'COLLATE utf8mb4_unicode_ci';
								} else {
									$collationType[$from] = 'COLLATE utf8_unicode_ci';
								}
							} else {
								$collationType[$from] = 'COLLATE utf8mb4_unicode_ci';
							}

						} else if ($db->hasUTFSupport()) {
							$collationType[$from] = 'COLLATE utf8_unicode_ci';
						}
					}
				}
			}
		}

		return $collationType[$from];
	}


	/**
	 * This method will intelligently determine which menu params this post should be inheriting from
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getMenuParams($id, $type, $menuParams = false, $debug = false)
	{
		static $items = [];

		$index = $id . $type;

		if (!isset($items[$index])) {

			$config = EB::config();
			$model = EB::model('Menu');

			// If there is an article menu item associated with this post, use this
			$menuId = 0;

			if ($type == 'listing') {
				$listingParams = $model->getDefaultXMLParams();
				$arrTmpParams = $listingParams->toArray();

				foreach ($arrTmpParams as $key => $val) {
					$listingParams->set($key, $config->get($type . '_' . $key));
				}

				$items[$index] = $listingParams;

				return $items[$index];
			}

			if ($type == 'categories' || $type == 'category') {
				$menuLayout = 'default';

				if ($type == 'category') {
					$menuLayout = 'listings';
					$menuId = $model->getMenusByCategoryId($id);
				} else {
					$menuId = $model->getMenus('categories');
				}

				$defaultParams = $model->getXMLParams('categories', $menuLayout);
			}

			if ($type == 'tags') {
				$menuLayout = 'default';
				$menuId = $model->getMenus('tags');

				$defaultParams = $model->getXMLParams('tags', $menuLayout);
			}

			if ($type == 'tag') {
				$menuId = $model->getMenusByTagId($id);
				$defaultParams = $model->getXMLParams('tags', 'tag');
			}

			if ($type == 'blogger' || $type == 'bloggers') {
				$menuLayout = 'default';

				if ($type == 'blogger') {
					$menuLayout = 'listings';
					$menuId = $model->getMenusByBloggerId($id);
				} else {
					$menuId = $model->getMenus('blogger');
				}

				$defaultParams = $model->getXMLParams('blogger', $menuLayout);
			}

			if ($menuId) {
				$params = $model->getMenuParamsById($menuId);
				$arrParams = $params->toArray();

				$isPostRowStyleUseGlobal = false;

				foreach ($arrParams as $key => $value) {

					// need to override the menu setting value if some of the setting is under child setting e.g. Post Listing Style
					// if you select "Post Listing Style" to Use Global then the child setting like "Columns Per Row" then need to Use Global value as well
					if ($key === 'row_style' && $value === '-1') {
						$isPostRowStyleUseGlobal = true;
					}

					if ($isPostRowStyleUseGlobal && ($key === 'column_style' || $key === 'card_column')) {
						$value = '-1';
					}

					if ($value == '-1') {
						$params->set($key, $config->get($type . '_' . $key));
					}
				}

				$items[$index] = $params;
			} else {
				$arrTmpParams = $defaultParams->toArray();

				foreach ($arrTmpParams as $key => $value) {
					$defaultParams->set($key, $config->get($type . '_' . $key));
				}

				$items[$index] = $defaultParams;
			}
		}

		return $items[$index];
	}

	/**
	 * Deprecated. Rely on FD::normalize
	 *
	 * @deprecated	6.0.0
	 */
	public static function normalize($data, $key, $default = null)
	{
		return FH::normalize($data, $key, $default);
	}

	/**
	 * Set callback url in joomla session
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function setCallback($data)
	{
		$session = JFactory::getSession();

		// Serialize the callback data.
		$data = serialize($data);

		// Store the data
		$session->set('easyblog.callback', $data, EBLOG_SESSION_NAMESPACE);
	}

	/**
	 * Retrieve callback url from joomla session
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getCallback($default = '', $resetSession = true)
	{
		$session = JFactory::getSession();
		$data = $session->get('easyblog.callback', '', EBLOG_SESSION_NAMESPACE);

		$data = unserialize($data);

		// Clear off the session once it's been picked up.
		if ($resetSession) {
			$session->clear('easyblog.callback', EBLOG_SESSION_NAMESPACE);
		}

		if (!$data && $default) {
			return $default;
		}

		return $data;
	}

	/**
	 * Deprecated. Use FH::isMultiLinagual
	 *
	 * @deprecated	6.0.0
	 */
	public static function isSiteMultilingualEnabled()
	{
		return FH::isMultiLingual();
	}

	/**
	 * Give a proper redirection when the user does not have the permission to view item.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getErrorRedirection($message = null)
	{
		$config = EB::config();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!$message) {
			$message = JText::_('COM_EASYBLOG_NOT_ALLOWED_TO_PERFORM_ACTION');
		}

		EB::requireLogin();

		EB::info()->set($message, 'error');
		return $app->redirect(EBR::_('index.php?option=com_easyblog&view=latest'), false);
	}

	/**
	 * Reads a XML file.
	 *
	 * @since   5.2
	 * @access  public
	 */
	 public static function getXml($data, $isFile = true)
	 {
		$class = 'SimpleXMLElement';

		if ($isFile) {
			// Try to load the XML file
			$xml = simplexml_load_file($data, $class);

		} else {
			// Try to load the XML string
			$xml = simplexml_load_string($data, $class);
		}

		if ($xml === false) {
			foreach (libxml_get_errors() as $error) {
				echo "\t", $error->message;
			}
		}

		return $xml;
	 }

	 /**
	  * Retrieves logo
	  *
	  * @since	5.1
	  * @access	public
	  */
	 public static function getLogo($type = 'email', $forceDefault = false)
	{
		$config = self::config();
		$logo = rtrim(JURI::root(), '/') . '/media/com_easyblog/images/' . $type . '/logo.png';

		if ($forceDefault || ($type == 'email' && !$config->get('custom_email_logo'))) {
			return $logo;
		}

		if (self::hasOverrideLogo($type)) {
			$override = self::getLogoOverridePath($type);
			$override = rtrim(JURI::root(), '/') . $override;

			return $override;
		}

		return $logo;
	}

	/**
	 * Determines if we should be applying watermark on images
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function hasImageWatermark()
	{
		static $watermark = null;

		if (is_null($watermark)) {
			$watermark = false;

			$config = EB::config();

			if (!$config->get('image_watermark') || !EB::hasOverrideLogo('watermark')) {
				return $watermark;
			}

			$watermark = true;
		}

		return $watermark;
	}

	/**
	 * Deprecated. Use FH::hasTwoFactor instead
	 *
	 * @deprecated	6.0.0
	 */
	public static function hasTwoFactor()
	{
		return FH::hasTwoFactor();
	}

	/**
	 * Determine if custom logo is exists
	 *
	 * @since	5.1
	 * @access	public
	 */
	 public static function hasOverrideLogo($type)
	 {
		$path = JPATH_ROOT . self::getLogoOverridePath($type);

		if (JFile::exists($path)) {
			return true;
		}

		return false;
	 }

	/**
	 * Retrieve the otpConfig from Joomla users
	 *
	 * @since	5.4.3
	 * @access	public
	 */
	public static function getOtpConfig($userId = null)
	{
		$user = JFactory::getUser($userId);

		$model = EB::getJoomlaUserModel();

		return $model->getOtpConfig($user->id);
	}

	/**
	 * Retrieves the override path for a specific type of logo. It could either be schema, watermarks etc.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function getLogoOverridePath($type)
	{
		$path = '/images/easyblog_override/' . $type . '/logo.png';

		return $path;
	}

	 /**
	  * Converts characters to HTML entities for Schema structure data
	  *
	  * @since	5.4
	  * @access	public
	  */
	public static function normalizeSchema($schemaContent)
	{
		// Converts characters to HTML entities
		$schemaContent = htmlentities($schemaContent, ENT_QUOTES);

		// Remove backslash symbol since this will caused invalid JSON data
		$schemaContent = stripslashes($schemaContent);

		return $schemaContent;
	}

	/**
	 * Return the last date of the current month
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function getLastDayOfCurrentMonth($dateString)
	{
		// Converting string to Unix timestamp
		$unixTimestamp = strtotime($dateString);

		// Last date of current month
		$lastDayOfCurrentMonth = date("Y-m-t", $unixTimestamp);

		return $lastDayOfCurrentMonth;
	}

	/**
	 * Determines if a list of posts contain the block by type given
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function hasBlockType($type, $posts)
	{
		if (!$posts || !is_array($posts)) {
			return false;
		}

		$hasBlockType = false;

		foreach ($posts as $post) {
			$property = 'has' . ucfirst($type);

			if ($post->$property) {
				$hasBlockType = true;
				break;
			}
		}

		return $hasBlockType;
	}

	/**
	 * Creates a new instance of the GIPHY library.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function giphy()
	{
		$giphy = FH::giphy(self::fd());

		return $giphy;
	}

	/**
	 * Creates a new instance of the Unsplash library.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function unsplash()
	{
		require_once(dirname(__FILE__) . '/unsplash/unsplash.php');

		$unsplash = new EasyBlogUnsplash();

		return $unsplash;
	}

	/**
	 * Creates a new instance of the Polls library.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function polls($id)
	{
		require_once(dirname(__FILE__) . '/polls/polls.php');

		$polls = new EasyBlogPolls($id);

		return $polls;
	}

	/**
	 * Set the current category theme
	 *
	 * @since	6.0.7
	 * @access	public
	 */
	public static function setCategoryTheme($theme)
	{
		self::$categoryTheme = $theme;
	}

	/**
	 * Retrieve the current category theme
	 *
	 * @since	6.0.7
	 * @access	public
	 */
	public static function getCategoryTheme()
	{
		return self::$categoryTheme;
	}
}

// We need to exclude EasyBlogHelper class for Joomla 4 since we do not use their API for J4
// Only for non-builder environment
if (!defined('EASYBLOG_COMPONENT_CLI') && EB::isFoundryEnabled() && !FH::isJoomla4()) {

// We need to leave this class here as
// 3rd party extensions who are lazy to update their integrations
// still relies on this class
class EasyBlogHelper extends EB {}

}
