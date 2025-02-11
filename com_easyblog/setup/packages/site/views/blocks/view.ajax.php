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

require_once(EBLOG_ROOT . '/views/views.php');

class EasyBlogViewBlocks extends EasyBlogView
{
	/**
	 * Displays confirmation to toggle unpublished state of the post templates
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function confirmDeleteBlockTemplates()
	{
		$ids = $this->input->get('ids', array(), 'array');
		$from = $this->input->get('from', 'listing', 'default');

		foreach ($ids as &$id) {
			$id = (int) $id;
		}

		$theme = EB::themes();
		$theme->set('ids', $ids);
		$theme->set('from', $from);
		$output = $theme->output('site/dashboard/blocktemplates/dialogs/delete');

		return $this->ajax->resolve($output);
	}
}
