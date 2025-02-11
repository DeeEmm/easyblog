<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewPolls extends EasyBlogAdminView
{
	/**
	 * Displays a list of polls
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('easyblog.manage.polls');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Load the frontend language
		EB::loadLanguages();

		$this->setHeading('COM_EB_TITLE_POLLS');

		JToolbarHelper::addNew('polls.create');
		JToolbarHelper::publishList('polls.publish');
		JToolbarHelper::unpublishList('polls.unpublish');
		JToolbarHelper::deleteList('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', 'polls.delete');

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.polls.filter_state', 'filter_state',  'all', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.polls.search', 'search', '', 'string');
		$search = EBString::trim($search);

		$order = $this->app->getUserStateFromRequest('com_easyblog.polls.filter_order', 'filter_order', 'a.ordering', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_easyblog.polls.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		$options = [];
		$options['state'] = $filter_state;
		$options['search'] = $search;
		$options['includeAll'] = true;

		$model  = EB::model('Polls');

		$limit = $model->getState('limit');
		$options['limit'] = $limit;

		$polls = $model->getPolls($options);
		$pagination = $model->getPagination();

		$this->set('limit', $limit);
		$this->set('currentFilter', $filter_state);
		$this->set('search', $search);
		$this->set('polls', $polls);
		$this->set('pagination', $pagination);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('polls/default');
	}
}
