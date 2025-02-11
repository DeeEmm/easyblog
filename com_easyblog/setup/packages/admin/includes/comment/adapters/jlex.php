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

require_once(__DIR__ . '/base.php');

class EasyBlogCommentJlex extends EasyBlogCommentBase
{
	/**
	 * Determines if Jlex comments is installed
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_jlexcomment/load.php';
	
		if (!JFile::exists($file)) {
			return false;
		}
		
		require_once($file);

		return true;
	}

	/**
	 * Renders the comment form from Jlex
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html(EasyBlogPost &$post)
	{
		if (!$this->exists()) {
			return;
		}

		$output = JLexCommentLoader::init('easyblog', $post->id, $post->getTitle());

		return $output;
	}

	/**
	 * Renders the comment count for Jlex
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCount(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return;
		}

		$count = JLexCommentLoader::count_cm('easyblog', $post->id);

		return $count->cm_count;
	}
}