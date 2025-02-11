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

require_once(__DIR__ . '/abstract.php');

class EasyBlogQuickPostText extends EasyBlogQuickPostAbstract
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
		$title = $this->input->get('title', '', 'default');
		$content = $this->input->get('content', '', 'default');

		// @rule: Title will be optional here
		if (!$title) {
			$title = EBString::substr(strip_tags($content), 0, 10) . JText::_('COM_EASYBLOG_ELLIPSES');
		}
		
		// Replace newlines with <br /> tags since the form is a plain textarea.
		$content = nl2br($content);
			
		$blog->title = $title;
		$blog->content = $content;
	}

	/**
	 * Validates the quick post submission
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function validate()
	{
		$content = $this->input->get('content', '', 'default');

		if (!$content) {
			return EB::exception('COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_CONTENT', 'error');
		}

		return true;
	}

	/**
	 * Since normal text posts doesn't contains any assets.
	 */
	public function afterSave()
	{
		return true;
	}

	public function getSuccessMessage()
	{
		return JText::_( 'COM_EASYBLOG_MICROBLOG_TEXT_POSTED_SUCCESSFULLY' );
	}
}
