<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogExternalButtonTwitter extends EasyBlogSocialButton
{
	public $type = 'twitter';

	/**
	 * Outputs the html code for Twitter button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html()
	{
		// Get the button size
		$size = $this->getButtonSize();

		// Get the via text
		$via = $this->config->get('main_twitter_button_via_screen_name', '');

		if ($via && $this->doc->getType() == 'html') {
			$via = EBString::substr($via, 1);

			$this->doc->addHeadLink('https://twitter.com/' . $via, 'me');
		}

		// Get the absolute url to this blog post
		$url = $this->getUrl();

		// Ge the formatted title to this blog post
		$title = $this->getTitle();

		// Twitter's sharing shouldn't have urlencoded values
		$title = urldecode($title);

		// Remove unwanted character inside url to avoid incorrect url sharing
		$title = str_replace('"', '', $title);

		// Determines if we should track with analytics
		$tracking = $this->config->get('main_twitter_analytics');
		$placeholder = $this->getPlaceholderId();

		$theme = EB::themes();
		$theme->set('tracking', $tracking);
		$theme->set('size', $size);
		$theme->set('via', $via);
		$theme->set('placeholder', $placeholder);
		$theme->set('url', $url);
		$theme->set('title', $title);

		$output = $theme->output('site/socialbuttons/external/twitter');
		return $output;
	}


	/**
	 * Determines if the twitter button should appear
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isEnabled()
	{
		return $this->config->get('main_twitter_button');
	}
}
