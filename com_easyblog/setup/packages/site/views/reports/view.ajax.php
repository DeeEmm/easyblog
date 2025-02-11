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

class EasyBlogViewReports extends EasyBlogView
{
	/**
	 * Renders the reporting form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function form()
	{
		// Check if the user is really allowed to report
		if ($this->my->guest && !$this->config->get('main_reporting_guests')) {
			die();
		}

		// Get the composite key for the item
		$id = $this->input->get('id', 0, 'int');
		$type = $this->input->get('type', '', 'cmd');

		// Load up the dialog
		$theme = EB::themes();

		$theme->set('id', $id);
		$theme->set('type', $type);

		$output = $theme->output('site/reports/dialogs/form');

		return $this->ajax->resolve($output);
	}
}
