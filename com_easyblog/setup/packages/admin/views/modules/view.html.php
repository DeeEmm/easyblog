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

class EasyBlogViewModules extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('easyblog.manage.modules');
		$this->setHeading('COM_EB_HEADING_MODULES_PACKAGE_MANAGER');

		// Get the server keys
		$key = $this->config->get('general.key');

		$model = EB::model('Modules');
		$initialized = $model->initialized();

		$layout = $this->getLayout();

		if (!$initialized || $layout == 'discover') {
			return $this->discover();
		}

		JToolbarHelper::custom('modules.install', 'upload' , '' , JText::_('Install / Update'));
		JToolbarHelper::custom('modules.uninstall', 'remove', '', JText::_('COM_EB_TOOLBAR_BUTTON_UNINSTALL'));
		JToolbarHelper::custom('modules.discover', 'refresh', '', JText::_('COM_EB_TOOLBAR_BUTTON_FIND_UPDATES'), false);

		$published = $this->app->getUserStateFromRequest('com_easyblog.modules.published', 'published', '*', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.modules.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));

		// Get the list of languages now
		$modules = $model->getModules();
		$pagination	= $model->getPagination();

		$this->set('published', $published);
		$this->set('search', $search);
		$this->set('modules', $modules);
		$this->set('pagination', $pagination);

		return parent::display('modules/default/default');
	}

	/**
	 * Discover modules from stackideas repository
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function discover()
	{
		$this->setHeading('COM_EB_HEADING_MODULES_PACKAGE_MANAGER');

		return parent::display('modules/initialize/default');
	}
}
