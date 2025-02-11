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

class EasyBlogViewMyblog extends EasyBlogView
{
	/**
	 * This method would be invoked by the parent to bind any active menu params available to the view and themes library
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function defineParams()
	{
		$params = $this->getActiveMenuParams('');

		return $params;
	}

	/**
	 * Default display method to list current logged in user's posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Require user to be logged in to access this page
		EB::requireLogin();

		// Get the default sorting behavior
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');

		// Load up the author profile
		$author = EB::user($this->my->id);

		// Set the breadcrumbs
		$this->setPathway(JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB'), EB::_('index.php?option=com_easyblog&view=blogger'));
		$this->setPathway($author->getName());

		$menu = JFactory::getApplication()->getMenu();
		$categoryInclusion = array();

		if ($menu) {
			$active = $menu->getActive();
			$categoryInclusion = $active->getParams()->get('inclusion');

			// If user configure to show ALL categories, so we skip it then it will display all categories post.
			if (isset($categoryInclusion) && $categoryInclusion[0] == 'all') {
				$categoryInclusion = array_slice($categoryInclusion,1);
			}
		}

		$model = EB::model('Blog');
		$posts = $model->getBlogsBy('blogger', $author->id, $sort, 0, EBLOG_FILTER_PUBLISHED, false, false, array(), false, false, true, array(), $categoryInclusion);

		// Get the pagination
		$pagination	= $model->getPagination();

		// Set the page title
		$title = $author->getName() . ' - ' . EB::getPageTitle(JText::_('COM_EASYBLOG_MY_BLOG_PAGE_TITLE'));
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Set meta tags for blogger
		EB::setMeta($this->my->id, META_ID_BLOGGERS, '', $pagination);

		// Format the posts
		$posts = EB::formatter('list', $posts);

		// Add the RSS headers on the page
		EB::feeds()->addHeaders('index.php?option=com_easyblog&view=myblog');

		// Determines if the viewer already subscribed to the author
		$subscribed = false;

		$bloggerModel = EB::model('Blogger');

		if ($bloggerModel->isBloggerSubscribedUser($author->id, $this->my->id, $this->my->email)) {
			$subscribed = true;
		}

		// Get the current url
		$return = EBR::_('index.php?option=com_easyblog', false);

		$postStyles = EB::getPostStyles($this->params);

		$this->set('postStyles', $postStyles);
		$this->set('return', $return);
		$this->set('subscribed', $subscribed);
		$this->set('author', $author);
		$this->set('posts', $posts);
		$this->set('sort', $sort);
		$this->set('pagination', $pagination);

		parent::display('myblog/default/default');
	}
}