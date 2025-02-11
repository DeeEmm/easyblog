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

class EasyBlogVideoVimeo
{
	private function getCode($url)
	{
		preg_match('/vimeo.com\/(.*)/is', $url, $matches);

		if (!empty($matches)) {
			// Because of vimeo implemented private videos, the url given will have 2 segments. 
			// Video player will not be able to play this video because of the additional segment.
			// Example of private video: https://vimeo.com/218590790/e24b9c7c64 <-- have 2 segments.
			// Example of normal video: https://vimeo.com/236370796
			$ids = explode('/', $matches[1]);
			return $ids[0];
		}

		return false;
	}

	public function getEmbedHTML($url, $width, $height, $amp = false)
	{
		$code = $this->getCode($url);

		if ($code) {
			return '<div class="legacy-video-container"><iframe src="https://player.vimeo.com/video/' . $code . '" width="' . $width . '" height="' . $height . '" frameborder="0"></iframe></div>';
		}
		
		return false;
	}
}
