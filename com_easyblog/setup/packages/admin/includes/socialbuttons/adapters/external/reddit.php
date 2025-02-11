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

class EasyBlogExternalButtonReddit extends EasyBlogSocialButton
{
	public $type = 'reddit';

	/**
	 * Outputs the html code for Google One button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html()
	{
		// If this is a frontpage, ensure that show in frontpage is enabled
		if (!$this->isEnabled()) {
			return;
		}

		// Get the button size
		$size = $this->getButtonSize();

		// Get the absolute url to this blog post
		$url = $this->getUrl();

		// Ge the formatted title to this blog post
		$title = $this->getTitle();

		$theme = EB::themes();
		$theme->set('size', $size);
		$theme->set('url', $url);
		$theme->set('title', $title);

		$output = $theme->output('site/socialbuttons/external/reddit');

		return $output;
	}

	/**
	 * Determines if reddit button should appear
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isEnabled()
	{
		return $this->config->get('main_reddit_button');
	}
}
