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

class EasyBlogTruncater extends EasyBlog
{
	/**
	 * Truncates the content of the blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function truncate(EasyBlogPost &$post , $charsLimit = null, $options = [])
	{
		$forceTruncateByChars = isset($options['forceTruncateByChars']) && $options['forceTruncateByChars'] ? true : false;
		$forceCharsLimit = isset($options['forceCharsLimit']) && $options['forceCharsLimit'] ?  $options['forceCharsLimit'] : 0;
		$forceImage = isset($options['forceImage']) ? true : false;

		$useFirstImage = $this->config->get('cover_firstimage', 0);

		// If introtext and content is already present, we don't need to truncate anything since it should respect the user's settings
		if (!$forceTruncateByChars && $post->intro && $post->content) {

			$post->text = $post->intro;

			// here we need to check if we need to remove the 1st image or not.
			if (! $forceImage) {
				$media = $post->getMedia();

				if ($media->images && $media->images[0]) {
					$coverImage = $post->getImage('original', true, false, $useFirstImage);

					if ($coverImage == $media->images[0]->url) {
						$post->text = EBString::str_ireplace($media->images[0]->html, '', $post->text);
					}
				}
			}


			return;
		}

		$addEllipses = false;
		$truncationType = '';
		$max = 0;

		if ($post->doctype == 'ebd') {
			$truncationType = 'chars';
			$addEllipses = true;

			if ($charsLimit && $charsLimit > 0) {
				$max = $charsLimit;
			} else {
				$max = $this->config->get('composer_truncation_chars', 350);
			}
			$max = $max <= 0 ? 350 : $max;
		} else {
			$truncationType = $this->config->get('main_truncate_type');

			if ($truncationType == 'chars' || $truncationType == 'words') {
				$addEllipses = true;

				if ($charsLimit && $charsLimit > 0) {
					$max = $charsLimit;
				} else {
					$max = $this->config->get('layout_maxlengthasintrotext', 350);
				}

				$max = $max <= 0 ? 350 : $max;

			} else {
				$max = $this->config->get('main_truncate_maxtag', 5);
				$max = $max <= 0 ? 5 : $max;
			}
		}

		if ($forceTruncateByChars) {
			$truncationType = 'chars';
			$max = $forceCharsLimit ? $forceCharsLimit : 350;
		}

		// now let get the media
		$media = $post->getMedia();

		// Strip out known codes
		$this->stripMedia($post, $media);

		// Strip out elements blocks such as buttons #1627
		$this->stripElementBlocks($post);

		// remove js script tag
		$post->text = $this->strip_only($post->text, '<script>', true);
		$post->text = $this->strip_only($post->text, '<object>', true);
		$post->text = EB::string()->trimEmptySpace($post->text);

		// Strip out known codes
		$this->stripCodes($post);

		// Truncation by characters
		if ($truncationType == 'chars') {
			$this->truncateByChars($post, $max);
		}

		// Truncation by break tags
		if ($truncationType == 'break') {
			$this->truncateByBreak($post, $max);
		}

		// Truncation by words
		if ($truncationType == 'words') {
			$this->truncateByWords($post, $max);
		}

		// Truncation by paragraph
		if ($truncationType == 'paragraph') {
			$this->truncateByParagraph($post, $max);
		}

		// Append ellipses to the content if necessary
		if ($addEllipses && (isset($post->readmore) && $post->readmore)) {
			$post->text .= JText::_('COM_EASYBLOG_ELLIPSES');
		}

		if ($truncationType == 'words' || $truncationType == 'chars') {
			$post->text = $this->wrapTextWithDropcap($post->text);
		}

		// Only process standard posts. treat empty postype as standard
		if ($post->posttype == 'standard' || !$post->posttype) {

			// Determine the position of media items that should be included in the content.
			$videoHTML = '';
			$imgHTML = '';
			$audioHTML = '';
			$galleryHTML = '';

			if ($media->images) {

				if (! $forceImage) {
					// here we need to check if we already assign the 1st image as post cover or not.
					// if yes, remove the 1st image
					$coverImage = $post->getImage('original', true, false, $useFirstImage);

					if ($coverImage == $media->images[0]->url) {
						array_shift($media->images);
					}
				}

				if ($media->images) {

					$imageLimit = (int) $this->config->get('composer_truncate_image_limit', 0);
					if ($imageLimit) {
						$media->images = array_slice($media->images, 0, $imageLimit);
					}

					foreach ($media->images as $image) {
						$imgHTML .= $image->html . '<br />';
					}

					$imgHTML = EBString::str_ireplace(array('nest-left', 'nest-right'), '', $imgHTML);
				}

			}

			// for galleries, we always return the 1st gallery if exists.
			if (property_exists($media, 'galleries') && isset($media->galleries[0]) && $media->galleries[0]->html) {
				$galleryHTML .= $media->galleries[0]->html;
			}

			if ($media->audios) {
				$audioLimit = (int) $this->config->get('composer_truncate_audio_limit', 0);
				if ($audioLimit) {
					$media->audios = array_slice($media->audios, 0, $audioLimit);
				}

				foreach ($media->audios as $audio) {
					$audioHTML .= $audio->html . '<br />';
				}
			}

			if ($media->videos) {
				$videoLimit = (int) $this->config->get('composer_truncate_video_limit', 0);
				if ($videoLimit) {
					$media->videos = array_slice($media->videos, 0, $videoLimit);
				}

				foreach ($media->videos as $video) {
					$videoHTML .= $video->html . '<br />';
				}
			}

			// We add <p> tag for styling purpose #2886
			$post->text = '<p>' . $post->text . '</p>';

			// images
			if ($this->config->get( 'composer_truncate_image_position') == 'top' && $imgHTML) {
				$post->text = $imgHTML . $post->text;
			}

			if ($this->config->get( 'composer_truncate_image_position') == 'bottom' && $imgHTML) {
				$post->text = $post->text . $imgHTML;
			}

			// Videos
			if ($this->config->get('composer_truncate_video_position') == 'top' && $videoHTML) {
				$post->text = $videoHTML . $post->text;
			}

			if ($this->config->get('composer_truncate_video_position') == 'bottom' && $videoHTML) {
				$post->text = $post->text . $videoHTML;
			}

			// Galleries
			if ($this->config->get( 'composer_truncate_gallery_position') == 'top' && $galleryHTML) {
				$post->text = $galleryHTML . $post->text;
			}

			if($this->config->get('composer_truncate_gallery_position') == 'bottom' && $galleryHTML) {
				$post->text = $post->text . $galleryHTML;
			}

			// Audios
			if ($this->config->get('composer_truncate_audio_position') == 'top' && $audioHTML) {
				$post->text = $audioHTML . $post->text;
			}

			if($this->config->get('composer_truncate_audio_position') == 'bottom' && $audioHTML) {
				$post->text = $post->text . $audioHTML;
			}
		}
	}

	/**
	 * Reverse of strip_tags
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function strip_only($str, $tags, $stripContent = false)
	{
		$content = '';

		if (!is_array($tags)) {
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));

			if (end($tags) == '') {
				array_pop($tags);
			}
		}

		foreach($tags as $tag) {
			if ($stripContent) {
				$content = '(.+</'.$tag.'[^>]*>|)';
			}

			$pattern = '#</?'.$tag.'[^>]*>#is';

			if ($stripContent) {
				$pattern = '@<('.$tag.')[^>]*>.*?</\1>@is';
			}

			$str = preg_replace($pattern, '', $str);
		}
		return $str;
	}

	/**
	 * Remove common elements blocks such as button.
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function stripElementBlocks(EasyBlogPost &$post)
	{
		static $_cache = array();

		$elementBlocks = array('buttons', 'note');

		if ($post->isEbd()) {

			// check if this post already been process or not for the tobeRemoveBlocks.
			if (!isset($_cache[$post->id])) {

				// check if this ducument as any elements blocks or not.
				$document = EB::document($post->document);

				$blocks = $document->getBlocks();
				$tobeRemoveBlocks = array();
				$nestedBlocks = array();
				$nestedBlocksUid = array();

				foreach ($blocks as $block) {

					if (in_array($block->type, $elementBlocks)) {
						$tobeRemoveBlocks[] = $block;
						continue;
					}

					if (isset($block->blocks) && is_array($block->blocks) && $block->type != 'section') {
						foreach ($block->blocks as $nestedBlock) {
							if (!in_array($nestedBlock->uid, $nestedBlocksUid)) {
								$nestedBlocks[] = $nestedBlock;
								$nestedBlocksUid[] = $nestedBlock->uid;
							}
						}
					}

					// Containers block needs to be handled differently
					if (isset($block->blocks) && is_array($block->blocks) && $block->type == 'section') {
						foreach ($block->blocks as $nestedBlock) {

							// Nested block could be a text block which is also nested
							if ($nestedBlock->nested && isset($nestedBlock->blocks) && is_array($nestedBlock->blocks)) {
								foreach ($nestedBlock->blocks as $nestedBlockChild) {
									if (!in_array($nestedBlockChild->uid, $nestedBlocksUid)) {
										$nestedBlocks[] = $nestedBlockChild;
										$nestedBlocksUid[] = $nestedBlockChild->uid;
									}
								}
							}

							// Other nested blocks
							if (!in_array($nestedBlock->uid, $nestedBlocksUid)) {
								$nestedBlocks[] = $nestedBlock;
								$nestedBlocksUid[] = $nestedBlock->uid;
							}
						}
					}
				}

				// Go through each of the nested blocks
				if ($nestedBlocks) {
					foreach ($nestedBlocks as $nestedBlock) {
						if (in_array($nestedBlock->type, $elementBlocks)) {
							$tobeRemoveBlocks[] = $nestedBlock;
							continue;
						}
					}
				}

				$_cache[$post->id] = $tobeRemoveBlocks;
			}

			$tobeRemoveBlocks = $_cache[$post->id];

			if ($tobeRemoveBlocks) {
				foreach ($tobeRemoveBlocks as $tb) {
					$tobeRemoveContent = EB::blocks()->renderViewableBlock($tb);

					if ($tobeRemoveContent) {
						$post->text = EBString::str_ireplace($tobeRemoveContent, '', $post->text);
					}
				}
			}
		}
	}

	/**
	 * Remove the processed media from the content so that the truncation can display the content properly.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function stripMedia(EasyBlogPost &$post, $media)
	{
		// if no media, just stop here.
		if (! $media) {
			return;
		}

		// remove images
		if ($media->images) {

			// foreach ($media->images as $item) {
			// 	$post->text = EBString::str_ireplace($item->html, '', $post->text);
			// }

			// remove image caption #1067, #1548, #1676
			$pattern = '/<div class="eb-image-caption".*?>[\s\S]+?<\/div>/i';

			$post->text = preg_replace($pattern, '', $post->text);
			$post->text = self::strip_only($post->text , '<img>');
		}

		// remove audios
		if ($media->audios) {
			$pattern = '/<div class="eb-block-audio".*>[\s\S]+?<audio id=".*\/>[\s\S]+?<\/div>/i';
			$post->text = preg_replace($pattern, "", $post->text);
		}

		// remove galleries
		if (property_exists($media, 'galleries') && $media->galleries) {
			$pattern = '/<div class="ebd-block" data-type="thumbnails">.*[\n\r]<\/div>/is';
			$post->text = preg_replace($pattern, "", $post->text);
		}

	}

	/**
	 * Remove known dirty codes from the content
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function stripCodes(EasyBlogPost &$post)
	{
		// Remove video codes
		EB::videos()->stripCodes($post);

		// Remove audio codes
		EB::audio()->stripCodes($post);

		// Remove gallery codes
		EB::gallery()->stripCodes($post);

		// Remove album codes
		EB::album()->stripCodes($post);

		// Remove Readmore tag for entry view #1979
		$this->removeReadmore($post->intro);
	}

	/**
	 * Performs truncation of the content by words
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function truncateByWords(EasyBlogPost &$post, $max = 0)
	{
		$tag = false;
		$count = 0;
		$output = '';

		if (!$max) {
			$max = $post->doctype == 'ebd' ? $this->config->get('composer_truncation_chars', 150) : $this->config->get('layout_maxlengthasintrotext', 150);
		}

		// remove javasript tag 1st.
		// $post->text = $this->strip_only($post->text, '<script>', true);

		// Remove uneccessary html tags to avoid unclosed html tags
		$post->text = $this->stripTags($post->text);

		// Get a list of space breaks
		$words = preg_split("/([\s]+)/", $post->text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		foreach ($words as $word) {

			if (!$tag || stripos($word, '>') !== false) {
				$tag = (bool) (strripos($word, '>') < strripos($word, '<'));
			}

			// If this is a space, we should skip this
			if (!$tag && trim($word) == '') {
				$count++;
			}

			if ($count > $max && !$tag) {
				$post->readmore = true;
				break;
			}

			$output .= $word;
		}

		$post->text = $output;
	}

	/**
	 * Performs truncation by characters
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function truncateByChars(EasyBlogPost &$post, $max = 0)
	{
		if (!$max) {
			$max = $post->doctype == 'ebd' ? $this->config->get('composer_truncation_chars', 150) : $this->config->get('layout_maxlengthasintrotext', 150);
		}

		// Remove uneccessary html tags to avoid unclosed html tags
		$post->text = $this->stripTags($post->text);

		// Remove blank spaces since the word calculation should not include new lines or blanks.
		$post->text = trim($post->text);

		$post->readmore = false;

		// Determines if there's a read more
		if (EBString::strlen($post->text) > $max) {
			$post->readmore = true;
		}

		// Since this is truncation by characters, just slice the data out
		$post->text = EBString::substr($post->text, 0, (int) $max);
	}

	/**
	 * Truncation by break tags
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function truncateByBreak(EasyBlogPost &$post, $max = 0)
	{
		// before we can truncation, we need to 'standardize the br tag'
		$tag = '<br />';
		$tobeReplace = array('<br>', '<br/>');

		$post->text = EBString::str_ireplace('<br>', $tag, $post->text);

		$this->truncateByTag($post, $tag, $max, 'break');
	}

	/**
	 * Truncates the content by paragraph
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function truncateByParagraph(EasyBlogPost &$post, $max = 0)
	{
		// lets clean up empty <p> tags.
		// $post->text = EBString::str_ireplace('<p>\s+</p>', '', $post->text);

		$post->text = preg_replace('/<p\>[\s\r\n]*<\/p\>/is', '', $post->text);

		$this->truncateByTag($post, '</p>', $max, 'paragraph');
	}


	/**
	 * Truncates the content by paragraph
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function truncateByTag(EasyBlogPost &$post, $tag, $max = 0, $truncateType = 'paragraph')
	{
		$position = 0;
		$matches = array();

		// if tag is empty, default to p tag.
		if (!$tag) {
			$tag = '</p>';
		}

		// strip out image.
		$post->text = $this->strip_only($post->text, '<img>');

		// Do not proceed if the content is empty
		if (!$post->text) {
			return;
		}

		// Iterate through the contents
		do {
			$position = @EBString::strpos(strtolower($post->text), $tag, $position +1);

			if ($position !== false) {
				$matches[] = $position;
			}
		} while ($position !== false);


		// If there's lesser number of paragraphs, just skip this altogether
		if (count($matches) <= $max) {

			// There shouldn't be a read more
			$post->readmore = false;

			return;
		}

		// Specifies the length of the returned string for example : </p>
		$lengthNum = 4;

		// since we looking for this break tag <br />, we need to set the length to 6
		if ($truncateType == 'break') {
			$lengthNum = 6;
		}

		$post->text = EBString::substr($post->text, 0, $matches[$max - 1] + $lengthNum);

		// Generate a list of known tags that might break the truncation
		$htmlTagPattern = array('/\<div/i', '/\<table/i');
		$htmlCloseTagPattern = array('/\<\/div\>/is', '/\<\/table\>/is');
		$htmlCloseTag = array('</div>', '</table>');

		for ($i = 0; $i < count($htmlTagPattern); $i++) {
			$htmlItem = $htmlTagPattern[$i];
			$htmlItemClosePattern = $htmlCloseTagPattern[$i];
			$htmlItemCloseTag = $htmlCloseTag[$i];

			preg_match_all($htmlItem, strtolower($post->text), $totalOpenItem);

			if (isset($totalOpenItem[0]) && !empty($totalOpenItem[0])) {
				$totalOpenItem = count($totalOpenItem[0]);

				preg_match_all($htmlItemClosePattern, strtolower($post->text), $totalClosedItem);

				$totalClosedItem = count($totalClosedItem[0]);
				$totalItemToAdd	= $totalOpenItem - $totalClosedItem;

				if ($totalItemToAdd > 0) {

					for ($y = 1; $y <= $totalItemToAdd; $y++) {
						$post->text .= $htmlItemCloseTag;
					}
				}
			}
		}

		// There needs to be a way to set a readmore on the post library
		$post->readmore = true;
	}

	/**
	 * Legacy method to retrieve images
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getImages($content, $returnObject = false, $debug = false)
	{
		// Match images that has preview.
		$pattern = '/<a class="easyblog-thumb-preview"(.*?)<\/a>/is';
		preg_match($pattern, $content, $matches);

		if (!$matches) {

			$pattern = '#<img[^>]*>#i';
			preg_match_all($pattern, $content, $matches);
		}

		if (!$matches || !isset($matches[0]) || !$matches[0]) {
			return array();
		}

		if ($returnObject) {

			$images = array();
			$urlPattern = '#src="([^"]*)"#i';

			$tmp = $matches[0];

			if (! is_array($matches[0])) {
				$tmp = array($matches[0]);
			}

			foreach ($tmp as $image) {
				preg_match($urlPattern, $image, $urls);

				if ($urls && isset($urls[1])) {
					$obj = new stdClass();
					$obj->url = $urls[1];
					$obj->html = $image;

					$images[] = $obj;
				}
			}

			return $images;
		}

		return $matches[0];
	}

	/**
	 * Strip readmore tag
	 *
	 * @since	5.3
	 * @access	public
	 */
	public function removeReadmore($text)
	{
		$pattern = '/<div[^>]+data-type=\"readmore\".*?>.*?<\/div>/';
		$text = preg_replace($pattern, '', $text);

		return $text;
	}

	/**
	 * Extended method to strip the html tags
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function stripTags($text)
	{
		// we need to strip the javascript tag 1st before further process on stripping other html tags. #1751
		$text = EB::truncater()->strip_only($text, 'script', true);

		// Replace paragraph and heading tags with white space to avoid the text from being clustered together.
		// #2279 and #2276
		$text = preg_replace(array('#<\/?h[1-6][^>]*>#', '#<\/?p[^>]*>#'), ' ', $text);

		// other html tags we can just replace with empty space to avoid unexpected spacing when
		// user use font styling tag like <b> and <i>.
		// #2069
		$text = preg_replace('#<[^>]+>#', '', $text);

		// Replace multiple whitespaces with single whitespace
		$text = preg_replace('!\s+!', ' ', $text);

		// Remove uneccessary html tags to avoid unclosed html tags
		$text = strip_tags($text);

		// remove leading space / ending space.
		$text = trim($text);

		return $text;
	}

	/**
	 * Wraps text with drop cap if necessary
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function wrapTextWithDropcap($text)
	{
		if (!$this->config->get('layout_dropcaps')) {
			return $text;
		}

		$text = '<p class="has-drop-cap">' . $text . '</p>';

		return $text;
	}
}
