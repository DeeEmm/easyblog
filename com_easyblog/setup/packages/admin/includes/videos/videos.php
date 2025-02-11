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

class EasyBlogVideos extends EasyBlog
{
	private $providers = [
		'youtube.com' => 'youtube',
		'youtu.be' => 'youtube',
		'vimeo.com' => 'vimeo',
		'yahoo.com' => 'yahoo',
		'metacafe.com' => 'metacafe',
		'google.com' => 'google',
		'liveleak.com' => 'liveleak',
		'revver.com' => 'revver',
		'dailymotion.com' => 'dailymotion',
		'nicovideo.jp' => 'nicovideo',
		'blip.tv' => 'blip',
		'soundcloud.com' => 'soundcloud'
	];

	/**
	 * Removes any video codes from the content
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function strip($content)
	{
		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content = str_ireplace('&quot;', '"', $content);

		$pattern = [
			'/\{video:.*?\}/',
			'/\{"video":.*?\}/',
			'/\[embed=.*?\].*?\[\/embed\]/'
		];

		$replace = ['',''];


		return preg_replace($pattern, $replace, $content);
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
	 * @since	4.0
	 * @access	public
	 */
	public function format(EasyBlogPost &$post, $plain = false, $useRelative = false)
	{
		$post->intro = $this->formatContent($post->intro, $plain, $useRelative);
		$post->content = $this->formatContent($post->content, $plain, $useRelative);
	}

	/**
	 * Formats the content with the appropriate video codes
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function formatContent($content, $plain = false, $useRelative = false)
	{
		$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if ($matches) {

			$allowed = ['video', 'videolink'];

			foreach ($matches as $match) {

				list($search, $type, $result) = $match;

				if (!in_array($type, $allowed)) {
					continue;
				}

				if ($type == 'video') {
					$content = $this->processUploadedVideos($content, $plain, $search, $result, $useRelative);
				}

				if ($type == 'videolink') {
					$content = $this->processExternalVideos($content, $plain, $search, $result);
				}
			}
		}

		return $content;
	}

	public function processAMP($content, $plain = false, $useRelative = false)
	{
		$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
		$embedded = false;

		if (!$matches) {
			return $content;
		}

		$allowed = ['video', 'videolink'];

		foreach ($matches as $match) {

			list($search, $type, $result) = $match;

			if (!in_array($type, $allowed)) {
				continue;
			}

			if ($type == 'videolink') {
				$content = $this->processExternalVideos($content, $plain, $search, $result, true);
			}
		}

		return $content;
	}

	/**
	 * Processes video codes and converts it accordingly.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string	The contents to search for
	 * @param 	bool	Determines if the caller only wants the video url
	 * @return
	 */
	public function processVideos($content, $isPlain = false)
	{
		return $this->formatContent($content, $isPlain);
	}

	public function processInstantVideos($content)
	{
		$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if ($matches) {

			$allowed = ['video', 'videolink'];

			foreach ($matches as $match) {

				list($search, $type, $result) = $match;

				if (!in_array($type, $allowed)) {
					continue;
				}

				if ($type == 'video') {
					// Facebook already has their own player. So, we just need to return the correct dom.
					if ($result) {
						$data = json_decode($result);

						// EB 5 Legacy editor
						if (isset($data->uri)) {
							$mm = EB::mediamanager();
							$file = $mm->getFile($data->uri);
							$url = $file->url;
						} else {
							// EB 3.9 or lower
							$file = trim($data->file, '/\\');

							$place = $data->place;
							if ($place == 'shared') {
								$url = rtrim(JURI::root(), '/') . '/' . trim(str_ireplace('\\', '/', $this->config->get('main_shared_path')), '/\\') . '/' . $file;
							} else {
								$place = explode(':', $place);
								$url = rtrim(JURI::root(), '/') . '/' . trim($this->config->get('main_image_path'), '/\\') . '/' . $place[1] . '/' . $file;
							}
						}
					}

					$videoType = 'video/x-flv';
					if (strpos($url, '.mp4') !== false) {
						$videoType = 'video/mp4';
					}

					$player = '<figure><video><source src="' . $url .'" type="' . $videoType . '" /></video></figure>';
				}

				if ($type == 'videolink') {

					if (!$result) {
						return $content;
					}

					$data = json_decode($result);

					if ($data == null) {
						$data = json_decode(strip_tags($result));
					}

					$search = !empty($search) ? $search : $result;

					$player = '<figure class="op-interactive"><iframe width="' . $data->width . '" height="' . $data->height . '" src="' . $data->video . '"></iframe></figure>';
				}

				$content = str_ireplace($search, $player, $content);
			}
		}

		return $content;
	}

	/**
	 * Search and replace videos that are uploaded to the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function processUploadedVideos($content, $isPlain = false, $findText = '', $result = '', $useRelative = null)
	{
		$cfg = EB::config();

		// Since 3.0 uses a different video format, we need to do some tests here.
		if ($result) {

			$data = json_decode($result);

			// New EasyBlog 5 legacy codes
			if (isset($data->uri)) {
				$mm = EB::mediamanager();
				$file = $mm->getFile($data->uri);

				$url = $file->url;
			} else {

				// This is the video codes used on EB3.9 or older
				$file = trim($data->file, '/\\');

				$place = $data->place;

				if ($place == 'shared') {
					$url = rtrim(JURI::root(), '/') . '/' . trim(str_ireplace('\\', '/', $cfg->get('main_shared_path')), '/\\') . '/' . $file;
				} else {
					$place = explode(':', $place);
					$url = rtrim(JURI::root(), '/') . '/' . trim($cfg->get('main_image_path'), '/\\') . '/' . $place[1] . '/' . $file;
				}
			}

			$options = [];
			$options['width'] = $data->width;
			$options['height'] = $data->height;
			$options['autostart'] = isset($data->autostart) ? $data->autostart : false;

			$player = EB::media()->renderVideoPlayer($url, $options, $useRelative);
			$content = str_ireplace($findText, $player, $content);

			return $content;
		}

		return $content;
	}

	/**
	 * Processes videos that are embedded on the post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function processExternalVideos($content, $isPlain = false, $findText = '', $jsonString = '', $amp = false)
	{
		if (!$jsonString) {
			return $content;
		}

		$video = json_decode($jsonString);

		// The json string might contain html tag if the decode return null.
		if ($video == null) {
			$video = json_decode(strip_tags($jsonString));
		}

		$search = !empty($findText) ? $findText : $jsonString;

		if ($isPlain) {
			$html = ' ' . $video->video . ' ';
			$content = EBString::str_ireplace($search, $html, $content);

			return $content;
		}


		$maxWidth = (int) $this->config->get('max_video_width');
		$maxHeight = (int) $this->config->get('max_video_height');

		$video->width = isset($video->width) && $video->width ? $video->width : $maxWidth;
		$video->height = isset($video->height) && $video->height ? $video->height : $maxHeight;

		// Ensure that the video dimensions doesn't exceed the maximum dimensions
		$video->width = $video->width > $maxWidth ? $maxWidth : $video->width;
		$video->height = $video->height > $maxHeight ? $maxHeight : $video->height;

		// Ensure that the video link is clean.
		$video->video = strip_tags($video->video);

		$output = $this->getProviderEmbedCodes($video->video, $video->width, $video->height, $amp);

		if ($output !== false) {
			$content = EBString::str_ireplace($search, $output, $content);
		}

		if (isset($video->nocookie) && $video->nocookie) {
			$content = str_replace('youtube.com/', 'youtube-nocookie.com/', $content);
		}

		return $content;
	}

	/**
	 * Retrieves a list of videos in an array
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getItems($content, $returnObject = false, $useRelative = false)
	{
		$videos = [];
		$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';

		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if (!$matches) {
			return $videos;
		}

		foreach ($matches as $match) {
			$type = $match[1];
			$search = $match[0];

			if ($type != 'videolink' && $type != 'video') {
				continue;
			}


			$video = $match[2];

			// Remove the video from the content since we have already extracted it.
			$content = EBString::str_ireplace($search, '', $content);

			$videoHtml = '';
			$videoUrl = '';


			if ($type == 'videolink') {
				$videoObj = json_decode($video);
				if ($videoObj) {
					$videoUrl = $videoObj->video;
					$videoHtml = $this->getProviderEmbedCodes($videoObj->video, $videoObj->width, $videoObj->height);
				}

			}

			if ($type == 'video') {
				$data = $this->processVideoLink($video, $useRelative);
				$videoUrl = $data->url;
				$videoHtml = $data->output;
			}

			if ($returnObject) {
				$obj = new StdClass();

				$obj->url = $videoUrl;
				$obj->html = $videoHtml;

				$videos[] = $obj;

			} else {
				$videos[] = $videoHtml;
			}
		}

		return $videos;
	}

	/**
	 * Given a set of content, try to match and return the list of videos that are found in the content.
	 * This is only applicable for videos that are supported by the library.
	 *
	 * @author	imarklee
	 * @access	public
	 * @param	string	$content	The html contents that we should look for.
	 * @return	Array				An array of videos that are found.
	 */
	public function getVideoObjects($content)
	{
		// This will eventually contain all the video objects
		$result = [];

		// Store temporary content for legacy fixes.
		$tmpContent	= $content;

		// @since 3.5
		// New pattern uses [embed=videolink] to process embedded videos from external URLs.
		//
		// videolink - External video URLs like Youtube, Google videos
		// video - Internal video URLs that are uploaded via media manager
		$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if (!empty($matches)) {

			foreach ($matches as $match) {

				list($search, $type, $json) = $match;

				// Decode the json string
				$data = json_decode($json);

				// Let's remove it from the temporary content.
				$tmpContent	= str_ireplace($search, '', $tmpContent);

				if ($type == 'videolink') {
					$data->html = $this->getProviderEmbedCodes($data->video, $data->width, $data->height);
				}

				if ($data->nocookie) {
					$data->html = str_replace('youtube.com/', 'youtube-nocookie.com/', $data->html);
				}

				// Now, let's add the data object back to the result list.
				$result[] = $data;
			}
		}

		return $result;
	}

	/**
	 * Detects the domain provider of the embedded video link
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getDomain($link)
	{
		$link = strip_tags($link);
		$domain = '';

		// Ensure that the video link contains protocol
		if (stristr($link, 'http://') === false && stristr($link, 'https://') === false) {
			$link = 'http://' . $link;
		}

		// Break down the link information
		$link = parse_url($link);
		$link = explode('.', $link['host']);

		// The parts of the domains are always xxx.com regardless if it's a subdomain or not.
		// E.g: something.youtube.com, xxx.youtube.com and yyy.vimeo.com
		if (count($link) >= 2) {
			$domain = $link[count($link) - 2] . '.' . $link[count($link) - 1];
		}

		return $domain;
	}

	/**
	 * Retrieve the embed codes from specific video provider
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function processVideoLink($jsonString, $useRelative = false)
	{
		$cfg = EB::config();

		// Since 3.0 uses a different video format, we need to do some tests here.
		if ($jsonString) {

			$data = json_decode($jsonString);

			$file = trim($data->uri, '/\\');
			$width = isset($data->width) ? $data->width : 0;
			$height = $data->height;
			$autostart = isset($data->autostart) ? $data->autostart : false;

			// New EasyBlog 5 legacy codes
			if (isset($data->uri)) {
				$mm = EB::mediamanager();
				$file = $mm->getFile($data->uri);

				$url = $file->url;
			} else {

				// This is the video codes used on EB3.9 or older
				$file = trim($data->file, '/\\');

				$place = $data->place;

				if ($place == 'shared') {
					$url = rtrim(JURI::root(), '/') . '/' . trim(str_ireplace('\\', '/', $cfg->get('main_shared_path')), '/\\') . '/' . $file;
				} else {
					$place = explode(':', $place);
					$url = rtrim(JURI::root(), '/') . '/' . trim($cfg->get('main_image_path'), '/\\') . '/' . $place[1] . '/' . $file;
				}
			}

			$options = [];
			$options['width'] = $width;
			$options['height'] = $height;
			$options['autostart'] = $autostart;

			$output = EB::media()->renderVideoPlayer($url, $options, $useRelative);

			// now we need to check if url should be return as relative or not.
			if ($useRelative) {
				$url = EB::string()->abs2rel($url);
			}

			$return = new stdClass();
			$return->url = $url;
			$return->output = $output;

			return $return;
		}

	}

	/**
	 * Processes an embedded video hyperlink with the appropriate embed codes.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string	The link to the video item
	 * @param	int		The width of the video
	 * @param	int		The height of the video
	 * @return
	 */
	public function getProviderEmbedCodes($link, $width = null, $height = null, $amp = false)
	{
		$domain = $this->getDomain($link);

		// If we can't find the video, skip this altogether.
		if (!array_key_exists($domain, $this->providers)) {
			return false;
		}

		$provider = strtolower($this->providers[$domain]);
		$path = dirname(__FILE__) . '/adapters/' . $provider . '.php';

		// Ensure that the file really exists. Do not allow authors to break the flow
		if (!JFile::exists($path)) {
			return false;
		}

		require_once($path);

		$class = 'EasyBlogVideo' . ucfirst($provider);

		if (!class_exists($class)) {
			return false;
		}

		$provider = new $class();
		$output = $provider->getEmbedHTML($link, $width, $height, $amp);

		return $output;
	}
}
