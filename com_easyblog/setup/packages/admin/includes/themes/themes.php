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

require_once(__DIR__ . '/helpers/abstract.php');

class EasyBlogThemes extends EasyBlog
{
	public $vars = [];
	public $categoryTheme = '';
	public $entryParams = null;
	public $theme = null;
	public $view = null;
	public $params= [];
	public $acl = null;
	public $fd  = null;
	public $profile;

	public function __construct($options = [])
	{
		$this->config = EB::config();
		$this->jconfig = EB::jconfig();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->doc = JFactory::getDocument();
		$this->theme = $this->getCurrentTheme();

		// If this is entry view, or category view, we need to respect the theme's category
		$this->params = new JRegistry();

		// We will just set it here from the menu when this class first get instantiate.
		// The corresponding view will have to do their own assignment if the view's templates need to access this entryParams
		$this->entryParams = $this->params;

		$this->my = JFactory::getUser();
		$this->profile = EB::user();
		$this->acl = EB::acl();
		$this->view = EB::normalize($options, 'view', null);
		$this->fd = EB::fd();
	}

	/**
	 * This is used to map methods from views to be accessible by theme files
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function __call($method, $args)
	{
		if (is_null($this->view)) {
			return false;
		}

		if (!method_exists($this->view, $method)) {
			return false;
		}

		return call_user_func_array(array($this->view, $method), $args);
	}

	/**
	 * Determines the current theme used on the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	private function getCurrentTheme()
	{
		static $theme = null;

		if (is_null($theme)) {
			$theme = $this->config->get('layout_theme');

			// If it's development mode, allow user to invoke in the url to change theme.
			$environment = $this->config->get('main_environment');

			if ($environment == 'development') {
				$custom = $this->input->get('theme', '', 'word');

				if ($custom) {
					$theme = $custom;
				}
			}
		}

		return $theme;
	}

	/**
	 * Allows caller to set a custom theme
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setCategoryTheme($theme)
	{
		$this->categoryTheme = $theme;
	}

	/**
	 * Allows caller to define their own params value
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function setParams($params)
	{
		$this->params = $params;
	}

	/**
	 * Resolves a given namespace to the appropriate path
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function resolve($namespace='', $extension='php', $checkOverridden = true)
	{
		$parts     = explode('/', $namespace);
		$location  = $parts[0];
		$path      = '';
		$extension = '.' . $extension;

		unset($parts[0]);

		// For admin theme files
		if ($location === 'admin') {
			$defaultPath = JPATH_ADMINISTRATOR . '/components/com_easyblog/themes/default/' . implode('/', $parts);

			// If there is a template override on the default joomla template, we should use that instead.
			$defaultJoomlaTemplate = $this->app->getTemplate();
			$path = JPATH_ADMINISTRATOR . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/' . implode('/', $parts);
			$exists = JFile::exists($path . $extension);

			if ($exists) {
				return $path;
			}

			return $defaultPath;
		}

		// For site theme files
		if ($location === 'site') {

			$defaultJoomlaTemplate = EB::getCurrentTemplate();

			// Implode the parts back to form the namespace
			$namespace = implode('/', $parts);

			if ($checkOverridden) {
				$categoryTheme = $this->categoryTheme;

				if (!$this->categoryTheme) {
					$categoryTheme = EB::getCategoryTheme();
				}

				// Category Theme
				if ($categoryTheme) {
					$path   = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/themes/' . $categoryTheme . '/' . $namespace;
					$exists = JFile::exists($path . $extension);

					if ($exists) {
						return $path;
					}
				}

				// If there is a template override on the default joomla template, we should use that instead.
				$path = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/' . $namespace;
				$exists = JFile::exists($path . $extension);

				if ($exists) {
					return $path;
				}

				// Use global template overrides
				$path = FH::getTemplateOverrideFolder('easyblog') . '/' . $namespace;
				$exists = JFile::exists($path . $extension);

				if ($exists) {
					return $path;
				}
			}

			// If there are no overrides, we should just use the default theme in EasyBlog
			$path = EBLOG_THEMES . '/' . $this->theme . '/' . $namespace;
			$exists = JFile::exists($path . $extension);

			if ($exists) {
				return $path;
			}

			// Base Theme
			// We no longer inherit from other themes. All themes will fallback to the wireframe theme by default.
			$path = EBLOG_THEMES . '/wireframe/' . $namespace;
		}

		return $path;
	}

	/**
	 * Retrieves the path to the current theme.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPath()
	{
		$theme 	= (string) trim(strtolower($this->theme));

		return EBLOG_THEMES . '/' . $theme;
	}


	/**
	 * Renders module in a template
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderModule($position, $attributes = [], $content = null)
	{
		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');

		$buffer = '';
		$modules = JModuleHelper::getModules($position);

		// Use a standard module style if no style is provided
		if (!isset($attributes['style'])) {
			$attributes['style'] = 'xhtml';
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
	 * Determines if this is from an iphone
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isIphone()
	{
		static $iphone = null;

		if (is_null($iphone)) {
			$iphone = EB::responsive()->isIphone();
		}

		return $iphone;
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isMobile()
	{
		static $responsive = null;

		if (is_null($responsive)) {
			$responsive = EB::responsive()->isMobile();
		}

		return $responsive;
	}

	/**
	 * Determines if this is a ipad layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isIpad()
	{
		static $responsive = null;

		if (is_null($responsive)) {
			$responsive = EB::responsive()->isIpad();
		}

		return $responsive;
	}

	/**
	 * Determines if this is a tablet layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isTablet()
	{
		static $responsive = null;

		if (is_null($responsive)) {
			$responsive = EB::responsive()->isTablet();
		}

		return $responsive;
	}

	/**
	 * Retrieves the document direction. Whether this is rtl or ltr
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getDirection()
	{
		$document = JFactory::getDocument();
		return $document->getDirection();
	}

	/**
	 * Compute text and determine its noun
	 *
	 * @deprecated	6.0.0
	 */
	public function getNouns($text, $count, $includeCount = false)
	{
		return EB::string()->getNoun($text, $count, $includeCount);
	}

	/**
	 * Deprecated. Use params from views
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getParam($key, $default = null)
	{
		return $this->params->get($key, $default);
	}

	/**
	 * Retrieves the themes parameters
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getThemeParams()
	{
		static $params = [];

		if (!isset($params[$this->theme])) {
			$model = EB::model('Themes');
			$params[$this->theme] = $model->getThemeParams($this->theme);
		}

		return $params[$this->theme];
	}

	/**
	 * Formats a date.
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function formatDate($format, $dateString)
	{
		$date = EB::date($dateString, true);

		return $date->format($format);
	}

	/**
	 * Template helper
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function html($namespace)
	{
		static $language = false;

		// Load language strings from back end.
		if (!$language) {
			EB::loadLanguages();

			$language = true;
		}

		static $cache = [];

		$parts = explode('.', $namespace);
		$method = array_pop($parts);

		// Backward compatibility for those who uses template override
		if ($method === 'print') {
			$method = 'printer';
		}

		if (!isset($cache[$namespace])) {
			$fileNamespace = strtolower(implode('/', $parts));

			$file = __DIR__ . '/helpers/' . $fileNamespace . '.php';

			$exists = file_exists($file);

			if ($exists) {
				include_once($file);

				$className = implode('', $parts);
				$className = 'EasyBlogThemesHelper' . ucfirst($className);

				$cache[$namespace] = new $className();
			}
		}

		// Separate the function arguments for php 7.4 compatibility.
		$args = func_get_args();
		$args = array_splice($args, 1);

		return call_user_func_array(array($cache[$namespace], $method), $args);
	}

	/**
	 * Sets a variable on the template
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/**
	 * New method to display contents from template files
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function output($namespace, $vars=array(), $extension='php')
	{
		$path = $this->resolve($namespace, $extension);
		$extension = '.' . $extension;

		// Extract template variables
		if (!empty($vars)) {
			extract($vars);
		}

		if (isset($this->vars)) {
			extract($this->vars);
		}

		$templateFile = $path . $extension;
		$templateContent = '';

		ob_start();
			include($templateFile);
			$templateContent = ob_get_contents();
		ob_end_clean();

		// Embed script within template
		$scriptFile = $path . '.js';

		$scriptFileExists = JFile::exists($scriptFile);

		if (!$scriptFileExists) {
			$tmpPath = $this->resolve($namespace, 'php', false);
			$scriptFile = $tmpPath . '.js';
		}

		$scriptFileExists = JFile::exists($scriptFile);

		if ($scriptFileExists) {

			ob_start();
				echo '<script type="text/javascript">';
				include($scriptFile);
				echo '</script>';
				$scriptContent = ob_get_contents();
			ob_end_clean();

			// Add to collection of scripts
			if ($this->doc->getType() == 'html') {
				EB::scripts()->add($scriptContent);
			} else {

				// Append script to template content
				// if we're not on html document (ajax).
				$templateContent .= $scriptContent;
			}
		}

		return $templateContent;
	}

	/**
	 * Escapes a string
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function escape($val)
	{
		return EB::string()->escape($val);
	}
}