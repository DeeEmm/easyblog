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

class EasyBlogBlockHandlerFacebook extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fab fa-facebook';
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

		return $meta;
	}

	public function data()
	{
		$data = (object) array();

		return $data;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function validate($block)
	{
		// if no url specified, return false.
		if (!isset($block->data->source) || !$block->data->source) {
			return false;
		}

		return true;
	}

	/**
	 * Standard method to format the output for displaying purposes
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		if ($textOnly) {
			return;
		}

		// If the source isn't set ignore this.
		if (!isset($block->data->source) || !$block->data->source) {
			return;
		}

		// By default, it is a post
		$type = 'post';

		$isVideo = $this->isVideo($block->data->source);

		if ($isVideo) {
			$type = 'video';
		}

		$themes = EB::themes();
		$themes->set('type', $type);
		$themes->set('block', $block);
		$contents = $themes->output('site/blocks/facebook');

		return $contents;
	}

	/**
	 * Determine if the link is video post
	 *
	 * @since   5.4.9
	 * @access  public
	 */
	private function isVideo($url)
	{
		$isVideo = false;
		$pattern = '/^https:\/\/www\.facebook\.com\/(.*?)\/(.*?)\//';

		preg_match($pattern, $url, $matches);

		if (!empty($matches) && $matches[2] == 'videos') {
			$isVideo = true;
		}

		$pattern = '/^https:\/\/www\.facebook\.com\/watch\/(.*?)/';

		preg_match($pattern, $url, $matches);

		if (!empty($matches)) {
			$isVideo = true;
		}

		$pattern = '/^https:\/\/fb\.watch\/(.*?)\//';

		preg_match($pattern, $url, $matches);

		if (!empty($matches)) {
			$isVideo = true;
		}

		return $isVideo;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		$source = $block->data->source;

		// determines if the url is facebook post or Video
		$regexPost = '/^https:\/\/www\.facebook\.com\/(photo(\.php|s)|permalink\.php|media|questions|notes|[^\/]+\/(activity|posts))[\/?].*$/';

		if (preg_match($regexPost, $source, $match)) {

			$html = '<amp-facebook width=486 height=657 layout="responsive" data-href="' . $match[0] . '"></amp-facebook>';

			return $html;
		}

		$regexVideo = '/^https:\/\/www\.facebook\.com\/([^\/?].+\/)?video(s|\.php)[\/?].*$/';

		if (preg_match($regexVideo, $source, $match)) {

			$html = '<amp-facebook width=552 height=574 layout="responsive" data-embed-as="video" data-href="' . $match[0] . '"></amp-facebook>';

			return $html;
		}

		return false;
	}
}
