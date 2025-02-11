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

class EasyBlogViewBlogs extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Check for access
		$this->checkAccess('easyblog.manage.blog');

		// Set page details
		$this->setHeading('COM_EB_HEADER_POSTS');

		$search = $this->app->getUserStateFromRequest('com_easyblog.blogs.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_state', 'filter_state', '*', 'word');
		$filter_category = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_category', 'filter_category', '*', 'int');
		$filterLanguage = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_language', 'filter_language', '', '');
		$filter_posttype = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_posttype', 'filter_posttype', '*', 'word');

		// Ordering
		$order = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');


		$source = $this->input->get('filter_source', '-1', 'default');
		$filteredBlogger = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_blogger', 'filter_blogger' , '' , 'int');
		$filterSortBy = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_sort_by', 'filter_sort_by', '', 'word');

		$startDate = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_after_date', 'filter_start_date', '', '');
		$endDate = $this->app->getUserStateFromRequest('com_easyblog.blogs.filter_before_date', 'filter_end_date', '', '');

		if ($filter_state != 'T') {
			JToolBarHelper::addNew('blogs.create');
			JToolbarHelper::publishList('blogs.publish');
			JToolbarHelper::unpublishList('blogs.unpublish');
			JToolBarHelper::custom('blogs.feature', 'star' , '' , JText::_('COM_EASYBLOG_FEATURE_TOOLBAR'));
			JToolBarHelper::custom('blogs.unfeature', 'star-empty' , '' , JText::_('COM_EASYBLOG_UNFEATURE_TOOLBAR'));
			JToolbarHelper::trash('blogs.trash');
		} else {
			JToolbarHelper::publishList('blogs.restore', JText::_('COM_EASYBLOG_RESTORE'));
			JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_CONFIRM_DELETE'), 'blogs.remove');
			JToolBarHelper::custom('blogs.empty', 'trash', '', JText::_('COM_EASYBLOG_EMPTY_TRASH'), false);
		}

		//Get data from the model
		$model = EB::model('Blogs');
		$rows = $model->getData();

		$pagination = $model->getDataPagination();

		$limit = $model->getState('limit');

		// Determines if the viewer is rendering this in a dialog
		$browse = $this->input->get('browse', 0, 'int');
		$browsefunction = $this->input->get('browsefunction', 'insertBlog', 'cmd');

		// Get autoposting sites
		$consumers = array();
		$sites = array('twitter', 'facebook', 'linkedin');
		$centralizedConfigured = false;

		foreach ($sites as $site) {

			$consumer = EB::table('OAuth');
			$consumer->load(array('system' => 1, 'type' => $site));

			if (!empty($consumer->id) && $consumer->access_token) {
				$centralizedConfigured  = true;

				$consumers[] = $consumer;
			}
		}

		$blogs 	= array();

		$blogModel = EB::model('Blog');

		$languages = JLanguageHelper::getLanguages('lang_code');

		// Assign the category object into the list of blogs
		foreach ($rows as $row) {

			$post = EB::post($row->id);

			// Get the primary category
			$post->category = $post->getPrimaryCategory();

			// Process the contribution item
			$post->contributionDisplay = JText::_('COM_EASYBLOG_BLOGS_WIDE');

			$contribution = $post->getBlogContribution();

			if ($contribution !== false) {
				$post->contributionDisplay = $contribution->getTitle();
			}

			// Determine's post featured status
			$post->featured = $post->isFeatured();

			$post->associations = $post->getAssociation();

			if ($post->associations) {
				foreach ($post->associations as $locale => &$association) {
					$association->language = EB::normalize($languages, $locale, []);
				}
			}

			$blogs[] = $post;
		}

		$statusKey = 'published';

		if ($filter_state == 'T' || $filter_state == 'A') {
			$statusKey = 'state';
		}

		$this->set('limit', $limit);
		$this->set('consumers', $consumers);
		$this->set('centralizedConfigured', $centralizedConfigured);
		$this->set('source', $source);
		$this->set('filterBlogger', $filteredBlogger);
		$this->set('filterLanguage', $filterLanguage);
		$this->set('browse' , $browse);
		$this->set('browseFunction', $browsefunction);
		$this->set('blogs', $blogs);
		$this->set('pagination', $pagination);
		$this->set('filter_state', $filter_state);
		$this->set('filter_category', $filter_category);
		$this->set('filter_posttype', $filter_posttype);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('filterSortBy', $filterSortBy);
		$this->set('startDate', $startDate);
		$this->set('endDate', $endDate);
		$this->set('statusKey', $statusKey);

		parent::display('blogs/default');
	}

	/**
	 * Displays a list of pending blogs on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function pending()
	{
		$this->checkAccess('easyblog.manage.pending');

		$this->setHeading('COM_EASYBLOG_BLOGS_PENDING_BLOGS');

		JToolbarHelper::publishList('blogs.approve', JText::_('COM_EASYBLOG_APPROVE'));
		JToolbarHelper::unpublishList('blogs.reject', JText::_('COM_EASYBLOG_REJECT'));
		JToolbarHelper::deleteList('', 'pending.remove');

		$search = $this->app->getUserStateFromRequest('com_easyblog.pending.search', 'search', '', 'string');
		$search = trim(EBString::strtolower($search));

		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.pending.filter_category', 'filter_category', '*', 'int');
		$order = $this->app->getUserStateFromRequest('com_easyblog.pending.filter_order', 'filter_order', 'a.post_id', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.pending.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

		// Get data from the model
		$model = EB::model('Pending');
		$items = $model->getBlogs(true);
		$pagination = $model->getPagination();
		$limit = $model->getState('limit');

		// Get the filters for category
		$filter = $this->getFilterCategory($categoryFilter);

		$posts = array();

		if ($items) {
			foreach ($items as $item) {
				$post = EB::post($item->id . '.' . $item->revision_id);
				$posts[] = $post;
			}
		}

		$this->set('limit', $limit);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('categoryFilter', $filter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('blogs/pending/default');
	}

	/**
	 * Displays a list of templates
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function templates()
	{
		// Ensure the user has access to manage templates
		$this->checkAccess('easyblog.manage.templates');

		$this->setHeading('COM_EASYBLOG_BLOGS_POST_TEMPLATES_TITLE', '', 'fa-clipboard');

		JToolBarHelper::addNew('blogs.createTemplate');
		JToolbarHelper::publishList('blogs.publishTemplate');
		JToolbarHelper::unpublishList('blogs.unpublishTemplate');
		JToolbarHelper::custom('blogs.copyTemplate', 'copy', '', JText::_('COM_EASYBLOG_DUPLICATE'));
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_CONFIRM_DELETE_POST_TEMPLATES'), 'blogs.deletePostTemplates');
		JToolbarHelper::custom('blogs.exportTemplates', 'export', '', JText::_('Export'), true);

		EB::loadLanguages();

		$search = $this->app->getUserStateFromRequest('com_easyblog.templates.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));
		$order = $this->app->getUserStateFromRequest('com_easyblog.templates.filter_order', 'filter_order', 'ordering', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.templates.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$model = EB::model('Templates');
		$rows = $model->getItems();

		$pagination = $model->getPagination();
		$limit = $model->getState('limit');
		$templates = array();
		$ordering = array();

		foreach ($rows as $row) {
			$template = EB::table('PostTemplate');
			$template->bind($row);

			$ordering[] = $template->id;

			$templates[] = $template;
		}

		// Save ordering
		$saveOrder = $order == 'ordering' && $orderDirection == 'asc';

		$this->set('limit', $limit);
		$this->set('search', $search);
		$this->set('templates', $templates);
		$this->set('pagination', $pagination);
		$this->set('ordering', $ordering);
		$this->set('order', $order);
		$this->set('saveOrder', $saveOrder);
		$this->set('orderDirection', $orderDirection);

		parent::display('blogs/templates/default/default');
	}

	/**
	 * Allows admin to edit the template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function editTemplate()
	{
		// Ensure the user has access to manage templates
		$this->checkAccess('easyblog.manage.templates');

		$this->setHeading('COM_EASYBLOG_BLOGS_POST_TEMPLATES_TITLE', '', 'fa-clipboard');

		JToolBarHelper::apply('templates.applyForm');
		JToolbarHelper::save('templates.saveForm');
		JToolBarHelper::cancel();

		$id = $this->input->get('id', 0, 'int');

		$template = EB::table('PostTemplate');
		$template->load($id);

		$this->set('template', $template);

		parent::display('blogs/templates/edit/default');
	}

	/**
	 * Displays a list of draft post on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function drafts()
	{
		$this->checkAccess('easyblog.manage.blog');

		$this->setHeading('COM_EASYBLOG_BLOGS_DRAFT_BLOGS');
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_BLOGS_DRAFT_DELETE_DRAFTS_CONFIRMATION'), 'drafts.remove');

		$search = $this->app->getUserStateFromRequest('com_easyblog.drafts.search', 'search', '', 'string');
		$search = trim(EBString::strtolower($search));

		$categoryFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_category', 'filter_category', '*', 'int');
		$authorFilter = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_blogger', 'filter_blogger', '*', 'int');
		$order = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_order', 'filter_order', 'a.ordering', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.drafts.filter_order_Dir', 'filter_order_Dir', '', 'word');

		// Get the filters for category
		$filter = $this->getFilterCategory($categoryFilter);

		// Get the filters for category
		$authorfilter = $this->getFilterBlogger($authorFilter);

		// Get data from the model
		$model = EB::model('Revisions');
		$rows = $model->getItems();

		$pagination = $model->getItemsPagination();
		$limit = $model->getState('limit');
		$drafts = array();

		if ($rows) {
			foreach ($rows as &$row) {

				$uid = $row->post_id . '.' . $row->id;
				$post = EB::Post($uid);

				$post->revisionOrdering = $row->ordering;

				$drafts[]	= $post;
			}
		}

		$this->set('search', $search);
		$this->set('drafts', $drafts);
		$this->set('limit', $limit);
		$this->set('pagination', $pagination);
		$this->set('categoryFilter', $filter);
		$this->set('authorFilter', $authorfilter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('blogs/drafts/default');
	}

	public function getLanguageTitle( $code )
	{
		$db 	= EB::db();
		$query	= 'SELECT ' . $db->nameQuote( 'title' ) . ' FROM '
				. $db->nameQuote( '#__languages' ) . ' WHERE '
				. $db->nameQuote( 'lang_code' ) . '=' . $db->Quote( $code );
		$db->setQuery( $query );

		$title 	= $db->loadResult();

		return $title;
	}

	public function getFilterBlogger($filter_type = '*')
	{
		$model = EB::model('Blogger');
		// $authors = $model->getData('alphabet', null, 'showbloggerwithpost');
		$authors = $model->getPostAuthors();

		$filter[] = JHTML::_('select.option', '', JText::_('COM_EB_FILTER_DROPDOWN_AUTHOR'));

		if ($authors) {
			foreach ($authors as $author) {
				$filter[] = JHTML::_('select.option', $author->id, $author->name);
			}
		}

		return JHTML::_('select.genericlist', $filter, 'filter_blogger', 'class="form-control" data-fd-select2="easyblog" data-theme="backend" data-fd-table-filter="easyblog"', 'value', 'text', $filter_type );
	}

	public function getFilterCategory($defaultCat = '*')
	{
		$categoryFilter = EB::populateCategoryFilter('filter_category', $defaultCat, 'class="form-control" data-fd-select2="easyblog" data-theme="backend" data-fd-table-filter="easyblog"', 'COM_EB_FILTER_DROPDOWN_CATEGORY');

		return $categoryFilter;
	}

	public function getFilterPosttype($filter_posttype = '')
	{
		$type[] = JHTML::_('select.option', '', JText::_('COM_EB_FILTER_DROPDOWN_TYPE'));
		$type[] = JHTML::_('select.option', 'text', JText::_('COM_EB_TYPE_TEXT'));
		$type[] = JHTML::_('select.option', 'photo', JText::_('COM_EB_TYPE_PHOTO'));
		$type[] = JHTML::_('select.option', 'video', JText::_('COM_EB_TYPE_VIDEO'));
		$type[] = JHTML::_('select.option', 'quote', JText::_('COM_EB_TYPE_QUOTE'));
		$type[] = JHTML::_('select.option', 'link', JText::_('COM_EB_TYPE_LINK'));
		return JHTML::_('select.genericlist', $type, 'filter_posttype', 'class="form-control" data-eb-filter-posttype data-fd-select2="easyblog" data-theme="backend" data-fd-table-filter="easyblog"', 'value', 'text', $filter_posttype);
	}

	public function getFilterState($filter_state = '*')
	{
		$state[] = JHTML::_('select.option', '', JText::_('COM_EB_FILTER_DROPDOWN_STATE'));
		$state[] = JHTML::_('select.option', 'P', JText::_('COM_EASYBLOG_PUBLISHED'));
		$state[] = JHTML::_('select.option', 'U', JText::_('COM_EASYBLOG_UNPUBLISHED'));
		$state[] = JHTML::_('select.option', 'S', JText::_('COM_EASYBLOG_SCHEDULED'));
		$state[] = JHTML::_('select.option', 'T', JText::_('COM_EASYBLOG_TRASHED'));
		$state[] = JHTML::_('select.option', 'F', JText::_('COM_EASYBLOG_STATE_FEATURED'));
		$state[] = JHTML::_('select.option', 'A', JText::_('COM_EASYBLOG_STATE_ARCHIVED'));
		$state[] = JHTML::_('select.option', 'FP', JText::_('COM_EASYBLOG_BLOGS_FRONTPAGE'));
		$state[] = JHTML::_('select.option', 'date', JText::_('COM_EASYBLOG_STATE_BY_DATE'));
		return JHTML::_('select.genericlist',   $state, 'filter_state', 'class="form-control" data-eb-filter-state  data-fd-select2="easyblog" data-theme="backend" data-fd-table-filter="easyblog"', 'value', 'text', $filter_state );
	}

	public function getFilterSortBy($filter_state = '*')
	{
		$state[] = JHTML::_('select.option', '', JText::_('COM_EASYBLOG_SELECT_SORT_STATE'));
		$state[] = JHTML::_('select.option', 'latest', JText::_('COM_EASYBLOG_SELECT_SORT_NEWEST'));
		$state[] = JHTML::_('select.option', 'oldest', JText::_('COM_EASYBLOG_SELECT_SORT_OLDEST'));
		$state[] = JHTML::_('select.option', 'popular', JText::_('COM_EASYBLOG_SELECT_SORT_MOST_VISITED'));
		$state[] = JHTML::_('select.option', 'highest_rated', JText::_('COM_EASYBLOG_SELECT_SORT_HIGHEST_RATED'));
		$state[] = JHTML::_('select.option', 'most_rated', JText::_('COM_EASYBLOG_SELECT_SORT_MOST_RATED'));

		return JHTML::_('select.genericlist', $state, 'filter_sort_by', 'class="form-control"  data-fd-select2="easyblog" data-theme="backend" data-fd-table-filter="easyblog"', 'value', 'text', $filter_state);
	}

	public function getFilterLanguage($filter_language='*')
	{
		$languages = FH::getJoomlaLanguages();

		$state[] = JHTML::_('select.option', '', JText::_('COM_EB_FILTER_DROPDOWN_LANGUAGE'));

		foreach ($languages as $language) {
			$state[] = JHTML::_('select.option', $language['tag'], $language['name']);
		}

		return JHTML::_('select.genericlist', $state, 'filter_language', 'class="form-control"  data-fd-select2="easyblog" data-theme="backend" data-fd-table-filter="easyblog"', 'value', 'text', $filter_language);
	}
}
