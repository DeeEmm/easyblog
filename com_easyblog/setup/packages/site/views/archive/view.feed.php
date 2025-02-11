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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewArchive extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Checks if rss is enabled
		if (!$this->config->get('main_rss')) {
			return;
		}

		// Set the link for this feed
		$this->doc->link = EBR::_('index.php?option=com_easyblog&view=archive');
		$this->doc->setTitle(JText::_('COM_EASYBLOG_ARCHIVED_POSTS'));
		$this->doc->setDescription(JText::_('COM_EASYBLOG_ARCHIVED_POSTS_DESC'));

		$posts = $this->getPosts();

		if (!$posts) {
			return;
		}

		$this->doc->items = EB::formatter('feeds', $posts);
	}

	/**
	 * Retrieves a list of archived posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		// Get the archives model
		$model = EB::model('Archive');
		$posts = $model->getPosts();

		return $posts;
	}
}
