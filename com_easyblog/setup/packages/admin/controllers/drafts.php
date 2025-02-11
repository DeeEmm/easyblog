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

class EasyBlogControllerDrafts extends EasyBlogController
{
	/**
	 * Deletes a draft from the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl access
		$this->checkAccess('blog');

		// Get list of blog post id's.
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOG_ID', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$draft = EB::table('Revision');
			$draft->load($id);

			$draft->delete();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_DELETED_SUCCESSFULLY', 'success');

		$return = 'index.php?option=com_easyblog&view=blogs&layout=drafts';
		return $this->app->redirect($return);
	}

}
