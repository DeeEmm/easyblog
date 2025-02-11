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

class EasyBlogExternalButtonPinterest extends EasyBlogSocialButton
{
	public $type = 'pinterest';

	/**
	 * Outputs the html code for Twitter button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html()
	{
		if (!$this->isEnabled()) {
			return;
		}

		static $scriptLoaded = false;

		$loadScript = false;

		if (!$scriptLoaded) {
			$loadScript = true;
			$scriptLoaded = true;
		}

		// Get the pinterest button style from the configuration
		$size = $this->getButtonSize();

		$url = EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $this->post->id, false, true);

		// Combine the introtext and the content
		$content = $this->post->intro . $this->post->content;

		// Get the media
		$media = $this->getMedia();

		$contentLength	= 350;

		$text = $this->post->intro . $this->post->content;
		$text = nl2br($text);
		$text = strip_tags( $text );
		$text = trim( preg_replace( '/\s+/', ' ', $text ) );
		$text = ( EBString::strlen( $text ) > $contentLength ) ? EBString::substr( $text, 0, $contentLength) . '...' : $text;

		$title = $this->post->title;

		// Urlencode all the necessary properties.
		$url = urlencode($url);
		$text = urlencode($text);
		$media = urlencode($media);

		$placeholder = $this->getPlaceholderId();

		$theme = EB::themes();
		$theme->set('loadScript', $loadScript);
		$theme->set('size', $size);
		$theme->set('placeholder', $placeholder);
		$theme->set('url', $url);
		$theme->set('title', $title);
		$theme->set('media', $media);
		$theme->set('text', $text);

		$output = $theme->output('site/socialbuttons/external/pinterest');

		return $output;
	}

	public function getMedia()
	{
		// Retrieve image for Pinterest
		$image 	= $this->getImage();

		// Retrieve videos for Pinterest
		$video 	= $this->getVideo();

		// If there's no image or video, skip this altogether because
		// Pinterest requirements requires video or image.
		if (!$image && !$video) {
			return false;
		}

		$media = $image;

		if (!$image) {
			$media = $video;
		}

		return $media;
	}

	/**
	 * Determines if the button is enabled
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isEnabled()
	{
		if (!$this->getMedia()) {
			return false;
		}

		return $this->config->get('main_pinit_button');
	}

	/**
	 * Retrieves the image of the post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getImage()
	{
		// Retrieve image that can be used for pinterest
		$image = $this->post->getImage('thumbnail', false, true);

		if (empty($image)) {

			// Combine the introtext and the content
			$content = $this->post->intro . $this->post->content;

			// Fetch the first image of the blog post
			$image = EB::getFirstImage($content);

			// Test if there's any blog image
			if (isset($this->post->images) && $this->post->images && is_array($this->post->images)) {
				$image 	= $this->post->images[0];
			}
		}

		return $image;
	}
	/**
	 * Retrieves video from blog post content
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getVideo()
	{
		// Retrieve video that can be used for pinterest
		$video = '';
		$videoObj = EB::videos()->getVideoObjects($this->post->content);

		if ($videoObj && !empty($videoObj)) {
			$video = $videoObj[0]->video;
		}

		return $video;
	}
}

