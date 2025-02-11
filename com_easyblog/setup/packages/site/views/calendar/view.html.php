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

class EasyBlogViewCalendar extends EasyBlogView
{
	/**
	 * This method would be invoked by the parent to set any params
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function defineParams()
	{
		$layout = $this->app->input->get('layout', '', 'cmd');
		$activeMenu = $this->getActiveMenu();

		// No active menu, we just return the default
		if (!$activeMenu) {
			return;
		}

		$segments = $activeMenu->query;

		if (empty($layout)) {
			$params = $this->getActiveMenuParams();

			return $params;
		}
	}

	/**
	 * Displays the calendar layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$calendarStyle = $this->params->get('calendar_style', 'calendarView');
		$layout = $this->getLayout();

		if ($layout === 'default') {
			$layout = $calendarStyle;
		}

		// Set the pathway
		$this->setPathway('COM_EASYBLOG_CALENDAR_BREADCRUMB');

		// Get the year and month if it's defined in the menu
		$year = $this->theme->params->get('calendar_year', 0);
		$month = $this->theme->params->get('calendar_month', 0);

		// Perhaps the year and month are being passed as query strings
		$year = $this->input->get('year', $year, 'default');
		$month = $this->input->get('month', $month, 'default');
		$day = $this->input->get('day', '01', 'default');

		// If category is provided, we should filter by specific category
		$rawCategory = $this->input->get('category', array(), 'array');
		$category = implode(",", $rawCategory);

		// Get the Itemid
		$itemId = $this->input->get('Itemid', 0, 'int');
		$archives = $this->input->get('archives', 0, 'int');

		// Try to generate timestamp if there's year and month provided
		$timestamp = '';
		$timestampDate = '';

		if ($year && $month) {
			$timestampDate = EB::date($year . '-' . $month . '-' . $day);
			$timestamp = strtotime($timestampDate->toSql());
		}

		// Get the date object based on the timestamp provided
		$date = EB::calendar()->getDateObject($timestamp);

		$canonical = 'index.php?option=com_easyblog&view=calendar';

		if ($layout == 'listView') {
			$canonical = 'index.php?option=com_easyblog&view=calendar&layout=listView&month=' . $date->month . '&year=' . $date->year;
		}

		if ($layout == 'calendarView') {
			$canonical = 'index.php?option=com_easyblog&view=calendar&layout=calendarView&month=' . $date->month . '&year=' . $date->year;
		}

		// Check if user access curent month calendar or not. #123
		$currentMonth = EB::calendar()->isCurrentMonth($date);

		if (!$currentMonth) {
			$canonical = 'index.php?option=com_easyblog&view=calendar&year=' . $date->year . '&month=' . $date->month;

			if ($layout == 'listView') {
				$canonical = 'index.php?option=com_easyblog&view=calendar&layout=listView&month=' . $date->month . '&year=' . $date->year;
			}

			if ($layout == 'calendarView') {
				$canonical = 'index.php?option=com_easyblog&view=calendar&layout=calendarView&month=' . $date->month . '&year=' . $date->year;
			}
		}

		$this->canonical($canonical);

		// Get the related data
		$calendar = EB::calendar()->prepare($date);

		// Prepare the date
		if (!$timestampDate) {
			$timestampDate = EB::date($date->unix);
		}

		// Set list view permalink
		$listViewUrl = 'index.php?option=com_easyblog&view=calendar&layout=listView&month=' . $date->month . '&year=' . $date->year;

		if ($rawCategory) {
			$listViewUrl .= '&category=' . $rawCategory;
		}

		$listViewUrl = EBR::_($listViewUrl);

		// meta, too late to add new meta id so we 'trick' the system to use the custom description.
		EB::setMeta('0', META_TYPE_VIEW, JText::sprintf('COM_EASYBLOG_CALENDAR_PAGE_TITLE_VIEW', $timestampDate->format('F'), $timestampDate->format('Y')));

		// Set the page title
		$title = EB::getPageTitle(JText::sprintf('COM_EASYBLOG_CALENDAR_PAGE_TITLE_VIEW', $timestampDate->format('F'), $timestampDate->format('Y')));

		// Default namespace
		$namespace = 'calendar/list/default';

		$posts = [];
		$pagination = null;

		if ($layout == 'calendarView') {
			$namespace = 'calendar/default/default';
		}

		if ($layout !== 'calendarView') {
			$options = [
				'usePagination' => true,
				'limit' => EB::getLimit()
			];

			// Get the posts data
			$model = EB::model('Archive');
			$posts = $model->getArchivePostListByMonth($date->month, $date->year, false, $category, $archives, $options);
			$pagination = $model->getPagination();

			// Format the blog posts
			$options = [
				'cacheComment' => false,
				'cacheCommentCount' => false,
				'cacheRatings' => false,
				'cacheTags' => false,
				'cacheAuthors' => false,
				'cacheVoted' => false,
				'cacheFeatured' => false,
				'cacheFields' => false,
				'loadAuthor' => false,
				'loadFields' => false
			];

			$posts = EB::formatter('list', $posts, true, $options);


			// These meta tag for listView
			// meta, too late to add new meta id so we 'trick' the system to use the custom description.
			EB::setMeta('0', META_TYPE_VIEW, JText::sprintf('COM_EASYBLOG_CALENDAR_PAGE_TITLE_FOR_LIST_VIEW', $timestampDate->format('F'), $timestampDate->format('Y')));

			// Set the page title
			$title = EB::getPageTitle(JText::sprintf('COM_EASYBLOG_CALENDAR_PAGE_TITLE_FOR_LIST_VIEW', $timestampDate->format('F'), $timestampDate->format('Y')));
		}

		// display the page title on the page
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));

		$this->set('archives', $archives);
		$this->set('posts', $posts);
		$this->set('category', $category);
		$this->set('date', $timestampDate);
		$this->set('timestamp', $timestamp);
		$this->set('listViewUrl', $listViewUrl);
		$this->set('pagination', $pagination);

		return parent::display($namespace);
	}
}
