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

class EasyBlogViewSubscriptions extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.subscription');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}
		
		// Set the page heading
		$this->setHeading('COM_EASYBLOG_TITLE_SUBSCRIPTIONS', '', 'fa-bell');

		JToolbarHelper::addNew('subscriptions.form');
		JToolbarHelper::deleteList('COM_EASYBLOG_CONFIRM_REMOVE_SUBSCRIBER', 'subscriptions.remove');
		
		$currentFilter = $this->app->getUserStateFromRequest('com_easyblog.subscriptions.filter', 'filter', 'site', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.subscriptions.search', 'search', '', 'string' );

		$search = trim(EBString::strtolower( $search ) );
		$order = $this->app->getUserStateFromRequest( 'com_easyblog.subscriptions.filter_order', 'filter_order', 'bname', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_easyblog.subscriptions.filter_order_Dir', 'filter_order_Dir', '', 'word');

		//Get data from the model
		$model = EB::model('Subscriptions');
		$subscriptions = $model->getSubscriptions();
		$pagination = $model->getPagination();
		$limit = $model->getState('limit');

		$this->set('limit', $limit);
		$this->set('subscriptions', $subscriptions);
		$this->set('pagination', $pagination);
		$this->set('currentFilter', $currentFilter);
		$this->set('search', $search );
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('subscriptions/default');
	}

	/**
	 * Renders the subscription form
	 *
	 * @since	5.3
	 * @access	public
	 */
	public function form()
	{
		$this->setHeading('COM_EASYBLOG_SUBSCRIPTION_FORM', '', 'fa-bell');

		$id = $this->input->get('id', 0, 'int');

		$subscription = EB::table('Subscriptions');
		$subscription->load($id);

		$isNew = $subscription->id ? false : true;

		$model = EB::model('Category');
		$categories = $model->getAllCategories();

		if ($isNew) {
			JToolbarHelper::save('subscriptions.create');
		} else {
			JToolBarHelper::apply('subscriptions.apply');
			JToolbarHelper::save('subscriptions.save');
		}

		JToolBarHelper::cancel('subscriptions.cancel');

		$this->set('subscription', $subscription);
		$this->set('categories', $categories);

		parent::display('subscriptions/form/default');

	}

	public function import()
	{
		$this->setHeading('COM_EASYBLOG_SUBSCRIPTION_IMPORT', '', 'fa-envelope-o');

		parent::display('subscriptions/import');
	}

	public function export()
	{
		$this->setHeading('COM_EASYBLOG_SUBSCRIPTION_EXPORT', '', 'fa-envelope-o');

		parent::display('subscriptions/export');
	}
}
