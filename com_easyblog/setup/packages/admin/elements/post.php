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

require_once(__DIR__ . '/abstract.php');

class JFormFieldPost extends EasyBlogFormField
{
	protected $type = 'Post';

	/**
	 * Displays the post selection form
	 *
	 * @since	5.0
	 * @access	public
	 */
	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}

		$title = JText::_('COM_EASYBLOG_MENU_SELECT_POST_TITLE');

		if ($this->value) {
			$post = EB::post($this->value);
			$title = $post->title;
		}

		$themes = EB::themes();
		$themes->set('id', $this->id);
		$themes->set('name', $this->name);
		$themes->set('value', $this->value);
		$themes->set('title', $title);
		$themes->set('isJoomla4', EB::isJoomla4());

		$output = $themes->output('admin/elements/post');

		return $output;
	}
}