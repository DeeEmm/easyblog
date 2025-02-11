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

use Foundry\Libraries\Scripts;

class EasyBlogAdminView extends JViewLegacy
{
	protected $heading = null;
	protected $desc = null;
	protected $doc = null;
	protected $my = null;
	protected $app = null;
	protected $config = null;
	protected $jconfig = null;
	protected $sidebar = true;

	public function __construct()
	{
		$this->config = EB::getConfig();
		$this->jconfig = JFactory::getConfig();
		$this->app = JFactory::getApplication();
		$this->doc = JFactory::getDocument();
		$this->my = JFactory::getUser();
		$this->input = EB::request();
		$this->info = EB::info();
		$this->help = false;

		$this->theme = EB::themes([
			'view' => $this
		]);

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = EB::ajax();
		}

		// Standardize heading
		JToolBarHelper::title(JText::_('COM_EASYBLOG'));

		parent::__construct();
	}

	/**
	 * Adds help button on the page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function addHelpButton($url)
	{
		$url = 'https://stackideas.com/docs/easyblog/' . ltrim($url, '/');

		$this->help = $url;
	}

	/**
	 * Hides back-end sidebar
	 *
	 * @since	5.4.4
	 * @access	public
	 */
	public function hideSidebar()
	{
		if (FH::isJoomla4()) {
			$this->input->set('hidemainmenu', true);
		}

		$this->sidebar = false;
	}

	/**
	 * Allows child classes to set heading of the page
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function setHeading($heading, $desc = '', $icon = '')
	{
		$this->heading = $heading;

		if (!$desc && $desc !== false) {
			$desc = $heading . '_DESC';
		}

		$this->desc = $desc;
	}

	/**
	 * Checks if the current viewer can really access this section
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function checkAccess($rule)
	{
		if (!$this->my->authorise($rule , 'com_easyblog')) {
			$this->info->set('JERROR_ALERTNOAUTHOR', 'error');
			return $this->app->redirect('index.php?option=com_easyblog');
		}
	}

	/**
	 * Override parent's implementation
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		EBCompat::renderjQueryFramework();

		// We need the core behavior to be loaded
		JHtml::_('behavior.core');

		// Set the appropriate namespace
		$namespace 	= 'admin/' . $tpl;

		// Get the child contents
		$output = $this->theme->output($namespace);

		// Get the sidebar
		$sidebar = $this->getSidebar();

		// Determine if this is a tmpl view
		$tmpl = $this->input->get('tmpl', '', 'word');

		// Prepare the structure
		$theme = EB::themes();

		// Get current version
		$version = EB::getLocalVersion();

		// Render a different structure prefix when tmpl=component
		$prefix = $tmpl == 'component' ? 'fd-window' : '';

		// Initialize all javascript frameworks
		EB::init('admin');

		// Initiliaze foundry js
		Scripts::initializeAdmin();

		$scripts = '';

		// Check if facebook token is expiring
		$model = EB::model('Oauth');
		$fbTokenExpiring = $model->getSoonTobeExpired('facebook', 7);

		$theme->set('info', $this->info);
		$theme->set('prefix', $prefix);
		$theme->set('version', $version);
		$theme->set('heading', $this->heading);
		$theme->set('desc', $this->desc);
		$theme->set('output', $output);
		$theme->set('tmpl', $tmpl);
		$theme->set('sidebar', $sidebar);
		$theme->set('jscripts', $scripts);
		$theme->set('help', $this->help);
		$theme->set('fbTokenExpiring', $fbTokenExpiring);

		$contents = $theme->output('admin/structure/default');

		// Collect all javascripts attached so that we can output them at the bottom of the page
		$scripts = EB::scripts()->getScripts();
		$this->doc->addCustomTag($scripts);

		echo $contents;

	}

	/**
	 * Proxy for setting a variable to the template.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function set($key, $value = '')
	{
		$this->theme->set($key, $value);
	}

	/**
	 * Processes counters from the menus.json
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getCounter($namespace)
	{
		static $counters = array();

		list($model, $method) = explode('/', $namespace);

		if (!isset($counters[$namespace])) {
			$model = EB::model($model);

			$counters[$namespace] = $model->$method();
		}

		return $counters[$namespace];
	}

	/**
	 * Prepares the sidebar
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getSidebar()
	{
		if (!$this->sidebar) {
			return;
		}

		$view = $this->input->get('view', '', 'cmd');
		$layout = $this->input->get('layout', '', 'cmd');

		$model = EB::model('Sidebar');
		$menus = $model->getItems($view);

		$output = EB::fd()->html('admin.sidebar', $menus, $view, $layout);

		return $output;

		$file = JPATH_COMPONENT . '/defaults/menus.json';
		$contents = file_get_contents($file);


		$layout = $this->input->get('layout', '', 'cmd');
		$result = json_decode($contents);
		$menus = [];

		foreach ($result as &$row) {

			// Normalize all properties
			$row->access = FH::normalize($row, 'access', null);
			$row->view = FH::normalize($row, 'view', '');
			$row->childs = FH::normalize($row, 'childs', []);
			$row->link = FH::normalize($row, 'link', '');
			$row->counter = FH::normalize($row, 'counter', false);
			$row->count = 0;

			$row->views = $this->getViews($row);
			$row->isActive = in_array($view, $row->views) ? true : false;

			// Check if the user is allowed to view this sidebar
			if ($row->access && !$this->my->authorise($row->access, 'com_easyblog')) {
				continue;
			}

			if (!$row->view) {
				$row->link = 'index.php?option=com_easyblog';
			}

			if (!$row->link) {
				$row->link = 'index.php?option=com_easyblog&view=' . $row->view;
			}

			// Parent would always get the counter from its child
			// If there is a counter, we need to get the count
			if ($row->counter && !$row->childs) {
				$row->count = $this->getCounter($row->counter);
			}

			if ($row->childs) {
				foreach ($row->childs as &$child) {
					$child->link = 'index.php?option=com_easyblog&view=' . $row->view;

					if ($child->url) {
						foreach ($child->url as $key => $value) {

							if (!empty($value)) {
								$child->link .= '&' . $key . '=' . $value;
							}
						}
					}

					// Processes items with counter
					if (isset($child->counter)) {
						$child->counter = $this->getCounter($child->counter);
						$child->counter = 1;
					}
				}
			}

			$menus[] = $row;
		}



		return $output;
	}

	/**
	 * Given a list of sidebar structure, determine all the views
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function getViews($menuItem)
	{
		$views = [$menuItem->view];

		if ($menuItem->childs) {
			foreach ($menuItem->childs as $childMenu) {
				$views[] = $childMenu->url->view;
			}
		}

		$views = array_unique($views);

		return $views;
	}
}
