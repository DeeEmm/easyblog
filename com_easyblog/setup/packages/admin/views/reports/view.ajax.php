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
	 * Dialog for unpublish confirmation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmUnpublish()
	{
		// Get the report id
		$id = $this->input->get('id', 0, 'int');

		$template = EB::themes();

		$template->set('id', $id);

		$output = $template->output('admin/reports/dialogs/unpublish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Dialog for delete confirmation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDelete()
	{
		// Get the report id
		$id = $this->input->get('id', 0, 'int');

		$template = EB::themes();

		$template->set('id', $id);

		$output = $template->output('admin/reports/dialogs/delete');

		return $this->ajax->resolve($output);
	}
}
