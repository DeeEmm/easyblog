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

class EasyBlogViewThemes extends EasyBlogAdminView
{
	/**
	 * Renders a confirmation dialog to revert changes
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmRevert()
	{
		$id = $this->input->get('id', '', 'default');
		$element = $this->input->get('element', '', 'cmd');

		$theme = EB::themes();
		$theme->set('id', $id);
		$theme->set('element', $element);
		
		$contents = $theme->output('admin/themes/dialogs/revert');

		return $this->ajax->resolve($contents);
	}
}
