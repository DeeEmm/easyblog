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

class EasyBlogViewEntry extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Get the blog post
		$id = $this->input->get('id', 0, 'int');

		// Load the blog post now
		$blog = EB::post($id);

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$blog->id) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// If the settings requires the user to be logged in, do not allow guests here.
		if ($this->my->id <= 0 && $this->config->get('main_login_read')) {

			$url = EB::_('index.php?option=com_easyblog&view=entry&id=' . $id . '&layout=login', false);

			return $this->app->redirect($url);
		}

		// Check if blog is password protected.
		if ($this->config->get('main_password_protect') && !empty($blog->blogpassword) && !$blog->verifyPassword()) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// If the blog post is already deleted, we shouldn't let it to be accessible at all.
		if ($blog->isTrashed()) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// Check if the blog post is trashed
		if (!$blog->isPublished() && $my->id != $blog->created_by && !FH::isSiteAdmin()) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// If the viewer is the owner of the blog post, display a proper message
		if ($this->my->id == $blog->created_by && !$blog->isPublished()) {
			$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_OWNER');
		}

		if (FH::isSiteAdmin() && !$blog->isPublished()) {
			$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_ADMIN');
		}

		$blog = EB::formatter('post', $blog);

		$this->set('post', $blog);

		return parent::display();
	}

	public function related()
	{
		// Get the blog post
		$id = $this->input->get('id', 0, 'int');
		$entry = EB::post($id);

		$entry = EB::formatter('entry', $entry);

		// Get the menu params associated with this post
		$params = $entry->getMenuParams();

		// Load up the blog model
		$model = EB::model('Blog');

		$limit = (int) $params->get('post_related_limit', 5);
		if ($limit < 0) {
			$limit = 5;
		}

		$behavior = $params->get('post_related_behavior', 'tags');
		$options = array('newness' => $params->get('post_related_interval', 180));

		$relatedPosts = $model->getRelatedPosts($entry->id, $limit, $behavior, $entry->category->id, $entry->getTitle(), $options);

		$items = array();

		// Format the related posts image
		if ($relatedPosts) {
			foreach ($relatedPosts as $post) {

				$post = EB::post($post->id);

				$post->postimage = $post->getImage('thumbnail', true, true);

				// Try to get the first image in the post
				if (!$post->hasImage()) {
					$content = $post->getContent(EASYBLOG_VIEW_ENTRY);

					$image = EB::string()->getImage($content);

					if ($image) {
						$post->postimage = $image;
					}
				}

				$permalink = $post->getPermalink(true,true);

				$protocol = array("https://", "http://");

				$item = new stdClass;
				$item->title = $post->getTitle();
				$item->url = str_replace($protocol, "//", $permalink);
				$item->thumbnail = str_replace($protocol, "//", $post->postimage);

				$items[] = $item;
			}
		}

		$this->set('items', $items);

		return parent::display();
	}

}
