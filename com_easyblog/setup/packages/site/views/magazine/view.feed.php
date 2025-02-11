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

class EasyBlogViewMagazine extends EasyBlogView
{
	public function display($tpl = null)
	{
		// Ensure that rss is enabled
		if (!$this->config->get('main_rss')) {
			return;
		}

		// Set the document properties
		$this->doc->link = EB::_('index.php?option=com_easyblog&view=magazine');
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
	 * Retrieves magazine posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		$model = EB::model('Blog');

		// Get the current active menu's properties.
		$params = $this->theme->params;

		// Determine if we should explicitly include authors.
		$includeAuthors = array();

		if ($params->get('magazine_inclusion_authors', false)) {
		    $includeAuthors = $params->get('magazine_inclusion_authors');
		}

		// Determine if we should explicitly exclude authors.
		$excludeAuthors = array();

		if ($params->get('magazine_exclusion_authors', false)) {
		    $excludeAuthors = $params->get('magazine_exclusion_authors');
		}

		// Determine if we should exclude featured post from list.
		$excludeFeatured = $params->get('magazine_exclude_featured', false);

		// Determine the list limit for the list article
		$listLimit = $params->get('listLimit', '6');

		// Retrieve the list article categories.
		$listArticleCategories = $params->get('magazine_list_article_category', array());
		
		// Fetch all blog entries based on the defined information above.
		$posts = $model->getBlogsby('', '', '', $listLimit, EBLOG_FILTER_PUBLISHED, false, false, array(), false, false, true, '', $listArticleCategories, null, 'listlength', 
			false, $includeAuthors, $excludeAuthors, $excludeFeatured);

		return $posts;
	}
}
