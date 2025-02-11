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

class EasyBlogExternalButtonVk extends EasyBlogSocialButton
{
	public $type = 'vk';

	/**
	 * Appends the vk script on the head of the page
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function addScript()
	{
		static $loaded = false;

		if (!$loaded) {
			$this->doc->addScript('//vk.com/js/api/openapi.js?116');

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Outputs the html code for Twitter button
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

		// The default button size
		$size = $this->getButtonSize();
		$placeholder = $this->getPlaceholderId();

		// Add the script
		$this->addScript();

		// Get the absolute url to this blog post
		$url = $this->getUrl();

		// Ge the formatted title to this blog post
		$title = $this->getTitle();

		// Get the desc
		$desc = $this->getDescription();

		// Get the blog image
		$image = $this->getImage();

		$theme = EB::themes();

		$apiKey = $this->config->get('main_vk_api');
		
		$theme->set('apiKey', $apiKey);
		$theme->set('url', $url);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('image', $image);
		$theme->set('size', $size);
		$theme->set('placeholder', $placeholder);

		$output = $theme->output('site/socialbuttons/external/vk');
		return $output;
	}

	/**
	 * Determines if the button should appear on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isEnabled()
	{
		// Ensure that vk api id is set
		if (!$this->config->get('main_vk_api')) {
			return false;
		}

		return $this->config->get('main_vk');
	}
}
