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

class EasyBlogViewMyBlog extends EasyBlogView
{
	public function display($tpl = null)
	{
		// Ensure that rss is enabled
		if (!$this->config->get('main_rss')) {
			return;
		}

		// Since this view only list current logged in user's post
		if (!$this->my->id) {
			return;
		}

		// Set document attributes
		$this->doc->link = EB::_('index.php?option=com_easyblog&view=myblog');
		$this->doc->setTitle(JText::_('COM_EASYBLOG_FEEDS_MYBLOG_TITLE'));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_MYBLOG_DESC', $this->my->name));

		// If there's no data, skip this altogether
		$posts = $this->getPosts();

		if (!$posts) {
			return;
		}

		$this->doc->items = EB::formatter('feeds', $posts);
	}

	/**
	 * Retrieves a list of posts from the current logged in user
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		// Get the default sorting behavior
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');

		// Load up the author profile
		$author = EB::user($this->my->id);

		// Get the blogs model
		$model = EB::model('Blog');
		$posts = $model->getBlogsBy('blogger', $author->id, $sort);

		return $posts;
	}
}
