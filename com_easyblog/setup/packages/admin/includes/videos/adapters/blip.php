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

class EasyBlogVideoBlip
{
	private function getCode($url)
	{
		$url = explode('-', $url);

		// We know the last segment is always the "ID"
		if (isset($url[ count($url) - 1 ])) {
			$code = $url[count($url) - 1];
			return $code;
		}

		return false;
	}
	
	public function getEmbedHTML($url, $width, $height, $amp = false)
	{
		$code = $this->getCode($url);
		
		if ($code) {

			$html 	= '<objec width="' . $width . '" height="' . $height . '">'
					. '<param value="http://a.blip.tv/scripts/flash/stratos.swf#file=http://blip.tv/rss/flash/' . $code . '&autostart=false&showinfo=false&captionson=true&removebrandlink=true" name="movie"></param>'
					. '<param value="always" name="allowscriptaccess"></param>'
					. '<param value="true" name="allowfullscreen"></param>'
					. '<param value="best" name="quality"></param>'
					. '<param value="transparent" name="wmode">'
					. '<embed width="' . $width . '" height="' . $height . '" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="always" allowfullscreen="true" src="http://a.blip.tv/scripts/flash/stratos.swf#file=http://blip.tv/rss/flash/' . $code . '&autostart=false&showinfo=false&captionson=true&removebrandlink=true" id="video_player_embed">'
					. '</object>';

			return $html;
		}
		
		return false;
	}
}