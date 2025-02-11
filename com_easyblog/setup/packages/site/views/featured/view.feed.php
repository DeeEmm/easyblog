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

class EasyBlogViewFeatured extends EasyBlogView
{
	public function display($tmpl = null)
	{
		if (!$this->config->get('main_rss')) {
			return;
		}

		$this->doc->link = EBR::_('index.php?option=com_easyblog&view=featured');
		$this->doc->setTitle(JText::_('COM_EASYBLOG_FEEDS_FEATURED_TITLE'));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_FEATURED_DESC' , JURI::root()));

		$posts = $this->getPosts();

		if (!$posts) {
			return;
		}

		$this->doc->items = EB::formatter('feeds', $posts);
	}

	/**
	 * Retrieves a list of featured posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		$model = EB::model('Blog');
		$posts = $model->getFeaturedBlog();

		return $posts;
	}
}