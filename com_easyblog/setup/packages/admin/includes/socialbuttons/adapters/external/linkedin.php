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

class EasyBlogExternalButtonLinkedin extends EasyBlogSocialButton
{
	public $type = 'linkedin';

	/**
	 * Outputs the html code for Linkedin button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html()
	{
		// Get the button size
		$size = $this->getButtonSize();
		$placeholder = $this->getPlaceholderId();

		// Get the absolute url to this blog post
		$url 	= $this->getUrl();

		// Ge the formatted title to this blog post
		$title	= $this->getTitle();

		$theme 	= EB::themes();
		$theme->set('size', $size);
		$theme->set('placeholder', $placeholder);
		$theme->set('url', $url);
		$theme->set('title', $title);

		$output = $theme->output('site/socialbuttons/external/linkedin');

		return $output;
	}

	/**
	 * Determines if linkedin buttons should be enabled
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isEnabled()
	{
		return $this->config->get('main_linkedin_button');
	}
}
