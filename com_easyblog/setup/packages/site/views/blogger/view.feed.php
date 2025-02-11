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

class EasyBlogViewBlogger extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Check if rss is enabled
		if (!$this->config->get('main_rss')) {
			throw EB::exception(JText::_('COM_EASYBLOG_FEEDS_DISABLED'), 404);
		}

		// Check if the author's id is provided
		$id = $this->input->get('id', '', 'cmd');

		if (!$id) {
			throw EB::exception(JText::_('COM_EASYBLOG_FEEDS_INVALID_AUTHOR_ID'), 404);
		}

		$author = EB::user($id);

		$this->doc->link = $author->getPermalink();
		$this->doc->setTitle(JText::sprintf('COM_EASYBLOG_FEEDS_BLOGGER_TITLE' , $author->getName()));
		$this->doc->setDescription(strip_tags($author->description));

		$posts = $this->getPosts($author);

		if (!$posts) {
			return;
		}

		$this->doc->items = EB::formatter('feeds', $posts);
	}

	/**
	 * Retrieves a list of posts by author
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts($author)
	{
		$model = EB::model('Blog');
		$posts = $model->getBlogsBy('blogger', $author->id);
		$posts = EB::formatter('list', $posts);

		return $posts;
	}
}
