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

class EasyBlogFeedAdapterMapper extends EasyBlog
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
	 * Maps the feed item with a post item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function map(EasyBlogPost &$post, &$item, EasyBlogTableFeed &$feed, &$params)
	{
		// Set the frontpage status
		$post->frontpage = $feed->item_frontpage;

		// Set the category
		$post->category_id = $feed->item_category;

		// Cheap fix
		$post->categories = array($post->category_id);

		// Set the author
		$post->created_by = $feed->item_creator;

		// Determines if comments is allowed
		$post->allowcomment = $this->config->get('main_comment', true);

		// Determines if subscription is allowed
		$post->subscription = $this->config->get('main_subscription', true);

		// Determines if we should notify subscribers
		$post->send_notification_emails = $params->get('notify', true);

		// The blog post should always be site wide
		$post->source_id = 0;
		$post->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;

		// If item_team is not empty, change the source_type
		if (!empty($feed->item_team)) {
			$post->source_id = $feed->item_team;
			$post->source_type = EASYBLOG_POST_SOURCE_TEAM;
		}

		// Set the blog post's language
		$post->language = $feed->language;

		// Set any copyright text
		$post->copyrights = $params->get('copyrights', '');

		// set robots  instructions
		$post->robots = $params->get('robots', '');

		// current datetime
		$dateTime = EB::date()->toSql();

		// get the raw publication date from feed item
		$pubDate = $item->get_date('');
		if ($pubDate) {
			$dateTime = EB::date($pubDate)->toSql();
		}

		// Set the creation date to the current date
		$post->created = $dateTime;
		$post->modified = $dateTime;
		$post->publish_up = $dateTime;

		// Determines if the blog should be new
		// since this is new item import, we always set this as new.
		$post->isnew = true;

		// Determines if the post published option is pending
		if ($feed->item_published == EASYBLOG_POST_PENDING) {
			$post->published = EASYBLOG_POST_PENDING;
		} else {
			// Set the publishing status
			$post->published = $feed->item_published != EASYBLOG_POST_UNPUBLISHED ? EASYBLOG_POST_PUBLISHED: EASYBLOG_POST_UNPUBLISHED;
		}

		// Bind the title
		$post->title = @$item->get_title();

		$post->title = @html_entity_decode($post->title);

		// If the title is empty, we need to intelligently get
		if (!$post->title) {
			$post->title = $this->getTitleFromLink();
		}

		// Ensure that there are no html entities
		$post->title = EB::string()->unhtmlentities($post->title);
	}

	/**
	 * Maps the content
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function mapContent(EasyBlogPost &$post, &$item, EasyBlogTableFeed &$feed, &$params)
	{
		// Initial content
		$contents = '';

		// Try to fetch the contents remotely
		if ($feed->item_get_fulltext) {
			$url = urldecode(@$item->get_link());
			$url = str_ireplace('&amp;', '&', $url);

			$tmp = $this->getFullContent($url, $item);

			// Clean up fetched contents by ensuring that there's no weird text before the html declaration
			$pattern = '/(.*?)<html/is';
			$replace = '<html';
			$tmp = preg_replace($pattern, $replace, $tmp, 1);

			if (!empty($tmp)) {

				if (function_exists('mb_detect_encoding')) {
					if (mb_detect_encoding($tmp, 'UTF-8', true) != 'UTF-8') {

						if (function_exists('mb_convert_encoding')) {
							// convert cyrillic (window) to utf-8 #874
							$tmp = mb_convert_encoding($tmp, "utf-8", "windows-1251");
						}
					}
				}

				// force utf-8
				$tmp = EB::string()->forceUTF8($tmp);

				// Load up the readability lib
				$readability = EB::readability($tmp);

				$readability->debug = false;
				$readability->convertLinksToFootnotes = false;

				$result = $readability->init();

				if ($result) {
					$output = $readability->getContent()->innerHTML;

					// Tidy up the contents
					$output = $this->tidyContent($output);

					$uri = JURI::getInstance();
					$scheme = $uri->toString(array('scheme'));
					$scheme = str_replace('://', ':', $scheme);

					// replace the image source to proper format so that feed reader can view the image correctly.
					$output = str_replace('src="//', 'src="' . $scheme . '//', $output);
					$output = str_replace('href="//', 'href="' . $scheme . '//', $output);

					if (stristr(html_entity_decode($output), '<!DOCTYPE html') === false) {
						$contents = $output;
						$contents = $this->convertRelativeToAbsoluteLinks($contents, @$item->get_link());
					}

					// find all the iframe HTML content
					preg_match_all('/(?:<iframe[^>]*>)/i', $contents, $matches);

					if (!empty($matches[0])) {

						// Remove all the iframe closing tag </iframe>
						$contents = str_replace('</iframe>', '', $contents);

						foreach ($matches[0] as $iframeHTML) {

							// Replace back the iframe closing tag
							$contents = str_replace($iframeHTML, $iframeHTML . '</iframe>', $contents);
						}
					}
				}
			}
		}

		// Get the content of the item
		if (!$contents) {
			$contents = @html_entity_decode($item->get_content());
		}

		// Default allowed html codes
		$allowed = '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>';

		// Remove disallowed tags
		$contents = strip_tags($contents, $params->get('allowed', $allowed));

		// Append original source link into article if necessary
		if ($params->get( 'sourceLinks')) {
			$contents .= '<div><a href="' . @$item->get_link() . '" target="_blank">' . JText::_( 'COM_EASYBLOG_FEEDS_ORIGINAL_LINK' ) . '</a></div>';
		}

		// Bind the author
		if ($feed->author) {
			$author = @$item->get_author();

			if ($author) {
				$name = @$author->get_name();
				$email = @$author->get_email();

				if ($name) {
					$contents .= '<div>' . JText::sprintf('COM_EASYBLOG_FEEDS_ORIGINAL_AUTHOR', $name) . '</div>';
				} else if ($email) {

					$segments = explode(' ', $email);

					if (isset($segments[1])) {
						$name = $segments[1];
						$name = str_replace(array('(', ')'), '', $name);
						$contents .= '<div>' . JText::sprintf('COM_EASYBLOG_FEEDS_ORIGINAL_AUTHOR', $name) . '</div>';
					}
				}
			}
		}

		// Try to get the media file if exist

		$enclosure = @$item->get_enclosure();
		$imageUrl = '';

		if ($enclosure) {
			$imageUrl = $enclosure->get_thumbnail();

			// Some uses link
			if (!$imageUrl && $this->isImage($enclosure->get_type()) && $enclosure->link) {

				$imageUrl = $enclosure->link;
				// $imageUrl = JString::str_ireplace(array('?', '#'), '', $enclosure->link);
			}
		}

		if ($imageUrl) {

			// Download the image
			$image = $this->downloadImage($imageUrl, $post);

			if ($image) {

				// Try to import the post as cover
				if ($params->get('cover', false)) {
					$post->image = $image->uri;
				} else {
					$contents .= $image->html;
				}
			}
		}

		if ($feed->item_content == 'intro') {
			$post->intro = $contents;
		} else {
			$post->content = $contents;
		}

		// The doctype for imported post should be legacy because there are no blocks here.
		$post->doctype = 'legacy';
	}

	/**
	 * Performs image download from the RSS
	 *
	 * @since   5.0
	 * @access  public
	 */
	private function downloadImage($url, $post, $html = true)
	{
		// Decode any html special characters to ensure the link integrity. #2981
		$url = htmlspecialchars_decode($url);

		// Store the image on a temporary location
		$temporaryPath = JPATH_ROOT . '/tmp/' . md5($url);

		// Read the external image file
		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// Save the image to a temporary location
		JFile::write($temporaryPath, $contents);

		// Prepare the image data
		$file = getimagesize($temporaryPath);

		// From here we know that the file is invalid. #2981
		if (!$file) {
			return false;
		}

		$name = basename($url);

		// Replace the extension with the correct one. #2248
		$ext = EB::image()->getExtension($temporaryPath);
		$name = JFile::stripExt($name) . '.' . $ext;

		$file['name'] = $name;
		$file['tmp_name'] = $temporaryPath;
		$file['type'] = $file['mime'];

		$media = EB::mediamanager();
		$uri = 'user:' . $post->created_by;

		$adapter = $media->getAdapter($uri);
		$result = $adapter->upload($file, $uri);

		// Delete the temporary file
		JFile::delete($temporaryPath);

		if (!isset($result->type)) {
			return false;
		}

		$path = $this->config->get('main_image_path');
		$path = rtrim($path, '/');

		$relative = $path . '/' . $post->created_by;

		$relativeImagePath = $relative . '/' . $result->title;

		$result->html = '<img src="'.$relativeImagePath.'" />';

		return $result;
	}

	/**
	 * Convert relative links to absolute links
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function convertRelativeToAbsoluteLinks($content, $absPath)
	{
		$dom = new DOMDocument();
		@$dom->loadHTML($content);

		// anchor links
		$links = $dom->getElementsByTagName('a');
		foreach($links as $link)
		{
			$oriUrlLink 	= $link->getAttribute('href');
			$urlLink    	= EB::helper('string')->encodeURL( $oriUrlLink );
			$urlLink    	= EB::helper('string')->rel2abs( $urlLink, $absPath );
			$link->setAttribute('href', $urlLink);

			$content    = str_replace( 'href="' . $oriUrlLink .'"', 'href="' . $urlLink .'"', $content );
		}


		// image src
		$imgs = $dom->getElementsByTagName('img');
		foreach($imgs as $img)
		{
			$oriImgLink = $img->getAttribute('src');
			$imgLink    = EB::helper('string')->encodeURL( $oriImgLink );
			$imgLink    = EB::helper('string')->rel2abs( $imgLink, $absPath );
			$content    = str_replace( 'src="' . $oriImgLink .'"', 'src="' . $imgLink .'"', $content );
		}

		return $content;
	}

	/**
	 * Tidy up the html contents
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function tidyContent($html)
	{
		return EB::string()->tidyHTMLContent($html);
	}

	/**
	 * Some feed items doesn't have a title. We need to convert the link to the title
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The linke to the item
	 * @return
	 */
	private function getTitleFromLink($link)
	{
		$segments = explode('/', $link);

		// Default title should we not be able to get the link
		$title = JText::sprintf('COM_EASYBLOG_FEEDS_GENERIC_TITLE', EB::date()->format(JText::_('DATE_FORMAT_LC3')));

		if (count($segments) > 1) {
			$title = $segments[count($segments) - 1];

			// Remove .html from the title
			$title = EBString::str_ireplace('.html', '', $title);

			// Replace - with spaces
			$title = EBString::str_ireplace('-', ' ', $title);

			$title = ucwords($title);
		}

		return $title;
	}

	/**
	 * Perform a connection to the blog post to get full content
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	private function getFullContent(&$url, &$item, $retry = 0)
	{
		$connector = FH::connector($url);
		$tmp = $connector->execute()->getResult();

		// If there is too much redirection means something is not right with the site.
		if ($retry > 2) {
			return $tmp;
		}

		// Check for possible redirection such as google alert. #1515
		$parser	= EB::simplehtml()->str_get_html($tmp);

		$meta = $parser->find('meta[http-equiv=refresh]', 0);

		if ($meta) {
			$redirectUrl = $meta->attr['content'];

			if ($redirectUrl) {
				preg_match('/URL=\'(.*?)\'/', $redirectUrl, $matches);

				if ($matches && $matches[1]) {
					$url = $matches[1];

					return $this->getFullContent($url, $item, $retry++);
				}
			}
		}

		return $tmp;
	}
}
