<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogQuickPostEmail extends EasyBlogQuickPostAbstract
{
	/**
	 * Processes the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind(&$blog)
	{
	}

	/**
	 * Method to validate a post
	 */
	public function validate()
	{
		$title = $this->input->get('quote', '', 'default');

		if (!$title) {
			return JText::_('COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_CONTENT');
		}

		return true;
	}

	/**
	 * Since quotes are stored in the title, we don't really need to do anything here
	 */
	public function afterSave(&$blog)
	{
		return true;
	}

	public function getSuccessMessage()
	{
		return JText::_('COM_EASYBLOG_MICROBLOG_QUOTE_POSTED_SUCCESSFULLY');
	}

	public function format(EasyBlogPost &$blog)
	{
		parent::format($blog);
	}
}
