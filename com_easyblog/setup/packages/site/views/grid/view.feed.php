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

class EasyBlogViewGrid extends EasyBlogView
{
	public function display($tpl = null)
	{
		// Ensure that rss is enabled
		if (!$this->config->get('main_rss')) {
			return;
		}

		// Set the document properties
		$this->doc->link = EB::_('index.php?option=com_easyblog&view=grid');
		$this->doc->setTitle(JText::_('COM_EASYBLOG_FEEDS_LATEST_TITLE'));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_LATEST_DESC', JURI::root()));

		$posts = $this->getPosts();

		if (!$posts) {
			return;
		}

		$this->doc->items = EB::formatter('feeds', $posts);

		return;
	}

	/**
	 * Retrieves grid view posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		$model = EB::model('Blog');

		// Get sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');

		// Get the current active menu's properties.
		$params = $this->theme->params;
		$inclusion	= '';

		// Determine if we should explicitly include authors.
		$includeAuthors = array();

		if ($params->get('grid_inclusion_authors', false)) {
			$includeAuthors = $params->get('grid_inclusion_authors');
		}

		// Determine if we should explicitly exclude authors.
		$excludeAuthors = array();

		if ($params->get('grid_exclusion_authors', false)) {
			$excludeAuthors = $params->get('grid_exclusion_authors');
		}

		// Determine if we should exclude featured post from the list.
		$excludeFeatured = $params->get('grid_exclude_featured', false);

		// Retrieve the post categories
		$postCategories = $params->get('grid_post_category', array());

		// Format postCategories if user selected all category.
		if ($postCategories) {
			$postCategories = array_diff($postCategories, array('all'));
		}

		// Fetch all blog entries based on the defined information above.
		$posts = $model->getBlogsby('', '', '', 0, EBLOG_FILTER_PUBLISHED, false, false, $excludeBlogs, false, false, true, '', $postCategories, null, 'listlength', 
			false, $includeAuthors, $excludeAuthors, $excludeFeatured);

		return $posts;
	}
}
