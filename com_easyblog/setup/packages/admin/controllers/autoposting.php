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

class EasyBlogControllerAutoposting extends EasyBlogController
{
	/**
	 * Purge the logs for auto posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function purge()
	{
		$this->checkAccess('autoposting');

		// Purge the history
		$model = EB::model('OAuth');
		$state = $model->purgeLogs();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_AUTOPOSTING_LOGS_PURGED', 'autoposting', array(
			'link' => 'index.php?option=com_easyblog&view=autoposting&layout=logs'
		));

		$this->info->set('COM_EASYBLOG_AUTOPOSTING_LOGS_PURGED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=autoposting&layout=logs');
	}
}
