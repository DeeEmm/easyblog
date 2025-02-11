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

class EasyBlogBlockHandlerHtml extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-code';
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;


		return $meta;
	}

	public function data()
	{
		$data = (object) array();

		return $data;
	}

	/**
	 * We need to alter the behavior of the HTML block
	 * to address issues with dynamic scripts that tries to alter the output
	 *
	 * @since 	5.1.4
	 * @access 	public
	 */
	public function getHtml($block, $textOnly = false)
	{
		// Since version 5.1.14, we now render the original codes instead.
		// The reason is because some codes dynamically alters the html codes (se.g: adsense codes)
		if (isset($block->data->original)) {

			// Ensure that the html content is in the correct format
			$block->data->original = EB::string()->fixUnclosedTags($block->data->original);

			return $block->data->original;
		}

		return $block->html;
	}

	/**
	 * Retrieves the output for the block when it is being edited
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getEditableHtml($block)
	{
		// Since version 5.1.14, we now store the original entered in the texteditor instead of the html output of the DOM
		if (isset($block->data->original)) {

			// Ensure that the html content is in the correct format
			$block->data->original = EB::string()->fixUnclosedTags($block->data->original);

			$content = $block->data->original;
		} else {
			$content = isset($block->editableHtml) ? $block->editableHtml : '';
		}

		// Strip any <form> tag to avoid nasty issue with form submitting. #1590
		$content = preg_replace('/<\/?form(.|\s)*?>/', '', $content);

		// Experimental fix with safari iframe issue for xss protection during rendering. #1928
		if (preg_match('/(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))/', $content)) {
			header("X-XSS-Protection: 0");
		}

		return $content;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.3.0
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		// process url protocol
		$uri = JURI::getInstance();

		// Retrieve the content from the HTML block
		$content = $block->html;

		if (isset($block->data->original)) {

			$content = $block->data->original;

			// Ensure that the html content is in the correct format
			$content = EB::string()->fixUnclosedTags($content);
		}

		if ($uri->getScheme() == 'https') {

			$iframeRegex = '/(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))/';

			if (preg_match($iframeRegex, $content, $match)) {

				$srcImage = EB::getAmpPlaceholderImage();

				// add placeholder in iframe
				$content = str_replace('</iframe>', '<amp-img layout="fill" src="' . $srcImage . '" placeholder></amp-img></iframe>', $content);

				return $content;
			}
		}

		$imageWidth = isset($block->data->imageWidth) && $block->data->imageWidth ? $block->data->imageWidth : '';
		$imageHeight = isset($block->data->imageHeight) && $block->data->imageHeight ? $block->data->imageHeight : '';

		// Convert this HTML content inside the image tag to amp-image tag
		$content = $this->normaliseImageTags($content, $imageWidth, $imageHeight);

		return $content;
	}

	/**
	 * Convert HTML img tag to amp-img tag
	 *
	 * @since   5.4.0
	 * @access  public
	 */
	public function normaliseImageTags($blockContent, $customImageWidth, $customImageHeight)
	{
		$config = EB::config();
		$maxWidth = $config->get('main_image_thumbnail_width');
		$maxHeight = $config->get('main_image_thumbnail_height');

		// Determine whether need to show responsive image layout or fixed pixel
		// Reference link : https://amp.dev/documentation/guides-and-tutorials/develop/style_and_layout/control_layout/?format=websites#the-layout-attribute
		if ($customImageWidth && $customImageHeight) {
			$dimension = 'width="' . $customImageWidth . '" height="' . $customImageHeight . '"';
			$layoutAttr = "fixed";
		} else {
			// respect the current maximum thumbnail image setting
			$dimension = 'width="' . $maxWidth . '" height="' . $maxHeight . '"';
			$layoutAttr = "responsive";
		}

		$pattern = '#<img[^>]*>#i';
		preg_match_all($pattern, $blockContent, $matches);

		if ($matches[0]) {

			foreach ($matches[0] as $imagetag) {

				// Retrieve the image link
				preg_match('/src="([^"]+)"/', $imagetag, $src);

				$url = $src[1];

				// Apply those image link and image dimension into this amp-img tag
				$normalisedAMPImageTag = '<amp-img src="' . $url . '" ' . ' layout="' . $layoutAttr . '" ' . $dimension . '></amp-img>';

				// Replace back the modified content into that block content
				$blockContent = str_replace($imagetag, $normalisedAMPImageTag, $blockContent);
			}
		}

		return $blockContent;
	}
}
