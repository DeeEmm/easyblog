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

require_once(JPATH_COMPONENT . '/controller.php');

jimport('joomla.installer.helper');
jimport('joomla.installer.installer');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class EasyBlogControllerSystem extends EasyBlogController
{
	/**
	 * Process EasyBlog upgrades
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function upgrade()
	{
		// Check for the access that user is allowed to upgrade or not
		if(!JFactory::getUser()->authorise('core.admin', 'com_easyblog')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$model = EB::model('System');
		$state = $model->update();

		if ($state === false) {
			$this->info->set($model->getError(), 'error');
			return $this->app->redirect('index.php?option=com_easyblog');
		}

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_EASYBLOG_UPDATE', 'system');

		$this->info->set('EasyBlog updated to the latest version successfully', 'success');
		return $this->app->redirect('index.php?option=com_easyblog');
	}

	/**
	 * Dismiss toolbar notice
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function dismissToolbarNotice()
	{
		$data = ['toolbar_installation_failed' => "0"];
		$model = EB::model('Settings');
		$model->save($data);
	}
}
