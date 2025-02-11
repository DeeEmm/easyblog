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

class EasyBlogViewSettings extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.setting');

		$layout = $this->getLayout();
		$activeTab = $this->input->get('tab', '', 'default');

		// Build the namespace
		$namespace = 'settings/' . $layout . '/default';

		if ($layout === 'default') {
			return $this->app->redirect('index.php?option=com_easyblog&view=settings&layout=general');
		}

		if ($layout === 'rebuildSearch') {
			return $this->$layout();
		}

		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_' . strtoupper($layout));

		JToolBarHelper::apply('settings.save');

		$tabs = $this->getTabs($layout);

		// Something is wrong with the layout. Just redirect to the general settings
		if (!$tabs) {
			return $this->app->redirect('index.php?option=com_easyblog&view=settings&layout=general');
		}

		$goto = $this->input->get('goto', '', 'cmd');

		$this->set('goto', $goto);
		$this->set('tabs', $tabs);
		$this->set('activeTab', $activeTab);
		$this->set('config', $this->config);
		$this->set('layout', $layout);
		$this->set('namespace', $namespace);

		parent::display('settings/form');
	}

	/**
	 * Perform additional settings changes
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function comments(EasyBlogThemes $theme)
	{
		$jcInstalled = false;
		if (file_exists(JPATH_ROOT . '/administrator/components/com_jomcomment/config.jomcomment.php')) {
			$jcInstalled = true;
		}

		//check if jcomments installed.
		$jComment 		= false;
		$jCommentFile 	= JPATH_ROOT . '/components/com_jcomments/jcomments.php';

		if (JFile::exists($jCommentFile)) {
			$jComment = true;
		}

		//check if rscomments installed.
		$rsComment 		= false;
		$rsCommentFile 	= JPATH_ROOT . '/components/com_rscomments/rscomments.php';

		if (JFile::exists($rsCommentFile)) {
			$rsComment = true;
		}

		// @task: Check if easydiscuss plugin is installed and enabled.
		$easydiscuss = JPluginHelper::isEnabled('content', 'easydiscuss');
		$komento = JPluginHelper::isEnabled('content', 'komento');

		// Legacy option fixes
		if ($this->config->get('comment_recaptcha')) {
			$this->config->set('comment_captcha_type', 'recaptcha');
		}

		if ($this->config->get('comment_captcha')) {
			$this->config->set('comment_captcha_type', 'builtin');
		}

		$theme->set('easydiscuss', $easydiscuss);
		$theme->set('komento', $komento);
		$theme->set('jcInstalled', $jcInstalled);
		$theme->set('jComment', $jComment);
		$theme->set('rsComment', $rsComment);
	}

	/**
	 * Renders the global views settings. Settings listed here will be inherited by the views
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function views(EasyBlogThemes $theme)
	{
		// Get the category params
		$params = $this->config;

		$model = EB::model('Settings');

		$fieldsets = new stdClass();

		$fieldsets->frontpage = $model->getViewFieldsets('latest');
		$fieldsets->entry = $model->getViewFieldsets('entry');
		$fieldsets->categories = $model->getViewFieldsets('categories');
		$fieldsets->category = $model->getViewFieldsets('categories', 'listings');
		$fieldsets->tag = $model->getViewFieldsets('tags', 'tag');
		$fieldsets->tags = $model->getViewFieldsets('tags');
		$fieldsets->author = $model->getViewFieldsets('blogger', 'listings');
		$fieldsets->authors = $model->getViewFieldsets('blogger');

		$theme->set('fieldsets', $fieldsets);
	}

	/**
	 * Retrieves a list of tabs on a settings page
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTabs($layout)
	{

		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/themes/default/settings/' . $layout;

		$files = JFolder::files($path, '.php');
		$tabs = [];

		// Get the current active tab
		$active = $this->input->get('tab', '', 'cmd');

		if (!$files) {
			return false;
		}

		foreach ($files as $file) {

			// If a user upgrades from 5.0 or any prior versions, we shouldn't get the default.php
			if ($file == 'default.php') {
				continue;
			}

			$fileName = $file;
			$file = str_ireplace('.php', '', $file);

			$tab = new stdClass();
			$tab->id = str_ireplace(array(' ', '.', '#', '_'), '-', strtolower($file));

			// Ensure this file name 'adsense' have to change to other name is because those adsblock extension searching for this common key #1694
			if ($file == 'adsense') {
				$tab->id = 'adsbygoogle';
			}

			$tab->title = JText::_('COM_EASYBLOG_SETTINGS_' . strtoupper($layout) . '_SUBTAB_' . strtoupper($file));
			$tab->file = $path . '/' . $fileName;
			$tab->active = ($file == 'general' && !$active) || $active === $tab->id;

			// Get the contents of the tab now
			$theme = EB::themes();

			// Comments settings
			if (method_exists($this, $layout)) {
				$this->$layout($theme);
			}

			$tab->contents = $theme->output('admin/settings/' . strtolower($layout) . '/' . $file);

			$tabs[$tab->id] = $tab;
		}

		// Sort items manually. Always place "General" as the first item
		if (isset($tabs['general'])) {

			$general = $tabs['general'];

			unset($tabs['general']);

			array_unshift($tabs, $general);
		} else {
			// First tab should always be highlighted
			$firstIndex = array_keys($tabs);
			$firstIndex = $firstIndex[0];

			if ($active) {
				$tabs[$firstIndex]->active = $active === $tabs[$firstIndex]->id;
			} else {
				$tabs[$firstIndex]->active = true;
			}
		}

		return $tabs;
	}

	/**
	 * Rebuilds the search database
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function rebuildSearch()
	{
		$this->setHeading('COM_EB_REBUILD_SEARCH');

		$file = EBLOG_DEFAULTS . '/menus.json';
		$contents = file_get_contents($file);

		$menus = json_decode($contents);

		$items = array();

		foreach ($menus as $menu) {
			if (!isset($menu->view) || $menu->view != 'settings') {
				continue;
			}

			foreach ($menu->childs as $child) {
				$items[] = $child->url->layout;
			}
		}

		$this->set('items', $items);

		return parent::display('settings/search.rebuild');
	}
}
