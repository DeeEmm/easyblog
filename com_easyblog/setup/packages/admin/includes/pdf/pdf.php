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

class EasyBlogPdf extends EasyBlog
{
	/**
	 * Removes any video codes from the content
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function strip($content)
	{
		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content = str_ireplace('&quot;', '"', $content);
		$pattern = array('/\[pdf\].*?\[\/pdf\]/');

		return preg_replace($pattern, '', $content);
	}

	/**
	 * Used in conjunction with EB::formatter()
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function stripCodes(EasyBlogPost &$post)
	{
		if (isset($post->text)) {
			$post->text = $this->strip($post->text);
		}

		$post->intro = $this->strip($post->intro);
		$post->content = $this->strip($post->content);

	}

	/**
	 * Used in conjunction with EB::formatter()
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function format(EasyBlogPost &$post, $plain = false, $useRelative = false)
	{
		$post->intro = $this->formatContent($post->intro, $plain, $useRelative);
		$post->content = $this->formatContent($post->content, $plain, $useRelative);
	}

	/**
	 * Retrieves a list of matched pdf patterns
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getMatches($content)
	{
		$pattern = '/\[pdf\](.*)\[\/pdf\]/uiU';
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		return $matches;
	}

	/**
	 * Formats the content with the appropriate pdf embed codes
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function formatContent($content, $plain = false, $useRelative = false)
	{
		$matches = $this->getMatches($content);

		if (!$matches) {
			return $content;
		}

		foreach ($matches as $match) {
			list($search, $result) = $match;

			$data = json_decode($result);

			if (!isset($data->uri)) {
				continue;
			}

			$media = EB::mediamanager();
			$file = $media->getFile($data->uri);
			$url = $file->url;

			$options = array();
			$options['height'] = $data->height;

			$html = EB::media()->renderPdfPreviewer($url, $options, $useRelative);
			$content = str_ireplace($search, $html, $content);
		}

		return $content;
	}

	/**
	 * Retrieves a list of pdf files in an array
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItems($content, $returnObject = false, $useRelative = false)
	{
		$matches = $this->getMatches($content);
		$pdf = array();

		if (!$matches) {
			return $pdf;
		}

		foreach ($matches as $match) {
			$search = $match[0];
			$obj = json_decode($match[1]);

			// Remove the video from the content since we have already extracted it.
			$content = EBString::str_ireplace($search, '', $content);

			$media = EB::mediamanager();
			$file = $media->getFile($obj->uri);
			$url = $file->url;

			// Determines if we should be using relative urls
			if ($useRelative) {
				$url = EB::string()->abs2rel($url);
			}

			$html = EB::media()->renderPdfPreviewer($url, array('height' => $obj->height), $useRelative);

			if ($returnObject) {
				$obj = new StdClass();
				$obj->url = $url;
				$obj->html = $html;

				$pdf[] = $obj;

			} else {
				$pdf[] = $html;
			}
		}

		return $pdf;
	}
}
