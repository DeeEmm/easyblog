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

class EasyBlogViewSpools extends EasyBlogAdminView 
{
	/**
	 * Previews an email
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function preview()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject();
		}

		$mailq = EB::table('Mailqueue');
		$mailq->load($id);
		
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&view=spools&layout=preview&tmpl=component&id=' . $mailq->id;		

		$theme = EB::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/spools/dialogs/preview');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders an email template preview
	 *
	 * @since	5.1.11
	 * @access	public
	 */
	public function templatePreview()
	{
		$file = $this->input->get('file', '', 'default');

		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&view=spools&layout=templatePreview&tmpl=component&file=' . $file;		

		$theme = EB::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/spools/dialogs/preview');

		return $this->ajax->resolve($output);
	}
}
