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

class EasyBlogCommentEasySocial extends EasyBlogCommentBase
{
	/**
	 * Determines if EasySocial exists on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function exists()
	{
		$lib = EB::easysocial();

		return $lib->exists();
	}

	/**
	 * Renders the comment form 
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function html(EasyBlogPost &$blog)
	{
		if (!$this->exists()) {
			return;
		}
	}

	/**
	 * Renders the comment count for Komento
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getCount(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return;
		}

		ES::language()->load('com_easysocial', JPATH_ROOT);

		$url = $post->getPermalink();
		$comments = ES::comments($post->id, 'blog', 'create', SOCIAL_APPS_GROUP_USER, $url);
		$count = $comments->getCount();
		
		return $count;
	}
}
