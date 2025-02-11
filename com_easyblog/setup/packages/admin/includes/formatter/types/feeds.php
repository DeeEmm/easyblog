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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/facebook/instantArticles.php');

class EasyBlogFormatterFeeds extends EasyBlogFormatterStandard
{
	/**
	 * Format posts for RSS feed consumption
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function execute()
	{
		$feedItems = array();

		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		// Format the items through the list formatting first
		$posts = EB::formatter('list', $this->items);
		$site = $this->jconfig->get('sitename');

		// Get rss type (instant article or normal feed)
		$instant = $this->input->get('instant', false);

		foreach ($posts as $post) {

			$item = new JFeedItem();
			$item->title = $post->title;
			$item->link = $post->getPermalink();

			$author = $post->getAuthor();
			$category = $post->getPrimaryCategory();

			if ($instant) {
				// If the post has a post cover, use it
				$cover = $post->getImage('original', false, true, false);
				$firstImage = false;

				// Check for image in post content
				if (!$cover) {
					$cover = $post->getImage('large', false, true, true);
					if ($cover) {
						$firstImage = true;
					}
				}

				$instantContent = $post->getInstantContent();

				$theme = EB::themes();
				$theme->set('post', $post);
				$theme->set('author', $author);
				$theme->set('category', $category);
				$theme->set('site', $site);
				$theme->set('config', $this->config);
				$theme->set('content', $instantContent);
				$theme->set('cover', $cover);
				$theme->set('firstImage', $firstImage);

				$description = $theme->output('site/feeds/default/default');

				EBIA::clean($description);

				$item->description = $description;
			} else {

				if ($this->config->get('main_rss_content') == 'fulltext') {
					$item->description = $post->getContent(EASYBLOG_VIEW_ENTRY, true, true);
				} else {
					$options = array('fromRss' => true);
					$item->description = $post->getIntro(false, true, 'intro', null, $options);
				}

				// replace the image source to proper format so that feed reader can view the image correctly.
				$item->description = str_replace('src="//', 'src="' . $scheme . '//', $item->description);
				$item->description = str_replace('href="//', 'href="' . $scheme . '//', $item->description);

				// If the post has a post cover, use it
				$image = $post->getImage('original', false, true, false);
				$useFirstImage = $this->config->get('cover_firstimage', 0);

				// Check for image in post content to be as post cover.
				$firstImage = false;

				if (!$image) {
					$image = $post->getImage('original', false, true, $useFirstImage);
					$firstImage = true;
				}

				if ($image) {
					$enclosure = new JFeedEnclosure();

					$imagePath = str_ireplace(rtrim(JURI::root(), '/'), JPATH_ROOT, $image);
					$imageType = 'jpeg/image';
					$imageSize = 0;
					$externalImage = $this->isImageExternal($imagePath);

					// Cleanup url for external images. When url contains query strings, enclosure will not be validated correctly #2125
					if ($externalImage) {
						$image = preg_replace('/\?.*/', '', $image);
					}

					// Local images
					if (!$externalImage && JFile::exists($imagePath)) {
						$imageInfo = getimagesize($imagePath);
						$imageType = $imageInfo['mime'];
						$imageSize = filesize($imagePath);
					}

					$enclosure->url = $image;
					$enclosure->type = $imageType;
					$enclosure->length = $imageSize;

					$item->setEnclosure($enclosure);

					// Remove the first image from content if there is any
					if ($firstImage) {
						$tmpImage = $post->getImage('original', false, false, $useFirstImage);

						$tmpImage = str_replace('/', '\/', $tmpImage);
						$tmpImage = str_replace('.', '\.', $tmpImage);

						$pattern = '/<img src=["|\'].*' . $tmpImage . '["|\'][^>]+\/>/i';
						$item->description = preg_replace($pattern, '', $item->description);
					}
				}
			}

			// remove unwanted attribute from description.
			$attrPattern = array('/itemprop="[\w]+"/',
								'/data-type="[\w]+"/',
								'/data-redactor-tag="[\w]+"/',
								'/data-redactor-class="[\w]+"/',
								'/data-verified="[\w]+"/');

			$item->description = preg_replace($attrPattern, '', $item->description);

			$item->date = $post->getCreationDate()->toSql();
			$item->category = $post->getPrimaryCategory()->getTitle();
			$item->author = $post->creator->getName();
			$item->authorEmail = $this->getRssEmail($post->creator);

			$feedItems[] = $item;
		}

		return $feedItems;
	}

	/**
	 * Determines if the image url is an external image
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function isImageExternal($url)
	{
		if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
			return false;
		}

		return true;
	}

	/**
	 * Sets the rss author email
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getRssEmail($author)
	{
		if ($this->jconfig->get('feed_email') == 'none') {
			return;
		}

		if ($this->jconfig->get('feed_email') == 'author') {
			return $author->user->email;
		}

		return $this->jconfig->get('mailfrom');
	}
}
