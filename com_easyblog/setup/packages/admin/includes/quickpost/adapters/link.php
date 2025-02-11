<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogQuickPostLink extends EasyBlogQuickPostAbstract
{
	/**
	 * Maps data from the request
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function bind(EasyBlogPost &$post)
	{
		// Get the data from the request
		$content = $this->input->get('content', '', 'default');
		$title = $this->input->get('title', '', 'default');
		$link = $this->input->get('link', '', 'default');

		// If title wasn't set, use the link as the title
		if (!$title) {
			$title = $link;
		}

		// Determine if we need to download an image from external urls
		$externalImage = $this->input->get('externalImage', '', 'default');

		if ($externalImage) {
			$this->downloadExternalImage($externalImage, $post);
		}

		// Determine if the user is using an internal image
		$internalImage = $this->input->get('internalImage', '', 'default');

		if ($internalImage) {
			$post->image = $internalImage;
		}
		
		$content = nl2br($content);

		if (!$content) {
			$date = EB::date();
			$content = '<p>' . JText::sprintf('COM_EASYBLOG_MICROBLOG_LINK_CONTENT_GENERIC', $date->format(JText::_('DATE_FORMAT_LC2'))) . '</p>';
		}

		$post->title = $title;
		$post->content = $content;
		$post->posttype = EBLOG_MICROBLOG_LINK;
	}

	/**
	 * Downloads an external image
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function downloadExternalImage($url, EasyBlogPost &$post)
	{
		$image = EB::image();
		
		try {
			$result = $image->download($url);

			$post->image = $result->uri;

		} catch (Exception $e) {

		}
	}

	public function afterSave(&$blog)
	{
		return true;
	}

	public function getSuccessMessage()
	{
		return JText::_('COM_EASYBLOG_MICROBLOG_LINK_POSTED_SUCCESSFULLY');
	}

	/**
	 * Dummy function as links uses the title, we just need to add a "link" column.
	 */
	public function format(EasyBlogPost &$blog)
	{
		$blog->link = $blog->title;
	}

	/**
	 * Save assets
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function saveAssets($blog)
	{
		$link = $this->input->get('link', '', 'default');

		if (!$link) {
			return JText::_('COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_LINK');
		}

		// Load up the assets object
		$assets = EB::table('assets');

		// try to load existing record if there is any.
		$assets->load(array('post_id' => $blog->id, 'key' => 'link'));

		$assets->post_id = $blog->id;
		$assets->key = 'link';
		$assets->value = $link;

		$assets->store();

		return true;
	}

	/**
	 * Validates the quick post submission
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function validate()
	{
		$link = $this->input->get('link', '', 'default');
		$title = $this->input->get('title', '', 'default');

		if (!$link) {
			return EB::exception('COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_LINK', 'error');
		}

		if (!$title) {
			return EB::exception('COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_TITLE', 'error');
		}

		return true;
	}
}
