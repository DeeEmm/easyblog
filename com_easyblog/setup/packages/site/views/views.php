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

jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');

class EasyBlogView extends JViewLegacy
{
	protected $app = null;
	protected $my = null;
	protected $customTheme = null;
	protected $props = [];

	// Use to allow views to define their params
	protected $params = null;

	public $paramsPrefix = '';

	public function __construct()
	{
		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->my = JFactory::getUser();
		$this->config = EB::config();
		$this->info = EB::info();
		$this->jconfig = EB::jconfig();
		$this->acl = EB::acl();

		// Default empty params
		$this->params = $this->getDefaultParams();

		// If this is an ajax document, we should pass the $ajax library to the client
		if ($this->doc->getType() == 'ajax') {
			EB::loadLanguages();

			$this->ajax = EB::ajax();
		}

		// Create an instance of the theme so child can start setting variables to it.
		$this->theme = EB::themes();

		if (method_exists($this, 'defineParams')) {
			$params = $this->defineParams();

			if ($params) {
				$this->params = $params;
			}
		}

		if ($this->params) {
			$this->theme->setParams($this->params);
		}

		// Set the input object
		$this->input = EB::request();
	}

	/**
	 * Allows child to set variables
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function set($key, $value = '')
	{
		if ($this->doc->getType() == 'json') {
			$this->props[$key] = $value;

			return;
		}

		$this->theme->set($key, $value);
	}

	/**
	 * Allows children to check for acl
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function checkAcl($rule, $default = null)
	{
		$allowed = $this->acl->get($rule, $default);

		if (!$allowed) {
			throw EB::exception(JText::_('COM_EASYBLOG_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'), 500);
		}

		return true;
	}

	/**
	 * Responsible to render the css files on the head
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function renderHeaders()
	{
		// Load js stuffs
		$view = $this->input->get('view', '', 'cmd');

		// Determines which js section to initialize
		$section = 'site';

		if ($view == 'composer' || $view == 'templates') {
			$section = 'composer';
		}

		EB::init($section);

		// Get the theme on the site
		$theme = $this->config->get('theme_site');

		if ($this->customTheme) {
			$theme = $this->customTheme;
		}

		// Attach the theme's css
		$stylesheet = EB::stylesheet($section, $theme);

		// Attach fontawesome stylesheet if needed to
		if ($this->config->get('css_fontawesome') || $section == 'composer') {
			$stylesheet->attachFontawesome();
		}

		// Allow caller to invoke recompiling of the entire css
		if ($this->input->get('compileCss') && FH::isSiteAdmin()) {
			$result = $stylesheet->build('full');

			header('Content-type: text/x-json; UTF-8');
			echo json_encode($result);
			exit;
		}

		$stylesheet->attach(true, true, $this->customTheme);

		// Render the custom styles
		$theme = EB::themes();
		$customCss = $theme->output('site/structure/css');

		// This custom css doesn't need to render on the composer page
		if ($view != 'composer') {
			// Compress custom css
			$customCss = FH::minifyCSS($customCss);

			$this->doc->addCustomTag($customCss);
		}
	}

	/**
	 * Allows caller to set a custom theme
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function setTheme($theme)
	{
		$this->customTheme = $theme;

		$this->theme->setCategoryTheme($theme);

		// Set the theme globally
		EB::setCategoryTheme($theme);
	}

	/**
	 * Responsible to display the entire component output
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function display($tpl = null)
	{
		// Response for json calls
		if ($this->doc->getType() == 'json') {

			$callback = $this->input->get('callback', '', 'cmd');
			$output = json_encode($this->props);

			if ($callback) {
				$output = $callback . '(' . $output . ')';
			}

			header('Content-type: text/x-json; UTF-8');
			echo $output;
			exit;
		}

		// Standard html response
		if ($this->doc->getType() == 'html') {
			EBCompat::renderjQueryFramework();

			// Check for webview access for EB Table. #2757
			$this->checkWebviewAccess();

			$this->renderHeaders();

			// Get the contents from the view
			$namespace  = 'site/' . $tpl;

			$contents = $this->theme->output($namespace);

			$suffix = $this->getMenuSuffix();
			$view = $this->getName();
			$layout = $this->getLayout();

			// We need to append the contents back into the main structure
			$theme = EB::themes();

			$tmpl = $this->input->get('tmpl');

			// Get the toolbar
			$contributionHeader = false;

			if ($view == 'entry' && $layout != 'preview') {

				$id = $this->input->get('id', 0, 'int');
				$post = EB::post($id);

				if (!$post->isStandardSource()) {
					$contribution = $post->getBlogContribution();

					$contributionHeader = $contribution->getHeader();
				}
			}

			// Push notifications
			if (EB::push()->isEnabled()) {
				EB::push()->generateScripts();
			}

			// We attach the script tags on the bottom of the page
			$scripts = EB::helper('Scripts')->getScripts();

			$lang = JFactory::getLanguage();
			$rtl = $lang->isRTL();

			// Load easysocial headers when viewing posts of another person
			$miniheader = '';

			$showMiniHeader = $this->config->get('integrations_easysocial_miniheader');

			// Only work for Easysocial 2.0. Only display if there is no contribution header.
			if ($showMiniHeader && $view == 'entry' && EB::easysocial()->exists() && !EB::easysocial()->isLegacy() && !$contributionHeader && $layout != 'preview') {
				ES::initialize();

				if (ES::user()->hasCommunityAccess()) {
					if (!isset($post)) {
						$id = $this->input->get('id', 0, 'int');
						$post = EB::post($id);
					}

					$user = ES::user($post->getAuthor()->id);

					$miniheader = ES::themes()->html('miniheader.user', $user);
				}
			}

			// For image popups and container
			$loadImageTemplates = $view == 'composer' ? false : true;

			// Sanitize the layout to ensure users do not try to break things
			$layout = preg_replace("/[^A-Za-z0-9?!]/", '', $layout);

			$theme->set('loadImageTemplates', $loadImageTemplates);
			$theme->set('miniheader', $miniheader);
			$theme->set('rtl', $rtl);
			$theme->set('bootstrap', '');
			$theme->set('jscripts', $scripts);
			$theme->set('contents', $contents);
			$theme->set('suffix', $suffix);
			$theme->set('layout', $layout);
			$theme->set('view', $view);

			$output = $theme->output('site/structure/default');

			echo $output;
			return;
		}
	}

	/**
	 * Sets view in breadcrumbs
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function setViewBreadcrumb($view = null)
	{
		if (is_null($view)) {
			$view = $this->getName();
		}

		if (!EBR::isCurrentActiveMenu($view)) {
			$this->setPathway(JText::_('COM_EASYBLOG_BREADCRUMB_' . strtoupper($view)));

			return true;
		}

		return false;
	}

	/**
	 * Retrieve the menu suffix for a page
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getMenuSuffix()
	{
		$menu = $this->app->getMenu()->getActive();
		$suffix = '';

		if ($menu) {
			$params = $menu->getParams();
			$suffix = $params->get('pageclass_sfx', '');
		}

		return $suffix;
	}

	/**
	 * Generate a canonical tag on the header of the page
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function canonical($url, $route = true, $external = true)
	{
		$isEnabledSEF = EBR::isSefEnabled();

		if ($route) {
			$url = EBR::getRoutedUrl($url, true, $external, true);
		}

		$url = $this->escape($url);

		// some of the URL already convert to htmlentities so if convert again, it will show duplicate
		if (!$isEnabledSEF) {
			$url = str_replace("&amp;amp;", "&amp;", $url);
		}

		$this->doc->addHeadLink($url, 'canonical');
	}

	/**
	 * Generate a rel tag on the header of the page
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function amp($url, $route = true)
	{
		$isEnabledSEF = EBR::isSefEnabled();

		if ($route && $isEnabledSEF) {
			$url = EBR::_($url, false, null, false, true);
		}

		if (!$isEnabledSEF) {
			// manually add the domain name if the site doesn't activate the SEF
			$domain = $this->getSiteDomainName(false);
			$url = $domain . $url;
		}

		$this->doc->addHeadLink($this->escape($url), 'amphtml');
	}

	/**
	 * Retrieve the site domain name with protocol
	 *
	 * @since   6.0.11
	 * @access  public
	 */
	public function getSiteDomainName($withSubfolder = true)
	{
		if ($withSubfolder) {
			$baseURL = rtrim(JURI::root(), '/');
			return $baseURL;
		}

		$subfolder = JURI::root(true);
		$baseURL = str_ireplace($subfolder, '', rtrim(JURI::root(), '/'));

		if (!empty($subfolder)) {
			$subfolder = $subfolder . '/';
			$baseURL = str_ireplace($subfolder, '', rtrim(JURI::root()));
		}

		return $baseURL;
	}

	/**
	 * Generate a preload link tag on the header of the page
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function preload($src, $type, $options = [])
	{
		$attributes = array_merge(['as' => $type], $options);

		$this->doc->addHeadLink($src, 'preload', 'rel', $attributes);
	}

	/**
	 * Retrieves the active menu
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getActiveMenu()
	{
		return $this->app->getMenu()->getActive();
	}

	/**
	 * Retrieves the menu params
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getActiveMenuParams($internalConfigPrefix = '')
	{
		$activeMenu = $this->getActiveMenu();

		if (!$activeMenu) {
			return;
		}

		// To avoid the menu being contaminated, we should clone this params to ensure that any "setting" of property would not be affected on subsequent calls
		$menuParams = clone $activeMenu->getParams();

		$model = EB::model('Menu');
		$params = $model->getCustomMenuParams($activeMenu->id, $menuParams, $internalConfigPrefix);

		return $params;
	}

	/**
	 * Generates a default set of menu params
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getDefaultParams()
	{
		$default = EB::getMenuParams('0', 'listing');

		$defaultParams = $default->toArray();

		$params = new JRegistry();

		if ($defaultParams) {
			foreach ($defaultParams as $key => $val) {
				$params->set($key, $val);
			}
		}

		return $params;
	}

	/**
	 * Retrieve any queued messages from the system
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getMessages()
	{
		$messages = EB::getMessageQueue();

		return $messages;
	}

	/**
	 * Adds the breadcrumbs on the site
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function setRssFeed($link ='')
	{
		return EB::feeds()->addHeaders($link);
	}

	/**
	 * Adds the breadcrumbs on the site
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function setPathway($title, $link ='')
	{
		// Get the pathway
		$pathway = $this->app->getPathway();

		// set this option to true if the breadcrumb didn't show the EasyBlog root menu.
		$showRootMenuItem = false;

		// Translate the pathway item
		$title = JText::_($title);
		$state = $pathway->addItem($title, $link);

		return $state;
	}

	/**
	 * Renders JSON output on the page
	 *
	 * @since	5.1
	 * @access	public
	 */
	protected function outputJSON($output = null)
	{
		echo '<script type="text/json" id="ajaxResponse">' . json_encode($output) . '</script>';
		exit;
	}

	/**
	 * Responsible to modify the title whenever necessary. Inherited classes should always use this method to set the title
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setPageTitle($title, $pagination = null , $addSitePrefix = false )
	{
		$page = null;
		$pageTitleSeparator = JText::_('COM_EB_PAGE_TITLE_SEPARATOR');

		if ($addSitePrefix) {
			$addTitle = $this->jconfig->get('sitename_pagetitles');
			$sitenameOrdering = $this->config->get('sitename_position', 'default');

			if ($sitenameOrdering == 'after' && $addTitle == 2) {

				// Only apply if the joomla site name setting is using 'after'
				$titleTmp = explode($pageTitleSeparator, $title);

				if (isset($titleTmp[0]) && $titleTmp[0]) {
					$title = $titleTmp[0] . $pageTitleSeparator . JText::_($this->config->get('main_title'));

					if (isset($titleTmp[1]) && $titleTmp[1]) {
						$title .= $pageTitleSeparator . $titleTmp[1];
					}
				}

			} else {
				// Normal ordering
				$title .= $pageTitleSeparator . JText::_($this->config->get('main_title'));
			}
		}

		if ($pagination && is_object($pagination)) {
			$page = $pagination->get('pages.current');

			// Append the current page if necessary.
			$title .= $page == 1 ? '' : ' - ' . JText::sprintf('COM_EASYBLOG_PAGE_NUMBER', $page);
		}

		$this->doc->setTitle($title);
	}

	/**
	 * Sets the rss author email
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getRssEmail($author)
	{
		if ($this->jconfig->get('feed_email') == 'none') {
			return;
		}

		if ($this->jconfig->get('feed_email') == 'author') {
			return $author->user->email;
		}

		return $this->jconfig->get('mailfrom');
	}

	/**
	 * Experimental method to determine if the view and layout is webview-able
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isWebviewable()
	{
		$view = $this->getName();
		$layout = $this->getLayout();

		$allowedView = [
			'composer',
			'templates',
			'dashboard',
			'entry'
		];

		$allowedLayout = [
			'dashboard' => 'profile',
			'entry' => 'preview'
		];

		if (!empty($allowedView)) {
			$allowed = false;

			foreach ($allowedView as $allow) {
				if ($view === $allow) {
					$allowed = true;

					if (isset($allowedLayout[$allow]) && $allowedLayout[$allow] !== $layout) {
						$allowed = false;
					}

					break;
				}
			}

			if (!$allowed) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check for webview access. Currently being use in EB Tablet
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function checkWebviewAccess()
	{
		if (!EB::responsive()->isEBTabletWevbiew()) {
			return;
		}

		$isWebviewable = $this->isWebviewable();

		if ($isWebviewable) {
			return;
		}

		$infoData = EB::info()->getMessage();

		ob_start();
		?>
This page is not supported for webview currently.
<script type="text/javascript">
	var data = {
		type: "close",
		info: <?php echo $infoData ? json_encode($infoData) : "false"; ?>
	};

	(window["ReactNativeWebView"] || window).postMessage(JSON.stringify(data));
</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		echo $output;
		exit;
	}
}
