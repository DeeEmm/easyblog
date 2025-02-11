<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogCommentJComments extends EasyBlog
{
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_jcomments/jcomments.php';

		if (!JFile::exists($file)) {
			return false;
		}

		include_once($file);

		return true;
	}

	public function html(EasyBlogPost &$blog)
	{
		if (!$this->exists()) {
			return false;
		}

		$template = EB::themes();
		$template->set('blog', $blog);
		$output = $template->output('site/comments/jcomments');

		return $output;
	}

	/**
	 * Retrieves the comment count for disqus
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getCount(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return false;
		}

		$db = EB::db();

		$query = array();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__jcomments');
		$query[] = 'WHERE ' . $db->qn('object_id') . '=' . $db->Quote($post->id);
		$query[] = 'AND ' . $db->qn('object_group') . '=' . $db->Quote('com_easyblog');
		$query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote(1);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$count = $db->loadResult();

		return $count;
	}
}


