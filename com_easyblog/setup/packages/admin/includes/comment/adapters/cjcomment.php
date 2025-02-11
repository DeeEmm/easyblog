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

require_once(__DIR__ . '/base.php');

class EasyBlogCommentCjComment extends EasyBlogCommentBase
{
	/**
	 * Determines if compojoom comments is installed
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_comment/helpers/utils.php';
	
		if (!JFile::exists($file)) {
			return false;
		}

		// Discover their library
		JLoader::discover('ccommentHelper', JPATH_ROOT . '/components/com_comment/helpers');

		return true;
	}

	/**
	 * Retrieves the comment plugin from compojoom
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPlugin(EasyBlogPost $post)
	{
		$plugin = ccommentHelperUtils::getPlugin('com_easyblog', $post);

		return $plugin;
	}

	/**
	 * Renders the comment form from compojoom
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html(EasyBlogPost &$blog)
	{
		if (!$this->exists()) {
			return;
		}

		$output = ccommentHelperUtils::commentInit('com_easyblog', $blog);

		return $output;
	}

	/**
	 * Renders the comment count for Ccomment
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCount(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return;
		}

		$plugin = $this->getPlugin($post);

		$count = ccommentHelperUtils::getCommentCount($plugin, 'com_easyblog');

		return $count;
	}
}