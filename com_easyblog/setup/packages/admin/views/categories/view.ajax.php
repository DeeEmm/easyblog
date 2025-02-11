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

class EasyBlogViewCategories extends EasyBlogAdminView
{
	/**
	 * Allows caller to browse for categories
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function browse($tpl = null)
	{
		$theme = EB::themes();

		$output	= $theme->output('admin/categories/dialogs/browse');
		
		return $this->ajax->resolve($output);
	}

	/**
     * Allow caller to delete category avatar
     *
     * @since   5.1
     * @access  public
     */
	public function confirmRemoveAvatar()
	{
		$id = $this->input->get('id', 0, 'int');

		$theme = EB::themes();
		$theme->set('id', $id);
		$output = $theme->output('admin/categories/dialogs/remove.avatar');

		return $this->ajax->resolve($output);
	}

	/**
	 * Display a dialof of confirmation on removing the default post cover
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function confirmRemovePostCover($tpl = null)
	{
		$id = $this->input->get('id', 0, 'int');

		$themes = EB::themes();
		$themes->set('id', $id);

		$output = $themes->output('admin/categories/dialogs/remove.post.cover');

		return $this->ajax->resolve($output);
	}
}
