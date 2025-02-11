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

class EasyBlogViewSearch extends EasyBlogView
{
	/**
	 * Default search view for EasyBlog
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Set the meta tags for search
		EB::setMeta(META_ID_SEARCH, META_TYPE_VIEW);

		// Set the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_SEARCH_PAGE_TITLE'));
		$this->setPageTitle($title);

		// Set the view's breadcrumbs
		$this->setViewBreadcrumb('search');

		// Get any existing query
		$query = $this->input->get('query', '', 'string');

		$badchars = array('#', '>', '<', '\\', '=', '(', ')', '*', ',', '.', '%', '\'');
		$query = trim(str_replace($badchars, '', $query));

		$Itemid	= $this->input->get('Itemid', '', 'int');
		$catId = $this->input->get('category_id', '', 'int');
		$tagIds = $this->input->get('tags', array(), 'array');

		$sort = $this->input->get('sort', '', 'string');
		$ordering = $this->input->get('ordering', '', 'string');

		$query = trim($query);

		$posts = array();
		$pagination = '';

		if (!empty($query)) {

			// Get the model
			$model = EB::model('Search');
			$model->setArchiveSearch($this->config->get('main_archiving_search', 0));
			if ($tagIds) {
				$model->setTags($tagIds);
			}

			if ($sort) {
				$model->setSorting($sort);
				$model->setOrdering($ordering);
			}

			$result = $model->getData();
			$total = count($result);

			if ($total > 0) {

				$searchworda = preg_replace('#\xE3\x80\x80#s', ' ', $query);
				$searchwords = preg_split("/\s+/u", $searchworda);
				$searchwords = array_unique($searchwords);

				// Format the post
				$posts = EB::formatter('list', $result);

				// Remove all unecessary codes from the output
				foreach ($posts as &$row) {

					// Strip videos
					$row->intro = EB::videos()->strip($row->intro);
					$row->content = EB::videos()->strip($row->content);

					// strip gallery
					$row->intro = EB::gallery()->strip($row->intro);
					$row->content = EB::gallery()->strip($row->content);

					// strip jomsocial album
					$row->intro = EB::album()->strip($row->intro);
					$row->content = EB::album()->strip($row->content);

					// strip audio
					$row->intro = EB::audio()->strip($row->intro);
					$row->content = EB::audio()->strip($row->content);

					// Strip <script> tag from the content
					$row->intro = EB::truncater()->strip_only($row->intro, '<script>', true);
					$row->content = EB::truncater()->strip_only($row->content, '<script>', true);

					// Format the content so that we can apply our search highlighting
					$content = preg_replace('/\s+/', ' ', strip_tags($row->content));

					if (empty($content)) {
						$content = preg_replace('/\s+/', ' ', strip_tags($row->intro));
					}

					// We only want a snippet of the content
					$content = EBString::substr(strip_tags($content), 0, 350);
					$pattern = '#(';

					$x 	= 0;

					foreach ($searchwords as $key => $value) {
						$pattern .= $x == 0 ? '' : '|';
						$pattern .= preg_quote($value, '#' );
						$x++;
					}

					$pattern .= ')#iu';

					$row->title = preg_replace($pattern, '<span class="search-highlight">\0</span>', $row->title);
					$row->content = preg_replace($pattern, '<span class="search-highlight">\0</span>', $content);
				}

				$pagination	= $model->getPagination();
			}

		}

		$categoryFilter = $this->input->get('category_id', 0, 'int');
		
		// fix uikit
		$ebtheme = $this->config->get('layout_theme');
		$selectClassName = $ebtheme == 'uikit' ? 'uk-select' : '';

		$categoryDropdown = EB::populateCategories('', '', 'select', 'xx_category_id', $categoryFilter, false, true, true, array(), '', 'COM_EASYBLOG_FILTER_SELECT_CATEGORY', $selectClassName);

		// get parents categories
		$categories = EB::getCategoriesForFilters('0');

		$activeCategoryId = $this->input->get('category_id', 0, 'int');

		$activeCategory = EB::table('Category');
		$activeCategory->load($activeCategoryId);


		// get tags. get max 50 most popular tags
		$tagModel = EB::model('Tags');
		$tags = $tagModel->getTagCloud('50', 'postcount', 'desc');


		$this->set('query', $query);
		$this->set('posts', $posts);
		$this->set('categories', $categories);
		$this->set('tags', $tags);
		$this->set('activeTagIds', $tagIds);
		$this->set('sort', $sort);
		$this->set('ordering', $ordering);
		$this->set('activeCategory', $activeCategory);
		$this->set('activeCategoryId', $activeCategoryId);
		$this->set('categoryDropdown', $categoryDropdown);
		$this->set('pagination', $pagination);
		$this->set('Itemid', $Itemid);

		parent::display('search/default');
	}

	public function parseQuery()
	{
		$query = $this->input->get('query', '', 'default');
		$query = rtrim($query, '.');

		$this->app->redirect(EBR::_('index.php?option=com_easyblog&view=search&query=' . $query, false));
		$this->app->close();
	}
}
