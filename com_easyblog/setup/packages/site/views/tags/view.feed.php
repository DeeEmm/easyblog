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

class EasyBlogViewTags extends EasyBlogView
{
	/**
	 * Default feed display method
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Ensure that rss is enabled
		if (!$this->config->get('main_rss')) {
			throw EB::exception(JText::_('COM_EASYBLOG_FEEDS_DISABLED'), 404);
		}

		$id = $this->input->get('id', '', 'int');
		$tag = EB::table('Tag');
		$tag->load($id);

		// Set document attributes
		$this->doc->link = EBR::_('index.php?option=com_easyblog&view=tags&id=' . $tag->id . '&layout=tag');
		$this->doc->setTitle($this->escape($tag->title));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_TAGS_DESC', $this->escape($tag->title)));

		$posts = $this->getPosts($tag);

		if (!$posts) {
			return;
		}

		$this->doc->items = EB::formatter('feeds', $posts);
	}

	/**
	 * Retrieves posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts($tag)
	{
		$sort = 'latest';
		$model = EB::model('Blog');

		$posts = $model->getTaggedBlogs($tag->id);

		return $posts;
	}
}
