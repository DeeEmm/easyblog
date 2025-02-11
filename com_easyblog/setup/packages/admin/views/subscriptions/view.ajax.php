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

class EasyBlogViewSubscriptions extends EasyBlogAdminView
{
	/**
	 * Allows admin to create a new manual subscriber on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function form()
	{
		// Check for access
		$this->checkAccess('easyblog.manage.subscription');

		// Get the type
		$type = $this->input->get('type', '', 'word');

		$theme = EB::themes();
		$theme->set('type', $type);
		$output = $theme->output('admin/subscriptions/dialogs/form');

		return $this->ajax->resolve($output);
	}
}
