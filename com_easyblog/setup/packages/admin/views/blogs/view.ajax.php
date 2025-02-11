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

require_once(JPATH_COMPONENT . '/views.php');

class EasyBlogViewBlogs extends EasyBlogAdminView
{
	/**
	 * Allows caller to re-notify subscribers
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function confirmNotify()
	{
		$id = $this->input->get('id', 0, 'int');

		$theme = EB::themes();
		$theme->set('id', $id);

		$output = $theme->output('admin/blogs/dialogs/notify');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the dialog to allow admin to move posts between categories
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function move()
	{
		$filter = array();
		$filter[] = JHTML::_('select.option', '', JText::_('COM_EASYBLOG_SELECT_CATEGORY'));

		$model = EB::model('Category');
		$categories = $model->getAllCategories();

		foreach ($categories as $cat) {
			$filter[] = JHTML::_('select.option', $cat->id, $cat->title);
		}

		$theme = EB::themes();
		$theme->set('filter', $filter);

		$output = $theme->output('admin/blogs/dialogs/move');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the dialog to allow admin to import post templates
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function importForm()
	{
		$theme = EB::themes();
		$output = $theme->output('admin/blogs/templates/dialog/import');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to empty trash
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function emptyTrash()
	{
		$theme = EB::themes();
		$output = $theme->output('admin/blogs/dialogs/empty.trash');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to accept a pending blog post
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function confirmAccept()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = EB::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('admin/blogs/dialogs/accept');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to reject a pending blog post
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function confirmReject()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = EB::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('admin/blogs/dialogs/reject');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to remove a pending blog post
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function confirmRemovePending()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = EB::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('admin/blogs/dialogs/remove.pending');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to auto post the post
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function confirmAutopost()
	{
		$id = $this->input->get('id', 0, 'int');
		$type = $this->input->get('type', '', 'word');

		// Determines if the primary category of this post requires auto posting or not
		$post = EB::post($id);
		$category = $post->getPrimaryCategory();

		if (!$category->autopost) {
			$output = JText::_('COM_EB_AUTOPOST_DISABLED_IN_CATEORY');
			return $this->ajax->resolve($output);
		}

		$theme = EB::themes();
		$theme->set('type', $type);
		$theme->set('id', $id);

		$output = $theme->output('admin/blogs/dialogs/autopost');

		return $this->ajax->resolve($output);
	}


	/**
	 * Renders the dialog to re-assign authors for the post
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function authors()
	{
		$filter = array();
		$filter[] = JHTML::_('select.option', '', JText::_('COM_EASYBLOG_SELECT_AUTHOR'));

		$model = EB::model('Users');
		$users = $model->getUsers(true, false);

		if ($users) {
			foreach ($users as $user) {
				$filter[] = JHTML::_('select.option', $user->id, $user->name);
			}
		}

		$theme = EB::themes();
		$theme->set('filter', $filter);

		$output = $theme->output('admin/blogs/dialogs/authors');
		return $this->ajax->resolve($output);
	}

	/**
	 * Display confirmation box when admin want to delete template thumbnails
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDeleteThumbnails()
	{
		$id = $this->input->get('id', 0, 'int');

		$theme = EB::themes();
		$theme->set('id', $id);
		$output = $theme->output('admin/blogs/dialogs/restore.thumbnails');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the dialog to assign custom tag for the existing post
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function massAssignTags()
	{
		$theme = EB::themes();
		$output = $theme->output('admin/blogs/dialogs/mass.assign.tags');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows caller to browse for blogs
	 *
	 * @since	5.3
	 * @access	public
	 */
	public function browse($tpl = null)
	{
		$theme = EB::themes();

		$output	= $theme->output('admin/blogs/dialogs/browse');
		
		return $this->ajax->resolve($output);
	}
}
