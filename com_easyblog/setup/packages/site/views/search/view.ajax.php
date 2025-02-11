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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewSearch extends EasyBlogView
{
	/**
	 * Allows caller to search for posts
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function search()
	{
		$query = $this->input->get('query', '', 'string');

		$options = array('search' => $query, 'limit' => 20, 'sort' => 'title');

		$model = EB::model('Blog');
		$items = $model->getUserPosts($this->my->id, $options);

		if (!$items) {
			return $this->ajax->resolve(JText::_('COM_EASYBLOG_SEARCH_POSTS_NO_RESULTS'), 0);
		}

		//cache the posts
		EB::cache()->cachePosts($items);

		$posts = array();

		foreach ($items as $item) {

			$post = EB::post($item->id);

			// Set a formatted date
			$post->formattedDate = EB::date($post->created)->format(JText::_('DATE_FORMAT_LC2'));
			$post->intro = $post->getIntro(true);
			$post->permalink = $post->getExternalPermalink();

			$posts[] = $post;
		}

		$theme = EB::themes();
		$theme->set('posts', $posts);

		$output = $theme->output('site/composer/posts/default');

		return $this->ajax->resolve($output, count($posts));
	}
}
