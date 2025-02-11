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

class EasyBlogControllerReactions extends EasyBlogController
{
	/**
	 * Deletes a list of reactions from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('reactions');

		// Get the list of metas to be deleted
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			return $this->app->redirect('index.php?option=com_easyblog&view=reactions');
		}

		// Do whatever you need to do here
		foreach ($ids as $id) {
			$id = (int) $id;

			$history = EB::table('ReactionHistory');
			$history->load($id);
			$history->delete();
		}

		$this->info->set('COM_EASYBLOG_REACTIONS_DELETED', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=reactions');
	}
}
