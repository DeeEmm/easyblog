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

class EasyBlogVideoNicoVideo
{
	private function getCode($url)
	{
		preg_match('/nicovideo.jp\/watch\/(.*)/is', $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}
		
		return false;
	}
	
	public function getEmbedHTML($url, $width, $height, $amp = false)
	{
		$code = $this->getCode($url);
		
		if ($code) {
			return '<script src="http://ext.nicovideo.jp/thumb_watch/' . $code . '?w=' . $width . '&h=' . $height . '&n=1" type="text/javascript"></script><noscript>Javascript is required to load the player</noscript>';
		}
		
		return false;
	}
}