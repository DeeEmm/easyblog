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

class EasyBlogBlockHandlerYoutube extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fab fa-youtube';
	public $element = 'figure';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;
		$meta->properties['textpanel'] = false;

		$template = EB::themes();
		$meta->wrapper = $template->output('site/composer/blocks/handlers/youtube/wrapper');

		return $meta;
	}

	/**
	 * Defines the data structure for this block
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function data()
	{
		$data = new stdClass();

		// The url to the video
		$data->author = '';
		$data->url = '';
		$data->width = '';
		$data->height = '';
		$data->fluid = true;
		$data->embed = '';
		$data->source = '';
		$data->related = true;

		$config = EB::config();

		$data->nocookie = $config->get('main_youtube_nocookie');

		return $data;
	}

	public function getEditableHtml($block)
	{
		return '';
	}


	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function validate($block)
	{
		// if no url specified, return false.
		if (!isset($block->data->url) || !$block->data->url) {
			return false;
		}

		return true;
	}

	/**
	 * Standard method to format the output for displaying purposes
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		// If configured to display text only, nothing should appear at all for this block.
		if ($textOnly) {
			return;
		}

		$uid = uniqid();

		// Need to ensure that we have the "source"
		if (!isset($block->data->source) || !$block->data->source) {
			return;
		}

		$template = EB::themes();
		$template->set('data', $block->data);
		$contents = $template->output('site/blocks/youtube');

		return $contents;
	}

	/**
	 * Normalize the data
	 *
	 * @since   6.0.10
	 * @access  public
	 */
	public function normalizeData($data)
	{
		$embed = EB::normalize($data, 'embed', null);
		$embed_nocookie = EB::normalize($data, 'embed_nocookie', null);
		$embed_original = EB::normalize($data, 'embed_original', null);

		if ($embed) {
			$data->embed = html_entity_decode($data->embed);
		}

		if ($embed_nocookie) {
			$data->embed_nocookie = html_entity_decode($data->embed_nocookie);
		}

		if ($embed_original) {
			$data->embed_original = html_entity_decode($data->embed_original);
		}

		return $data;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		// Need to ensure that we have the "url"
		if (!isset($block->data->url) || !$block->data->url) {
			return;
		}

		$url = $block->data->url;

		$videoId = $this->getVideoId($url);

		if (!$videoId) {
			return;
		}

		$html = '<amp-youtube data-videoid="' . $videoId . '" layout="responsive" width="480" height="270"></amp-youtube>';

		return $html;
	}

	/**
	 * Retrieve the YouTube video ID
	 *
	 * @since   5.4.12
	 * @access  public
	 */
	public function getVideoId($url)
	{
		/* match http://www.youtube.com/watch?v=TB4loah_sXw&feature=fvst */
		preg_match('/youtube.com\/watch\?v=(.*)(?=&)/is', $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}

		/* match http://www.youtube.com/watch?v=sr1eb3ngYko */
		preg_match('/youtube.com\/watch\?v=(.*)/is', $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}

		// exclude the start paramater e.g. https://youtu.be/IR9qmPbJnE8?t=309
		preg_match('/youtu.be\/([^?]*)/is', $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}

		return false;
	}
}
