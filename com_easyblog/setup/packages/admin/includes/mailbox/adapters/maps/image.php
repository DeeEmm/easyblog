<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogMailboxMapImage extends EasyBlogMailboxMapAbstract
{
	public function map(&$file, $index, &$contents)
	{
		// Get the attachment's extension
		$extension = JFile::getExt($file['name']);

		// Store the file to a temporary location
		$file['tmp_name'] = $this->tmp . '/' . md5($file['name']);
		JFile::write($file['tmp_name'], $file['data']);

		// Load up media manager now
		$mm = EB::mediamanager();
		$adapter = $mm->getAdapter('user:' . $this->authorId);
		$result = $adapter->upload($file, 'user:' . $this->authorId);

		$title = $file['name'];
		$url = $this->absoluteUrl . '/' . $file['name'];

		// Get the properties from media manager result
		if (is_object($result) && property_exists($result, 'title')) {
			$title = $result->title;
			$url = $result->url;
		}

		// Since the image is already uploaded, we want to set the first image as the blog image
		if ($index == 0 && $this->config->get('main_remotepublishing_mailbox_blogimage')) {
			$this->blog->image = $result->uri;
		}

		// Once the attachment is already uploaded, we want to delete the temporary file now
		JFile::delete($file['tmp_name']);

		$isInline = false;

		// Check if a file id is provided in the email
		if (isset($file['id']) && !empty($file['id'])) {

			$fileId = $file['id'];
			$fileId = str_replace('<', '', $fileId);
			$fileId = str_replace('>', '', $fileId);

			$patterns = array('/<div><img[^>]*src="[A-Za-z0-9:^>]*' . $fileId . '"[^>]*\/><\/div>/si', '/<img[^>]*src="[A-Za-z0-9:^>]*' . $fileId . '"[^>]*\/>/si');
			$replace = array('[EB:IMG]', '[EB:IMG]');

			if ($index == 0 && $this->config->get('main_remotepublishing_mailbox_blogimage')) {
				$replace = array('', '');
			}

			if ($index > 0 || !$this->config->get('main_remotepublishing_mailbox_blogimage')) {
				// we need to check if this is inline image or not.
				$imgPattern = '/<img[^>]*src="[A-Za-z0-9:^>]*' . $fileId . '"[^>]*\/>/si';
				if (preg_match($imgPattern, $contents, $match)) {
					$isInline = true;
				}
			}

			$contents = preg_replace($patterns, $replace, $contents);
		}

		// Now we need to insert these image tags into the content
		if (($index != 0 || !$this->config->get('main_remotepublishing_mailbox_blogimage'))) {

			$template = EB::themes();
			$template->set('title', $title);
			$template->set('url', $url);

			$imageHtml = $template->output('site/mailpublishing/template.image');

			$contents = EBString::str_ireplace('[EB:IMG]', $imageHtml, $contents);
		}

		// Process the rest of the image
		if (($index != 0 || !$this->config->get('main_remotepublishing_mailbox_blogimage')) && !$isInline) {
			// this is attachment type of image. just append at the end of the content. #787
			$template = EB::themes();
			$template->set('title', $title);
			$template->set('url', $url);

			$imageHtml = $template->output('site/mailpublishing/template.image');
			$contents .= $imageHtml;
		}

	}
}
