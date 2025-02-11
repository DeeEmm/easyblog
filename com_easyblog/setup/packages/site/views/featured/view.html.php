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

class EasyBlogViewFeatured extends EasyBlogView
{
	/**
	 * This method would be invoked by the parent to set any params
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function defineParams()
	{
		// Get the current active menu's properties.
		$params = $this->getActiveMenuParams('');

		return $params;
	}

	/**
	 * Default display method for featured listings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Add the RSS headers on the page
		EB::feeds()->addHeaders('index.php?option=com_easyblog&view=featured');

		// Add breadcrumbs on the site menu.
		$this->setPathway('COM_EASYBLOG_FEATURED_BREADCRUMB');

		$limit = EB::getViewLimit('limit', 'featured');
		$model = EB::model('Featured');

		// Get a list of featured posts
		$posts = $model->getPosts([
			'limit' => $limit
		]);

		$pagination	= $model->getPagination();

		// Set the meta tags for this page
		EB::setMeta(META_ID_FEATURED, META_TYPE_VIEW, '', $pagination);

		$posts = EB::formatter('list', $posts);

		// Set the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_FEATURED_PAGE_TITLE'));
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Get the current url
		$return = EBR::_('index.php?option=com_easyblog&view=featured', false);

		$postStyles = EB::getPostStyles($this->params);

		$this->set('postStyles', $postStyles);
		$this->set('return', $return);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('featured/default/default');
	}
}
