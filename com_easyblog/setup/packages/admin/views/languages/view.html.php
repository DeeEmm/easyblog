<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewLanguages extends EasyBlogAdminView
{
	/**
	 * Default user listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		$this->checkAccess('easyblog.manage.languages');

		$this->setHeading('COM_EASYBLOG_HEADING_LANGUAGES', '', 'fa-language');

		// Get configuration
		$config = EB::config();

		// Get the api key from the config
		$key = $config->get('main_apikey');

		if (!$key) {

			JToolbarHelper::custom('savekey', 'save' , '' , JText::_('COM_EASYBLOG_SAVE_APIKEY_BUTTON') , false);
			$return = base64_encode('index.php?option=com_easyblog&view=languages');

			$this->set('return', $return);

			return parent::display('languages/key');
		}

		JToolbarHelper::custom('languages.discover' , 'refresh' , '' , JText::_('COM_EASYBLOG_TOOLBAR_BUTTON_DISCOVER') , false);
		JToolbarHelper::custom('languages.purge' , 'purge' , '' , JText::_('COM_EASYBLOG_TOOLBAR_BUTTON_PURGE_CACHE'), false);
		JToolbarHelper::custom('languages.install', 'upload' , '' , JText::_('COM_EASYBLOG_TOOLBAR_BUTTON_INSTALL_OR_UPDATE'));
		JToolbarHelper::custom('languages.uninstall', 'remove' , '' , JText::_('COM_EASYBLOG_THEMES_UNINSTALL'));

		// Get the languages that are already stored on the db
		$model = EB::model('Languages');
		$initialized = $model->initialized();

		if (!$initialized) {
			$this->set('key', $key);

			return parent::display('languages/initialize');
		}

		// Get languages
		$languages 	= $model->getLanguages();

		foreach ($languages as &$language) {

			$translators = json_decode($language->translator);

			$language->translator = $translators;
		}


		$this->set("languages", $languages);

		parent::display('languages/default');
	}
}
