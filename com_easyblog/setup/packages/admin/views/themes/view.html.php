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

require_once(JPATH_COMPONENT . '/views.php');

class EasyBlogViewThemes extends EasyBlogAdminView
{
	/**
	 * Displays the theme listings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.theme');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Set heading text
		$this->setHeading('COM_EASYBLOG_TITLE_THEMES', '', 'fa-flask');

		JToolBarHelper::custom('themes.setDefault', 'star', '', JText::_('COM_EASYBLOG_SET_DEFAULT'), false);

		// Get themes
		$model = EB::model('Themes');
		$themes = $model->getThemes();

		$this->set('default', $this->config->get('theme_site'));
		$this->set('themes', $themes);
		$this->set('search', '');

		parent::display('themes/default');
	}

	/**
	 * Render theme settings
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function settings()
	{
		$id = $this->input->get('id', '', 'word');

		// Do not allow to view this page if there is no element provided
		if (!$id) {
			$this->info->set('COM_EASYBLOG_THEMES_PLEASE_SELECT_THEME_TO_BE_EDITED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=themes');
		}

		$model = EB::model('Themes');
		$themeObj = $model->getThemeObject($id);

		$this->setHeading('COM_EB_THEME_' . trim(strtoupper($themeObj->name)));


		JToolBarHelper::apply('themes.saveSettings');
		JToolBarHelper::cancel();

		$params = $model->getThemeParams($themeObj->element);

		$this->set('params', $params);
		$this->set('themeObj', $themeObj);

		parent::display('themes/settings/default');
	}

	/**
	 * Render editor for themes
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function editor()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_THEMES_EDITOR', '', 'fa-edit');
		$this->hideSidebar();

		$element = $this->input->get('element', '', 'word');

		// Do not allow to view this page if there is no element provided
		if (!$element) {
			$this->info->set('COM_EASYBLOG_THEMES_PLEASE_SELECT_THEME_TO_BE_EDITED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=themes');
		}

		$model = EB::model('Themes');
		$table = EB::table('ThemeOverrides');
		$id = $this->input->get('id', '', 'default');
		$item = null;

		if ($id) {
			$item = $model->getFile($id, $element, true);

			JToolBarHelper::apply('themes.saveFile');

			if ($item->modified) {
				JToolBarHelper::trash('revert', JText::_('COM_EASYBLOG_REVERT_CHANGES'), false);

				$table->load(array('file_id' => $item->override));
			}
		}

		JToolBarHelper::cancel();

		// Get a list of theme files from this template file
		$files = $model->getFiles($element);

		// Always use codemirror
		$editor = EBFactory::getEditor('codemirror');

		$this->set('item', $item);
		$this->set('table', $table);
		$this->set('element', $element);
		$this->set('id', $id);
		$this->set('files', $files);
		$this->set('editor', $editor);

		parent::display('themes/editor/default');
	}

	/**
	 * Allows site admin to insert custom css codes
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function custom()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_THEMES_CUSTOM_CSS', '', 'fa-edit');
		$this->hideSidebar();

		// Always use codemirror
		$editor = EBFactory::getEditor('codemirror');

		$model = EB::model('Themes');
		$template = $model->getCurrentTemplate();

		JToolBarHelper::apply('themes.saveCustomCss');
		JToolBarHelper::cancel();

		// Get the custom.css override path for the current Joomla template
		$path = $model->getCustomCssTemplatePath();
		$contents = '';

		if (JFile::exists($path)) {
			$contents = file_get_contents($path);
		}

		$this->set('contents', $contents);
		$this->set('editor', $editor);

		parent::display('themes/custom/default');
	}

	/**
	 * Renders the theme installer form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function install($tpl = null)
	{
		// Set heading text
		$this->setHeading('COM_EASYBLOG_THEMES_INSTALL', '', 'fa-flask');

		JToolBarHelper::custom('themes.upload', 'save', '', JText::_('COM_EASYBLOG_UPLOAD_AND_INSTALL_BUTTON'), false);

		parent::display('themes/install');
	}
}
