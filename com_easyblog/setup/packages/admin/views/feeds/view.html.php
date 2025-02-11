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

class EasyBlogViewFeeds extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.feeds');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		$this->setHeading('COM_EASYBLOG_BLOGS_FEEDS_TITLE', '', 'fa-rss-square');
		JToolbarHelper::addNew('feeds.add');
		JToolBarHelper::divider();
		JToolbarHelper::publishList('feeds.publish');
		JToolbarHelper::unpublishList('feeds.unpublish');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_FEEDS_DELETE_CONFIRMATION'), 'feeds.remove');

		$model = EB::model('Feeds');
		$feeds = $model->getData();
		$pagination = $model->getPagination();
		$limit = $model->getState('limit');

		if ($feeds) {

			foreach ($feeds as &$feed) {

				if ($feed->last_import == '0000-00-00 00:00:00') {
					$feed->import_text = JText::_('COM_EASYBLOG_NEVER');
				} else {
					$feed->import_text = $feed->last_import;
				}
			}
		}

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.feeds.filter_state', 'filter_state', '*', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.feeds.search', 'search', '', 'string');
		$search = trim(EBString::strtolower($search));

		$this->set('limit', $limit);
		$this->set('filterState', $filter_state);
		$this->set('search', $search);
		$this->set('feeds', $feeds);
		$this->set('pagination', $pagination);

		parent::display('feeds/default');
	}

	/**
	 * Displays the new feed form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function form()
	{
		// Load the feed table
		$feed = EB::table('Feed');
		$id = $this->input->get('id', 0, 'int');

		$session = JFactory::getSession();
		$post = $session->get('feeds.data', '', 'easyblog');

		$feed->load($id);

		if (!empty($post)) {
			$feed->bind($post);

			// Reset the session
			$session->set('feeds.data', null, 'easyblog');
		}

		$title = 'COM_EASYBLOG_FEEDS_CREATE_NEW_TITLE';

		if ($feed->id) {
			$title	= 'COM_EASYBLOG_FEEDS_EDIT_TITLE';
		}

		$this->setHeading($title, '', 'fa-rss-square');

		// Set the toolbars
		JToolBarHelper::apply('feeds.apply');
		JToolBarHelper::save('feeds.save');
		JToolbarHelper::save2new('feeds.savenew');
		JToolBarHelper::divider();
		JToolbarHelper::cancel('feeds.cancel');

		$this->set('params', $feed->getParams());
		$this->set('feed', $feed);

		parent::display('feeds/form/default');
	}
}