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

require_once(__DIR__ . '/libraries/compiler.php');
require_once(__DIR__ . '/libraries/minifier.php');
require_once(__DIR__ . '/libraries/builder.php');
require_once(__DIR__ . '/libraries/analyzer.php');

use Foundry\Libraries\StyleSheets;

class EasyBlogStylesheet extends EasyBlog
{
	public $ns = 'EASYBLOG';
	public $baseurl = null;

	private $class = __CLASS__;

	public $workspace = array(
		'site'       => null,
		'site_base'  => null,
		'admin'      => null,
		'admin_base' => null,
		'override'   => null
	);


	public $location;
	public $name;
	public $isOverride = false;
	public $overrideStylesheet;
	static $attached = array();
	static $filetypes = array(
		'ats'     => array('css', 'minified'),
		'less'    => array('less', 'css', 'minified'),
		'css'     => array('css', 'minified'),
		'section' => array('less', 'css', 'minified')
	);

	const FILE_STATUS_NEW       = -1;
	const FILE_STATUS_UNCHANGED = 0;
	const FILE_STATUS_MODIFIED  = 1;
	const FILE_STATUS_MISSING   = 2;
	const FILE_STATUS_REMOVED   = 3;
	const FILE_STATUS_UNKNOWN   = 4;

	public $isModule = false;
	public $themeName = '';
	public $environment = 'production';

	public function __construct($location, $theme, $useOverride)
	{
		parent::__construct();

		if (!defined('EASYBLOG_COMPONENT_CLI')) {
			$this->environment = $this->config->get('main_environment');
		}

		// Determines if this is an override request based on the location path
		$isOverride = preg_match('/(.*)(-override)$/', $location, $parts);

		// Internally, override is a location.
		if ($useOverride) {
			$location = 'override';
		}

		$this->baseurl = '';

		// If cdn is enabled, we need to update the base url
		$cdn = EB::getCdnUrl();

		if ($cdn && !EB::isFromAdmin()) {
			$this->baseurl = $cdn;
		}

		$this->themeName = $theme;

		$this->workspace = $this->getDefaultWorkspace();
		$this->workspace = array_merge($this->workspace, array($location => $theme));
		$this->location = $isOverride ? $parts[1] : $location;
		$this->name = $this->workspace[$isOverride ? 'override' : $this->location];
		$this->isOverride = $isOverride;

		// The following views should always render from wireframe
		if ($this->location == 'composer') {
			$this->themeName = 'wireframe';
		}
	}

	public function getDefaultWorkspace()
	{
		static $defaultWorkspace;

		if (!isset($defaultWorkspace)) {
			$defaultWorkspace = array(
				'site'=> strtolower($this->config->get('theme_site')),
				'site_base' => strtolower($this->config->get('theme_site_base')),
				'admin' => strtolower($this->config->get('theme_admin')),
				'admin_base' => strtolower($this->config->get('theme_admin_base'))
			);
		}

		return $defaultWorkspace;
	}

	/**
	 * Compiles css files
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function build($preset = 'cache', $options=array())
	{
		$builder = $this->builder();
		$task = $builder->run($preset, $options);

		return $task;
	}

	/**
	 * Retrieves the builder instance
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function builder()
	{
		$builder = new EasyBlogStylesheetBuilder($this);

		return $builder;
	}

	/**
	 * Creates a new compiler instance
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function compiler()
	{
		$compiler = new EasyBlogStylesheetCompiler($this);

		return $compiler;
	}

	public function minifier()
	{
		if (!isset($minifier)) {
			$minifier = new EasyBlogStylesheetMinifier($this);
		}

		return $minifier;
	}

	public function type()
	{
		// ATS
		$manifestFile = $this->file('manifest');
		if (JFile::exists($manifestFile)) {
			$type = 'ats';
			return $type;
		}

		// LESS
		$lessFile = $this->file('less');
		if (JFile::exists($lessFile)) {
			$type = 'less';
			return $type;
		}

		// CSS
		$cssFile = $this->file('css');
		if (JFile::exists($cssFile)) {
			$type = 'css';
			return $type;
		}

		// Fallback is always css
		return 'css';
	}

	/**
	 * Retrieves the path of a given folder type
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function folder($name = 'current', $force = false)
	{
		static $cache = array();

		if (!isset($cache[$name]) || $force || defined('EASYBLOG_COMPONENT_CLI')) {

			$base = $this->location == 'composer' || $this->location == 'dashboard' || $this->location == 'site' ? JPATH_ROOT : JPATH_ADMINISTRATOR;
			$theme = $this->themeName;

			if ($name == 'site_base') {
				$theme = 'wireframe';
			}

			if ($name == 'admin_base') {
				$theme = 'default';
			}

			if ($name == 'composer' || $name == 'dashboard' || $name == 'site' || $name == 'site_base' || $name == 'admin' || $name == 'admin_base') {
				$folder = $base . '/components/com_easyblog/themes/' . $theme . '/styles';
			}

			if ($name == 'cache') {
				$folder = $base . '/components/com_easyblog/themes/' . $theme . '/styles/_cache';
			}

			if ($name == 'log') {
				$folder = $base . '/components/com_easyblog/themes/' . $theme . '/styles/_log';
			}

			if ($name == 'media') {
				$folder = EBLOG_MEDIA;
			}

			if ($name == 'component') {
				$folder = EBLOG_MEDIA . '/styles';
			}

			if ($name == 'foundry' || $name == 'global') {
				$folder = EBLOG_MEDIA . '/styles/foundry';
			}

			if ($name == 'root') {
				$folder = JPATH_ROOT;
			}

			if ($name == 'current') {
				$folder = $this->folder($this->location);
			}

			$folder = JPATH::clean($folder, '/');
			$cache[$name] = $folder;
		}

		return $cache[$name];
	}

	/**
	 * Retrieves the path of the folder to a specific file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function file($filename, $type = null)
	{
		// Default file options
		$defaultOptions = array(
								'location' => $this->location,
								'filename' => 'style',
								'type' => $type,
								'seek' => false
							);
		// Current options
		$options = array();

		// When passing in an object.
		// $this->file(array('location'=>'override', 'type'=>'css'));
		if (is_array($filename)) {
			$options = $filename;

		// When passing in type or filename + type pair.
		// $this->file('css') returns 'path_to_location/style.css'
		// $this->file('photos', 'css') returns 'path_to_location/photos.css'
		} else {
			$numargs = func_num_args();
			if ($numargs===1) $options['type'] = $filename;
			if ($numargs===2) $options['filename'] = $filename;
		}

		// Extract options as variables
		$options = array_merge($defaultOptions, $options);
		extract($options);

		// If we should seek for the file according
		// to the list of import ordering locations.
		if ($seek) {

			// Get list of import ordering locations
			$locations = EasyBlogStylesheetCompiler::importOrdering($this->location . ($this->isOverride ? '-override' : ''));

			// Go through each of the location
			foreach ($locations as $location) {

				$file = $this->file(array(
					'location' => $location,
					'filename' => $filename,
					'type' => $type
				));

				// and return if the file exists
				if (JFile::exists($file)) return $file;
			}

			// If file could not be found, return file from current location.
			$file = $this->file(array(
				'location' => $this->location,
				'filename' => $filename,
				'type' => $type
			));

			return $file;
		}

		// Get current eb version
		$version = EB::getLocalVersion();

		// Construct filename without extension
		$folder = $this->folder($location);

		switch ($type) {

			case 'worksheet':
			case 'less':
				$file = "$folder/$filename.less";
				break;

			case 'stylesheet':
			case 'css':
				$file = "$folder/$filename.css";
				break;

			case 'minified':
				$file = $folder . '/' . $filename . '-' . $version . '.min.css';
				break;

			case 'manifest':
			case 'json':
				$file = "$folder/$filename.json";
				break;

			case 'fallback':
				$file = "$folder/$filename.default.css";
				break;

			case 'config':
			case 'xml':
				$file = "$folder/$filename.default.xml";
				break;

			case 'log';
				$folder = $this->folder('log');
				$file = "$folder/$filename.json";
				break;

			case 'cache':
				$folder = $this->folder('cache');
				$file = "$folder/$filename.json";
				break;

			case 'variables':
				$file = "$folder/variables.less";
				break;
		}

		return $file;
	}

	public function relative($dest, $root='', $dir_sep='/')
	{
		$root = explode($dir_sep, $root);
		$dest = explode($dir_sep, $dest);
		$path = '.';
		$fix = '';
		$diff = 0;

		for ($i = -1; ++$i < max(($rC = count($root)), ($dC = count($dest)));) {

			if (isset($root[$i]) and isset($dest[$i])) {

				if ($diff) {
					$path .= $dir_sep. '..';
					$fix .= $dir_sep. $dest[$i];
					continue;
				}

				if ($root[$i] != $dest[$i]) {
					$diff = 1;
					$path .= $dir_sep. '..';
					$fix .= $dir_sep. $dest[$i];
					continue;
				}

			} elseif (!isset($root[$i]) and isset($dest[$i])) {

				for($j = $i-1; ++$j < $dC;) {
					$fix .= $dir_sep. $dest[$j];
				}
				break;

			} elseif (isset($root[$i]) and !isset($dest[$i])) {

				for($j = $i-1; ++$j < $rC;) {
					$fix = $dir_sep. '..'. $fix;
				}
				break;
			}
		}

		$rel = $path . $fix;
		$rel = (substr($rel, 0, 2)=='./') ? substr($rel, 2) : '';

		return $rel;
	}

	/**
	 * Retrieves the manifest mapping
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getManifestMap()
	{
		if ($this->location == 'composer') {
			return 'composer';
		}

		// By default we assume the standard style should be used
		return 'style';
	}

	/**
	 * Get a list of manifest contents
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function manifest()
	{
		$manifestFile = $this->file('manifest');
		$manifestExists = JFile::exists($manifestFile);

		// If manifest file exists,
		if ($manifestExists) {

			// read manifest file,
			$manifestData = file_get_contents($manifestFile);

			// and parse manifest data.
			$manifestContent[$this->location] = json_decode($manifestData, true);
		}

		// If no manifest file found or manifest could not be parsed, assume simple stylesheet.
		// Simple stylesheet does not contain sections, the bare minimum is a single "style.css" file.
		// If it has a "style.less" file, then this less file is considered the source stylesheet where "style.css" is compiled from, else "style.css" is considered the source stylesheet.
		if (empty($manifestContent[$this->location]) || !is_array($manifestContent[$this->location])) {
			$manifestContent[$this->location] = array('style' => array('style'));
		}

		$sections = array();

		$map = $this->getManifestMap();

		if (!isset($sections[$map])) {
			$sections[$map] = array($map => $manifestContent[$this->location][$map]);
		}

		return $sections[$map];
	}

	public function uri($filename, $type = null)
	{
		$path = is_array($filename) ? $this->path($filename) : $this->path($filename, $type);
		$uri = $this->baseurl . '/' . $path;

		return $uri;
	}

	/**
	 * Allows caller to compile the less files and combine them into a single style.css file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compile($section, $options=array())
	{
		$compiler = $this->compiler();
		$task = $compiler->run($section, $options);
		return $task;
	}

	/**
	 * Minifies the css file
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function minify($section, $options=array())
	{
		$minifier = $this->minifier();
		$task = $minifier->run($section, $options);
		return $task;
	}

	/**
	 * Attaches font awesome css library
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function attachFontawesome()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			StyleSheets::load('fontawesome');

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Attaches stylesheet on the site.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function attach($minified = true, $allowOverride = true, $customCategoryTemplate = null)
	{
		static $loaded = array();

		if (isset($loaded[$this->location]) && $loaded[$this->location] === true) {
			return true;
		}

		$isDev = $this->environment == 'development';

		// Ensure that this block of codes only executs once
		$loaded[$this->location] = true;
		$build = false;

		// If we're in a development environment, always cache compile stylesheet and
		// attached uncompressed stylesheets.
		if ($isDev) {
			$build = true;
			$minified = false;
		}

		// Rebuild stylesheet on page load if necessary
		if ($build) {
			$task = $this->build($this->environment);

			// This generates build log in the browser console.
			if ($task->state == 'error') {
				$script = EB::script();
				$script->set('task', $task);
				$script->attach('admin/stylesheet/log');
			}
		}

		// Determines if the viewer is viewing the admin section.
		if (EB::isFromAdmin()) {
			$extension = $isDev ? 'css' : 'min.css';
			StyleSheets::add('/media/foundry/css/joomla-backend.' . $extension, 'component');

			$allowOverride = false;
			$this->process($minified, $allowOverride);

			return;
		}

		// If there's a custom category theme, we need to check for it here
		if ($customCategoryTemplate) {

			$overrideFiles = array();

			$categoryCustomThemeFile = $this->getCategoryCustomThemeStyle($customCategoryTemplate, 'style', $minified);
			if ($categoryCustomThemeFile) {
				$overrideFiles[] = $this->baseurl . '/' . $categoryCustomThemeFile;
			}

			// Check if there's a css file in /templates/JOOMLA_TEMPLATE/html/com_easyblog/themes/THEME_NAME/styles/custom.css
			$path = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/themes/' . $customCategoryTemplate . '/styles/custom.css';
			if (JFile::exists($path)) {
				$customURI = $this->baseurl . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/themes/' . $customCategoryTemplate . '/styles/custom.css';
				$overrideFiles[] = $customURI;
			}

			if ($overrideFiles) {
				foreach ($overrideFiles as $customFile) {
					StyleSheets::add($customFile, 'override');
				}
				return;
			}

			// If there is no custom css file, we should revert to the currently configured theme
			$this->loadDefaultTheme();
		}

		$uris = array();

		// Determine the type of stylesheet to attach
		$type = $minified ? 'minified' : 'css';

		// Process the stylesheet
		$this->process($minified, $allowOverride);

		// Custom stylesheets
		$file = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/styles/custom.css';

		if (JFile::exists($file)) {
			$customCssFile = $this->baseurl . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/styles/custom.css';
			StyleSheets::add($customCssFile, 'override');
		}

		// Render EB typography.
		if ($this->config->get('enable_typography')) {
			$typographyPath = JPATH_ROOT . '/components/com_easyblog/themes/wireframe/styles/typography.css';

			if (JFile::exists($typographyPath)) {
				StyleSheets::add('/components/com_easyblog/themes/wireframe/styles/typography.css', 'override');
			}
		}
	}

	/**
	 * Force system to load the configured theme
	 *
	 * @since	5.4.
	 * @access	public
	 */
	public function loadDefaultTheme()
	{
		$this->workspace['site'] = $this->config->get('theme_site');
		$this->themeName = $this->config->get('theme_site'); // reset the theme to use configured one.

		// now let call folder function to regenerate the cache so that
		// any subsequent call to folder will get the correct theme.
		$this->folder('site', true);
	}

	/**
	 * Determines if there is category custom css file
	 *
	 * @since	5.4.
	 * @access	public
	 */
	private function getCategoryCustomThemeStyle($customTheme, $file = 'style', $minified = true)
	{
		// lets check if this category custom theme has the require style.min.css or not. #2391
		// Check if there's a css file in /templates/JOOMLA_TEMPLATE/html/com_easyblog/themes/THEME_NAME/styles/style.min.css

		// Construct filename without extension
		$folder = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easyblog/themes/' . $customTheme . '/styles';
		$path = $folder . '/' . $file . '.css';

		if ($minified || $this->environment == 'production') {
			$version = EB::getLocalVersion();
			$path = $folder . '/' . $file . '-' . $version . '.min.css';
		}

		// if style.css override file not exists, stop here.
		if (!JFile::exists($path)) {
			return false;
		}

		// remove the root path.
		$uri = $this->strip_root($path);

		// RTL stylesheets
		$lang = JFactory::getLanguage();
		$isRtl = $lang->isRTL();
		$isAdmin = EB::isFromAdmin();

		// Only frontend will be using RTL css file
		if ($isRtl && !$isAdmin) {
			$uri = str_ireplace('.css', '-rtl.css', $uri);
		}

		return $uri;
	}

	/**
	 * Determines if a css file has an override
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function hasOverride()
	{
		if ($this->isOverride) {
			return false;
		}

		$overrideFile = $this->file(array('location' => 'override', 'type' => 'css'));
		$hasOverride = JFile::exists($overrideFile);

		return $hasOverride;
	}

	/**
	 * Determines if the css should be added to the site
	 *
	 * @since	5.3.3
	 * @access	public
	 */
	public function shouldAttachCss($location)
	{
		static $attach = array();

		if (!isset($attach[$location])) {
			$attach[$location] = true;
			$view = $this->input->get('view', '', 'word');

			if ($location == 'site' && !$this->config->get('layout_css')) {
				$attach[$location] = false;
			}
		}

		return $attach[$location];
	}

	/**
	 * Attaches the stylesheet
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function process($minified=true, $allowOverride=true)
	{
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$isAdmin = EB::isFromAdmin();

		// Only add this in production mode
		if ($this->environment == 'production') {
			$target = array(
							'location' => $this->location,
							'type' => 'minified'
						);

			if ($this->location == 'site' || $this->location == 'admin') {
				$target['filename'] = 'style';
			}

			if ($this->location == 'composer') {
				$target['filename'] = 'composer';
			}

			$uri = $this->uri($target);

			// Stop because this stylesheet
			// has been attached.
			if (isset(self::$attached[$uri])) {
				return;
			}

			// RTL stylesheets
			$lang = JFactory::getLanguage();
			$isRtl = $lang->isRTL();

			// Only frontend will be using RTL css file
			if ($isRtl && (!$isAdmin || $this->location == 'composer')) {
				$uri = str_ireplace('.min.css', '-rtl.min.css', $uri);
			}

			// Attach to document head.
			if ($this->shouldAttachCss($this->location)) {
				StyleSheets::add($uri, 'component');
			}

			// Remember this stylesheet so
			// we won't reattach it again.
			self::$attached[$uri] = true;

			return self::$attached[$uri];
		}

		// Load manifest file.
		$manifest = $this->manifest();

		// Get only the current location manifest
		$uris = array();

		foreach ($manifest as $group => $sections) {
			// Determine the type of stylesheet to attach
			$type = $minified ? 'minified' : 'css';

			// Build path options
			$target = [
					'location' => $this->isOverride ? 'override' : $this->location,
					'filename' => $group,
					'type' => $type
				];

			// Fallback to css if minified not exists,
			// only for template overrides because
			// we don't want too much disk i/o.
			if ($this->isOverride && $minified) {

				$minifiedFile = $this->file($target);

				if (!JFile::exists($minifiedFile)) {
					$target['type'] = 'css';
				}
			}

			// Get stylesheet uri.
			$uri = $this->uri($target);
			$uris[] = $uri;

			// Stop because this stylesheet
			// has been attached.
			if (isset(self::$attached[$uri])) {
				return;
			}

			// RTL stylesheets
			$lang = JFactory::getLanguage();
			$isRtl = $lang->isRTL();

			// Only frontend will be using RTL css file
			if ($isRtl && !$isAdmin) {
				$uri = str_ireplace('.css', '-rtl.css', $uri);
			}

			// Attach to document head.
			if ($this->shouldAttachCss($this->location)) {
				StyleSheets::add($uri, 'component');
			}

			// Remember this stylesheet so
			// we won't reattach it again.
			self::$attached[$uri] = true;
		}

		return $uris;
	}

	public function path($filename, $type=null)
	{
		$path = is_array($filename) ? $this->file($filename) : $this->file($filename, $type);

		$path = $this->strip_root($path);

		return $path;
	}

	public function sections()
	{
		// static $sections;

		if (isset($sections)) return $sections;

		// Get manifest
		$manifest = $this->manifest();

		// Merge all sections in a single array
		$sections = array();
		foreach ($manifest as $group => $_sections) {
			$sections = array_merge($sections, $_sections);
		}

		// Remove duplicates
		$sections = array_unique($sections);

		return $sections;
	}

	public function strip_root($path='')
	{
		$root = JPATH_ROOT;
		$root_win = str_replace('\\', '/', $root);

		if (strpos($path, $root)===0) {
			$path = substr_replace($path, '', 0, strlen($root));
		} else if (strpos($path, $root_win)===0) {
			$path = substr_replace($path, '', 0, strlen($root_win));
		}

		// For some site's the root path is just / and it's probably caused by chroot
		if ($root === '/') {
			return $path;
		}

		// Strip trailing slash
		return substr($path, 1);
	}
}
