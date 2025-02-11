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

class EasyBlogExternalButtonFacebook extends EasyBlogSocialButton
{
	public $type = 'facebook';

	/**
	 * Retrieves the correct locale mapping for facebook
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function getLocale()
	{
		// Load up the document
		$language = $this->doc->getLanguage();
		$language = explode('-', $language);

		// If the language doesn't use proper internationalization, we use the default one.
		if (count($language) != 2) {
			$language = array('en', 'GB');
		}

		$locale = $language[0] . '_' . strtoupper($language[1]);

		return $locale;
	}

	/**
	 * Renders Facebook's button HTML
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html()
	{
		// Get the button size
		$size = $this->getButtonSize(); 

		// Get standard properties
		$locale = $this->getLocale();
		$width = $this->config->get('main_facebook_like_width');
		$verb = $this->config->get('main_facebook_like_verb');
		$fbTheme = $this->config->get('main_facebook_like_theme');
		$send = $this->config->get('main_facebook_like_send');

		// Get the permalink to the blog post.
		$url = $this->post->getExternalPermalink();

		// Determines if we should track with analytics
		$tracking = $this->config->get('main_facebook_analytics');

		// Generate a placeholder
		$placeholder = $this->getPlaceholderId();

		$theme = EB::themes();

		$theme->set('size', $size);
		$theme->set('placeholder', $placeholder);
		$theme->set('url', $url);
		$theme->set('locale', $locale);
		$theme->set('verb', $verb);
		$theme->set('fbTheme', $fbTheme);
		$theme->set('send', $send);
		$theme->set('tracking', $tracking);

		$output = $theme->output('site/socialbuttons/external/facebook');

		return $output;
	}

	/**
	 * Determines if Facebook button is enabled
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isEnabled()
	{
		return $this->config->get('main_facebook_like');
	}
}
