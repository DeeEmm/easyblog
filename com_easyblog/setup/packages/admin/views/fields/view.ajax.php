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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewFields extends EasyBlogAdminView
{
	/**
	 * Retrieve a custom field form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getForm()
	{
		// Get the field id
		$id = $this->input->get('id', 0, 'int');

		// Get the field type
		$type = $this->input->get('type', '', 'cmd');

		if (!$type) {
			return $this->ajax->reject();
		}

		$field = EB::table('Field');
		$field->load($id);

		// Get the form
		$form = EB::fields()->get($type)->admin($field);

		return $this->ajax->resolve($form);
	}

	/**
	 * Displays a list of field groups on the site in a dialog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function browseGroup()
	{
		$theme = EB::themes();

		$output = $theme->output('admin/fields/dialogs/browsegroup');
		
		return $this->ajax->resolve($output);
	}
}