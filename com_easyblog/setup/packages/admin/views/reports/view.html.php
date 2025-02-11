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

require_once(JPATH_COMPONENT . '/views.php');

class EasyBlogViewReports extends EasyBlogAdminView
{
	/**
	 * Displays a list of reports
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Test for user access if on 1.6 and above
		$this->checkAccess('easyblog.manage.report');

		$this->setHeading('COM_EASYBLOG_TITLE_REPORTS', '', 'fa-exclamation-triangle');
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_CONFIRM_DISCARD_REPORTS'), 'reports.discard', JText::_('COM_EASYBLOG_DISCARD_REPORT_BUTTON'));

		$order = $this->app->getUserStateFromRequest('com_easyblog.reports.filter_order', 'filter_order', 'a.created', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easyblog.reports.filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );

		// Load front end language file
		EB::loadLanguages();
		
		// Get reports
		$model = EB::model('Reports');
		$result = $model->getData();
		$pagination	= $model->getPagination();

		$reports = array();

		if ($result) {
			foreach ($result as $row) {
				$report = EB::table('Report');
				$report->bind($row);

				$post = EB::table('Post');
				$post->load($report->obj_id);

				$report->blog = $post;

				$reports[]	= $report;
			}
		}

		$this->set('pagination', $pagination);
		$this->set('reports', $reports);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('reports/default');
	}
}
