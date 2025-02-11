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

class EasyBlogViewCategories extends EasyBlogView
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

		$id = $this->input->get('id', '', 'cmd');
		
		$category = EB::table('Category');
		$category->load($id);

		// Set document attributes
		$this->doc->link = EBR::_('index.php?option=com_easyblog&view=categories&id=' . $id . '&layout=listings');
		$this->doc->setTitle($this->escape($category->getTitle()));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_RSS_FEEDS_CATEGORY_DESC', $this->escape($category->getTitle())));

		$posts = $this->getPosts($category);

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
	public function getPosts($category)
	{

		// Private category shouldn't allow to access.
		$privacy = $category->checkPrivacy();

		if (!$privacy->allowed) {
			throw EB::exception(JText::_('COM_EASYBLOG_NOT_ALLOWED_HERE'), 500);
		}

		// Get the nested categories
		$category->childs = null;

		EB::buildNestedCategories($category->id, $category);

		$linkage = '';
		EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

		$catIds = array();
		$catIds[] = $category->id;
		EB::accessNestedCategoriesId($category, $catIds);

		$category->nestedLink    = $linkage;

		$model = EB::model('Blog');
		$sort = $this->input->get('sort', $this->config->get( 'layout_postorder' ), 'cmd');

		$posts = $model->getBlogsBy('category', $catIds, $sort);

		return $posts;
	}

	/**
	 * Proxy to the default layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function listings()
	{
		return $this->display();
	}

}
