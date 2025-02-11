<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogCrawlerVideo
{
	/**
	 * Ruleset to process document title
	 *
	 * @params	string $contents	The html contents that needs to be parsed.
	 * @return	boolean				True on success false otherwise.	 	 	 
	 */	 	
	public function process($parser, &$contents, $uri, $absoluteUrl, $originalUrl, &$data)
	{
		// Find video objects
		$videos = array();

		// Get the list of video_src items on the page
		$items = $parser->find('link[rel=video_src]');

		if (!$items || !isset($items[0])) {
			return $videos;
		}

		// Get the first video match
		$video = new stdClass();
		$video->source = $items[0]->getAttribute('href');

		// Get the video width
		$width = $parser->find('meta[name=video_width]');

		if (isset($width[0])) {
			$video->width = $width[0]->getAttribute('content');
		}

		// Get the video width
		$height = $parser->find('meta[name=video_height]');

		if (isset($height[0])) {
			$video->height = $height[0]->getAttribute('content');
		}

		return $video;
	}
}
