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

class EasyBlogViewFeatured extends EasyBlogView
{
	/**
	 * Displays a confirmation dialog to confirm featuring an author.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirm()
	{
		// Ensure that the user has privileges
		if (!FH::isSiteAdmin() && !$this->acl->get('feature_entry') ) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$type = $this->input->get('type', '', 'word');
		$id = $this->input->get('id', '', 'int');

		if ($type == 'blogger') {
			$title 	 = JText::_('COM_EASYBLOG_FEATURE_AUTHOR_DIALOG_TITLE');
			$content = JText::_('COM_EASYBLOG_FEATURE_AUTHOR_DIALOG_CONTENT');
		}

		if ($type == 'teamblog') {
			$title = JText::_('COM_EASYBLOG_FEATURE_TEAMBLOG_DIALOG_TITLE');
			$content = JText::_('COM_EASYBLOG_FEATURE_TEAMBLOG_DIALOG_CONTENT');
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('content', $content);

		$output = $theme->output('site/featured/dialogs/confirm');

		return $this->ajax->resolve($output);
	}

	/**
	 * Mark an item as featured item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function makeFeatured()
	{
		// Ensure that the user has privileges
		if (!FH::isSiteAdmin() && !$this->acl->get('feature_entry') ) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		// Get the type
		$type = $this->input->get('type', '', 'word');
		$id = $this->input->get('id', '', 'int');

		// Make the item featured
		$model = EB::model('Featured');
		$model->makeFeatured($type, $id);

		$this->ajax->resolve(true);
	}

	/**
	 * Removes featured status from an object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeFeatured()
	{
		// Ensure that the user has privileges
		if (!FH::isSiteAdmin() && !$this->acl->get('feature_entry') ) {
			return $this->ajax->reject(500, JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$type = $this->input->get('type', '', 'word');
		$id = $this->input->get('id', '', 'int');

		if ($type == 'blogger') {
			$title 	 = JText::_('COM_EASYBLOG_UNFEATURE_AUTHOR_DIALOG_TITLE');
			$content = JText::_('COM_EASYBLOG_UNFEATURE_AUTHOR_DIALOG_CONTENT');
		}

		if ($type == 'teamblog') {
			$title 	 = JText::_('COM_EASYBLOG_UNFEATURE_TEAMBLOG_DIALOG_TITLE');
			$content = JText::_('COM_EASYBLOG_UNFEATURE_TEAMBLOG_DIALOG_CONTENT');
		}

		$model = EB::model('Featured');
		$model->removeFeatured($type, $id);

		$theme 	= EB::themes();

		$theme->set('title', $title);
		$theme->set('content', $content);

		$output = $theme->output('site/featured/dialogs/unfeature');

		return $this->ajax->resolve($output);
	}
}
