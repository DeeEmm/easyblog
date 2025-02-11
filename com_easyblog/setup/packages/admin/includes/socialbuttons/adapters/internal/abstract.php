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

class EasyBlogInternalButtons
{
	protected $name = null;
	protected $post = null;
	protected $title = null;
	protected $permalink = null;

	public function __construct($name, EasyBlogPost $post)
	{
		$this->name = $name;
		$this->post = $post;
		$this->title = JText::_('COM_EASYBLOG_BUTTON_' . ucfirst($name));

		$url = $post->getExternalPermalink();

		$config = EB::config();

		if (EBR::isSefEnabled() && $config->get('main_sef_unicode')) {
			// permalink might content unicode due to unicode alias enabled. #1740
			$url = $post->getExternalPermalink();
			$url = urlencode($url);
			$url = str_replace("%2F", "/", $url);
			$url = str_replace("%3A", ":", $url);
		}

		$this->permalink = $url;

		// We might need to use the shortened url
		$yourls = EB::yourls();

		if ($yourls->enabled()) {
			$this->permalink = $yourls->getShortenedUrl($post, $this->permalink);
		}
	}

	/**
	 * Provides the button name identifier
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Provides the button title
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Renders the icon class for the button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getIcon()
	{
		$icon = 'fdi fab fa-' . $this->name;

		return $icon;
	}

	/**
	 * Retrieves the post title
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostTitle()
	{
		$title = EBString::trim(urlencode($this->post->title));

		return $title;
	}
}
