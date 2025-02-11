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
	 * Displays the calendar via ajax
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function render()
	{
		$timestamp = $this->input->get('timestamp', '', 'default');
		$category = $this->input->get('category', '', 'default');
		$archives = $this->input->get('archives', false, 'bool');

		// Sanitize the category
		$category = EB::sanitizeCsv($category, 'integer');

		$model = EB::model('Archive');
		$date = EB::calendar()->getDateObject($timestamp);
		$calendar = EB::calendar()->prepare($date);

		$posts = $model->getArchivePostByMonth($date->month, $date->year, false, $category, $archives);

		$datetimeStamp = EB::date($date->year . '-' . $date->month . '-' . $date->day);

		// Format the calendar name
		$displayDate = $datetimeStamp->format(JText::_('COM_EB_CALENDAR_HEADING'));

		// Update the url of list view
		$newListUrl = EBR::_('index.php?option=com_easyblog&view=calendar&layout=listView&month=' . $date->month . '&year=' . $date->year);

		$date->display = $datetimeStamp->format(JText::_('COM_EB_CALENDAR_HEADING_SHORT_MONTH'));

		$template = EB::themes();
		$template->set('calendar', $calendar);
		$template->set('date', $date);
		$template->set('posts', $posts);

		$output = $template->output('site/calendar/calendar/default');

		return $this->ajax->resolve($output, $displayDate, $newListUrl);
	}
}
