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

require_once(__DIR__ . '/abstract.php');

class EasyBlogVideoYoutube extends EasyBlogVideoProvider
{
	public function getCode($url)
	{
		/* match http://www.youtube.com/watch?v=TB4loah_sXw&feature=fvst */
		$pattern = '/youtube.com\/watch\?v=(.*)(?=&feature)(?=&)/is';
		preg_match($pattern, $url, $matches);

		if (!empty($matches)) {
			$code = explode('&', $matches[1]);

			if (count($code) > 1) {
				return $code[0];
			}

			return $matches[1];
		}

		/* New format: http://www.youtube.com/user/ToughMudder?v=w1PhUWGz_xw */
		$pattern = '/youtube.com\/user\/(.*)\?v=(.*)/is';
		preg_match($pattern, $url, $matches);

		if (!empty($matches)) {

			// Ensure that the code doesn't contain any &
			$code = explode('&', $matches[2]);

			if (count($code) > 1) {
				return $code[0];
			}

			return $matches[1];
		}

		/* match http://www.youtube.com/watch?v=sr1eb3ngYko */
		$pattern = '/youtube.com\/watch\?v=(.*)/is';

		preg_match($pattern, $url, $matches);

		if (!empty($matches)) {

			// Ensure that the code doesn't contain any &
			$code = explode('&', $matches[1]);

			if (count($code) > 1) {
				return $code[0];
			}

			return $matches[1];
		}

		// http://www.youtube.com/watch?feature=player_embedded&v=XUaTQKeDw4E
		$pattern = '/youtube.com\/watch\?.*v=(.*)(?=&)/is';
		preg_match($pattern, $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}

		// youtu.be
		$pattern = '/youtu.be\/(.*)/is';

		preg_match($pattern, $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}

		return false;
	}

	/**
	 * Retrieves the embedded html code for youtube
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getEmbedHTML($url, $width = null, $height = null, $amp = false)
	{
		// Some content plugins tries to replace & with &amp; in the content. We need to ensure that the URL doesn't contain &amp;
		$url = str_ireplace('&amp;', '&', $url);

		// Sometimes the semicolon in the &amp; is being replace with %3b , hence the above algorithm will be skipped. We need to fix this as well.
		$url = str_ireplace('&amp%3b', '&', $url);

		$code = $this->getCode($url);
		$width = $width ? ' width="' . $width . '"' : ' width="100%"';
		$height = $height ? ' height="' . $height . '"' : '';

		$normalizedURL = $this->normalizeUrl($url);

		$iframeHtml = '<iframe title="YouTube video player" ' . $width . $height . ' src="' . $normalizedURL . '" frameborder="0" allowfullscreen></iframe>';

		if ($code) {

			if ($amp) {
				// Since amp doesn't allow any other value than the code itself,
				// we need to retrieve the code again. https://youtu.be/72ClsmFzWWo?t=12m18s
				if (strpos($url, 'youtu.be') !== false) {
					$pattern = '/youtu.be\/(.*)(?=\?)/is';

					preg_match($pattern, $url, $matches);

					if (!empty($matches)) {
						$code = $matches[1];
					}
				}

				return '<amp-youtube data-videoid="' . $code . '" layout="responsive" width="480" height="270"></amp-youtube>';

			}

			$normalizedURL = $this->normalizeUrl($code);

			$iframeHtml = '<iframe ' . $width . $height . ' src="https://www.youtube.com/embed/' . $normalizedURL . '" frameborder="0" allowfullscreen></iframe>';
		}

		return '<div class="legacy-video-container">' . $iframeHtml . '</div>';
	}

	/**
	 * Normalize the youtube URL whether need to contain ? or &
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeUrl($url)
	{
		$rel = '';

		if (strpos($url, '&rel=0') !== false) {
			$rel = '&rel=0';
			$url = str_ireplace($rel, '', $url);
		}

		if (strpos($url, '?rel=0') !== false) {
			$rel = '?rel=0';
			$url = str_ireplace($rel, '', $url);
		}

		$wmode = $rel ? $rel . '&wmode=transparent' : '?wmode=transparent';
		$url = $url . $wmode;

		return $url;
	}
}
