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

class EasyBlogScripts extends EasyBlog
{
	static $scripts = [];
	public $dependencies = [];
	public $baseurl = null;
	private $async = false;
	private $defer = false;
	private $location = '';
	public $environment = '';

	static $attached = false;

	public function __construct($location = 'site')
	{
		parent::__construct();

		// Get the base url
		$this->baseurl = JURI::root(true);

		// Get a list of foundry dependencies
		$this->dependencies = $this->getDependencies(false, $this->config->get('easyblog_jquery'));

		// Get the current environment
		$this->environment = $this->config->get('main_environment');

		// Legacy purposes
		if ($this->environment == 'static') {
			$this->environment = 'production';
		}

		// If cdn is enabled, we need to update the base url
		$cdn = EB::getCdnUrl();

		if ($this->environment == 'production' && $cdn) {
			$this->baseurl = $cdn;
		}

		$this->location = $location;

		// Hardcode the section to be on the admin if viewer is viewing the admin
		if (!defined('EASYBLOG_COMPONENT_CLI') && EB::isFromAdmin() && $this->location != 'composer') {
			$this->location = 'admin';
		}
	}

	/**
	 * Adds script into the queue
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function add($script)
	{
		self::$scripts[] = $script;
	}

	/**
	 * Allows caller to attach an external script
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function addScript($url, $forceDefer = false, $forceAsync = false)
	{
		// We should only attach scrips on html documents otherwise JDocument would hit an error
		if ($this->doc->getType() != 'html') {
			return;
		}

		$tag = $this->createScriptTag($url, $forceDefer, $forceAsync);

		$this->doc->addCustomTag($tag);
	}

	/**
	 * Attaches the necessary script libraries on the page
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function attach()
	{
		// Only attach the scripts on the page once.
		if (self::$attached) {
			return true;
		}

		// We should only attach scrips on html documents otherwise JDocument would hit an error
		if ($this->doc->getType() != 'html') {
			return;
		}

		// Add configurations about the site
		$configuration = $this->getJavascriptConfiguration();
		$this->doc->addCustomTag($configuration);

		// In production mode, we need to attach the compiled scripts
		if ($this->environment == 'production') {

			// Render EasyBlog's own jQuery to avoid conflict with EasySocial's
			$renderjQuery = $this->config->get('easyblog_jquery');
			$option = $this->input->get('option', '', 'cmd');

			if ($option == 'com_easysocial') {
				$renderjQuery = true;
			}

			// For now, if we detected the current component is com_menus, we load the complete version.
			// #920
			if (!$renderjQuery && EB::isFromAdmin() && $this->input->get('option', '', 'cmd') == 'com_menus') {
				$renderjQuery = true;
			}

			// test if ES or KMT already load Joomla jquery or not. if yes, we have to load our own jquery
			if (!$renderjQuery) {
				if (defined('COM_EASYSOCIAL_JQUERY_FRAMEWORK') || defined('COM_KOMENTO_JQUERY_FRAMEWORK') || class_exists('ES') || class_exists('KT')) {
					$renderjQuery = true;
				}
			}

			// If jquery is not rendered, we need to trigger Joomla to enforce it to load jquery
			if (!$renderjQuery) {
				define('COM_EASYBLOG_JQUERY_FRAMEWORK', 1);
				JHtml::_('jquery.framework');
			}

			$minified = true;

			$fileName = $this->getFileUri($this->location, $minified, $renderjQuery);

			$this->doc->addCustomTag($this->createScriptTag($fileName));
		}

		// In development mode, we need to attach the main entry file so the system knows which files to be rendered asynchronously.
		if ($this->environment == 'development') {

			// Render the bootloader on the page first
			$bootloader = $this->baseurl . '/media/com_easyblog/scripts/bootloader.js';
			$this->doc->addCustomTag($this->createScriptTag($bootloader));

			// Render dependencies from the core
			foreach ($this->dependencies as $dependency) {
				$path = $this->baseurl . '/media/com_easyblog/scripts/vendors/' . basename($dependency);

				$this->doc->addCustomTag($this->createScriptTag($path));
			}

			// Render easysocial's dependencies
			$scriptUrl = $this->baseurl . '/media/com_easyblog/scripts/' . $this->location . '/' . $this->location . '.js';
			$script = $this->createScriptTag($scriptUrl);

			$this->doc->addCustomTag($script);
		}

		// Determines if we should attach the migrate script
		$loadMigrateScript = $this->renderMigrateScript();

		if ($loadMigrateScript) {
			$migrate = $this->baseurl . '/media/vendor/jquery-migrate/js/jquery-migrate.min.js';

			$this->doc->addCustomTag($this->createScriptTag($migrate));
		}

		self::$attached = true;
	}

	/**
	 * Generates a configuration string for EasySocial's javascript library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getJavascriptConfiguration()
	{
		$locale = JFactory::getLanguage()->getTag();

		// moment locale mapping against joomla language
		// If the counter part doesn't exist, then we all back to the nearest possible one, or en-gb
		$momentLangMap = [
			'af-za' => 'en-gb',
			'ar-aa' => 'ar',
			'bg-bg' => 'bg',
			'bn-bd' => 'en-gb',
			'ca-es' => 'ca',
			'cs-cz' => 'cs',
			'da-dk' => 'da',
			'de-de' => 'de',
			'el-gr' => 'el',
			'en-gb' => 'en-gb',
			'en-us' => 'en-gb',
			'es-cl' => 'es',
			'es-es' => 'es',
			'fa-ir' => 'fa',
			'fi-fi' => 'fi',
			'fr-ca' => 'fr',
			'fr-fr' => 'fr',
			'he-il' => 'he',
			'hr-hr' => 'hr',
			'hu-hu' => 'hu',
			'hy-am' => 'hy-am',
			'id-id' => 'id',
			'it-it' => 'it',
			'ja-jp' => 'ja',
			'ko-kr' => 'ko',
			'lt-lt' => 'lt',
			'ms-my' => 'ms-my',
			'nb-no' => 'nb',
			'nl-nl' => 'nl',
			'pl-pl' => 'pl',
			'pt-br' => 'pt-br',
			'pt-pt' => 'pt',
			'ro-ro' => 'ro',
			'ru-ru' => 'ru',
			'sq-al' => 'sq',
			'sv-se' => 'sv',
			'sw-ke' => 'en-gb',
			'th-th' => 'th',
			'tr-tr' => 'tr',
			'uk-ua' => 'uk',
			'vi-vn' => 'vi',
			'zh-cn' => 'zh-cn',
			'zh-hk' => 'zh-cn',
			'zh-tw' => 'zh-tw'
		];

		$lcLocale = strtolower($locale);
		$momentLang = isset($momentLangMap[$lcLocale]) ? $momentLangMap[$lcLocale] : 'en-gb';

		ob_start();
?>
<!--googleoff: index-->
<script type="text/javascript">
window.ezb = window.eb = {
	"environment": "<?php echo $this->environment;?>",
	"rootUrl": "<?php echo rtrim(JURI::root(), '/');?>",
	"ajaxUrl": "<?php echo EB::fd()->getAjaxUrl($this->config->get('ajax_use_index'));?>",
	"token": "<?php echo EB::token();?>",
	"mobile": <?php echo (EB::responsive()->isMobile() || EB::responsive()->isIpad() || EB::responsive()->isTablet()) ? 'true' : 'false'; ?>,
	"ios": <?php echo EB::responsive()->isIphone() ? 'true' : 'false';?>,
	"locale": "<?php echo $locale;?>",
	"momentLang": "<?php echo $momentLang;?>",
	"direction": "<?php echo $this->doc->getDirection();?>",
	"appearance": "light",
	"version": "<?php echo md5(EB::getLocalVersion());?>",
	"scriptVersioning": true
};
</script>
<!--googleon: index-->
<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Generates script tags that should be added on the page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function createScriptTag($path, $forceDefer = false, $forceAsync = false)
	{
		$script = '<script' . (($this->defer || $forceDefer) ? ' defer' : '') . (($this->async || $forceAsync) ? ' async' : '') . ' src="' . $path . '"></script>';

		return $script;
	}

	/**
	 * Retrieves the main dependencies from vendors
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getDependencies($absolutePath = false, $jquery = true)
	{
		$coreFiles = [
			'lodash.js',
			'bootstrap.js',
			'utils.js',
			'uri.js',
			'mvc.js',
			'joomla.js',
			'module.js',
			'script.js',
			'template.js',
			'require.js',
			'iframe-transport.js',
			'server.js',
			'component.js'
		];

		// Determines if we should include jquery.easysocial.js library
		if ($jquery) {
			array_unshift($coreFiles, 'jquery.easyblog.js');
		}

		if (!$jquery) {
			array_unshift($coreFiles, 'jquery.js');
		}

		foreach ($coreFiles as &$file) {
			$file = DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'vendors' . DIRECTORY_SEPARATOR . $file;

			if ($absolutePath) {
				$file = JPATH_ROOT . $file;
			}
		}

		return $coreFiles;
	}

	/**
	 * Generates the file name
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFileName($section, $jquery = true)
	{
		$version = EB::getLocalVersion();
		$file = $section . '-' . $version;

		if (!$jquery) {
			$file .= '-basic';
		}

		return $file;
	}

	/**
	 * Generates the file path
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFileUri($section, $minified = true, $jquery = true)
	{
		$path = $this->baseurl . '/media/com_easyblog/scripts/' . $this->getFileName($section, $jquery);

		if ($minified) {
			$path .= '.min.js';
		} else {
			$path .= '.js';
		}

		return $path;
	}

	/**
	 * Retrieves the list of scripts from the queue
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getScripts()
	{
		return implode('', self::$scripts);
	}

	/**
	 * Determines if we should render the jquery migrate script
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function renderMigrateScript()
	{
		static $loadMigrateScript = null;

		if (is_null($loadMigrateScript)) {
			$loadMigrateScript = false;

			// If the site is rendering EasyBlog's jquery, we do not need the jquery-migrate
			if ($this->config->get('easyblog_jquery') || !FH::isJoomla4()) {
				return $loadMigrateScript;
			}

			// Do not need to render jquery-migrate for the front end except for the composer
			$view = $this->input->get('view', '', 'cmd');

			if (!EB::isFromAdmin() && $view != 'composer') {
				return $loadMigrateScript;
			}

			// For Joomla 4, we need to render the jquery-migrate.min.js for our scripts to work
			$loadMigrateScript = true;
		}

		return $loadMigrateScript;
	}
}
