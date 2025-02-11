<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogClientGoogleHelper extends EasyBlog
{

	/**
	 * Given the mime, determine if the mime is an image
	 *
	 * @since 	5.3.3
	 * @access 	public
	 *
	 **/
	public function isImage($mime)
	{
		$knownMimes = array(
			'image/jpeg',
			'image/jpg',
			'image/png'
		);

		if (in_array($mime, $knownMimes)) {
			return true;
		}

		return false;
	}


	/**
	 * Performs image download from the RSS
	 *
	 * @since   5.0
	 * @access  public
	 */
	private function downloadImage($url, $post)
	{
		// Store the image on a temporary location
		$temporaryPath = JPATH_ROOT . '/tmp/' . md5($url);

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// Save the image to a temporary location
		JFile::write($temporaryPath, $contents);

		// Replace the extension with the correct one. #2248
		$ext = EB::image()->getExtension($temporaryPath);

		$now = time();
		$name = $post->id . '_' . $now;
		$name = JFile::stripExt($name) . '.' . $ext;

		// Prepare the image data
		$file = getimagesize($temporaryPath);
		$file['name'] = $name;
		$file['tmp_name'] = $temporaryPath;
		$file['type'] = $file['mime'];

		$media = EB::mediamanager();
		$uri = 'post:' . $post->id;

		$adapter = $media->getAdapter($uri);
		$result = $adapter->upload($file, $uri);

		// Delete the temporary file
		JFile::delete($temporaryPath);

		if (!isset($result->type)) {
			return false;
		}

		// $path = $this->config->get('main_image_path');
		// $path = rtrim($path, '/');

		// $relative = $path . '/' . $post->id;

		// $relativeImagePath = $relative . '/' . $result->title;

		// $result->html = '<img src="'.$relativeImagePath.'" />';

		return $result;
	}


	/**
	 * Method to process the content based on the configuration
	 *
	 * @since	6.0
	 * @access	private
	 */
	public function processContent($post, $html, $styling)
	{
		$contents = $this->processInline($html);

		if ($styling == 'plain') {
			$contents = $this->processPlain($html);
		}

		// to download images for this draft post.
		$contents = $this->processImages($post, $contents);

		return $contents;
	}

	/**
	 * Method to download the external image from google and save in local
	 *
	 * @since	6.0
	 * @access	private
	 */
	private function processImages($post, $html)
	{
		$pattern = '/<img[^>]+>/ims';

		$content = $html;

		preg_match_all($pattern, $content, $matches);

		if ($matches) {
			$images = $matches[0];
			foreach ($images as $img) {
				$srcPattern = '/src="([^"]*)"/i';

				preg_match($srcPattern, $img, $srcMatches);

				if ($srcMatches && isset($srcMatches[1]) && $srcMatches[1]) {

					// lets try to download the images and save into local
					$src = $srcMatches[1];

					$localImg = $this->downloadImage($src, $post);

					if ($localImg !== false) {
						$newSrc = $localImg->url;

						// replace with new image src
						$newImg = str_replace('src="' . $src . '"', 'src="' . $newSrc . '"', $img);

						// replace with new img tag

						$content = str_replace($img, $newImg, $content);
					}
				}
			}
		}

		return $content;
	}


	/**
	 * Method to process the content based on the inline styling
	 *
	 * @since	6.0
	 * @access	private
	 */
	private function processInline($html)
	{
		$bodyContent = '';

		// let get the contents inside the body tag
		$pattern = '/\<body[^\>]*>(.*?)\<\/body\>/i';
		preg_match($pattern, $html, $matches);

		if ($matches) {
			$bodyContent = isset($matches[1]) ? $matches[1] : '';
		}

		if (!$bodyContent) {
			// TODO: to load the body content using dom document libary.
		}

		return $bodyContent;
	}

	/**
	 * Method to process the content based on the plain styling
	 *
	 * @since	6.0
	 * @access	private
	 */
	private function processPlain($htmlContent)
	{
		$contents = '';

		if (function_exists('mb_detect_encoding')) {
			if (mb_detect_encoding($htmlContent, 'UTF-8', true) != 'UTF-8') {

				if (function_exists('mb_convert_encoding')) {
					// convert cyrillic (window) to utf-8 #874
					$htmlContent = mb_convert_encoding($htmlContent, "utf-8", "windows-1251");
				}
			}
		}

		// force utf-8
		$htmlContent = EB::string()->forceUTF8($htmlContent);

		// Load up the readability lib
		$readability = EB::readability($htmlContent);

		$readability->debug = false;
		$readability->convertLinksToFootnotes = false;

		$result = $readability->init();

		if ($result) {
			$output = $readability->getContent()->innerHTML;

			// Tidy up the contents
			$output = EB::string()->tidyHTMLContent($output);

			$contents = $output;

		}

		return $contents;
	}

}
