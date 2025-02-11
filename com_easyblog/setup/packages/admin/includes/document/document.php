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

class EasyBlogDocument extends EasyBlog
{
	/**
	 * Determines the document type
	 * @var string
	 */
	public $type = 'ebd';

	/**
	 * The version of this document
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * The title of the document
	 * @var string
	 */
	public $title = '';

	/**
	 * The permalink of the document
	 * @var string
	 */
	public $permalink = '';

	/**
	 * Contains blocks placed in the intro section
	 * @var Array
	 */
	public $intro = [];

	/**
	 * Contains blocks placed in the content section
	 * @var Array
	 */
	public $content = [];
	public $blocks = [];

	public function __construct($json = null)
	{
		parent::__construct();

		// New document
		if (isset($json)) {
			$this->load($json);
		}
	}

	/**
	 * Loads the json string of a document
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function load($json)
	{
		if (is_string($json)) {
			$document = json_decode($json);
		}

		if (is_object($json)) {
			$document = $json;
		}

		// Check if object is empty by converting the data to array
		$documentArray = EB::makeArray($document);

		// Return empty set
		if (!$document || empty($document) || empty($documentArray)) {
			return;
		}

		$this->title = isset($document->title) ? $document->title : '';
		$this->permalink = isset($document->permalink) ? $document->permalink : '';
		$this->type = $document->type;
		$this->version = $document->version;
		$this->blocks = $document->blocks;

		// Split the intro / content blocks so we know how to retrieve it later
		$readmoreIndex = false;

		for ($i = 0; $i < count($this->blocks); $i++) {
			$block = $this->blocks[$i];

			if ($block->type == 'readmore') {
				$readmoreIndex = $i;
			}
		}

		// If there is read more block found in the content, split the intro and content accordingly
		if ($readmoreIndex !== false) {
			$this->intro = array_slice($this->blocks, 0, $readmoreIndex + 1);
			$this->content = array_slice($this->blocks, $readmoreIndex + 1);
		} else {

			// When there is no read more block assigned, just place all the blocks into the content
			$this->intro = [];
			$this->content = $this->blocks;
		}
	}

	/**
	 * Retrieves the blocks from a document
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getBlocks()
	{
		$blocks = array_merge($this->intro, $this->content);

		return $blocks;
	}

	/**
	 * Determines if this document has read more
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasReadmore()
	{
		$readmore = true;

		// If there is intro and content is not empty, we know there should be a read more
		if ($this->intro && $this->content) {
			return true;
		}

		// If there is intro but content is empty, we know there shouldn't be a read more since it doesn't make sense to display read more link.
		if ($this->intro && !$this->content) {
			return false;
		}

		// If the automated truncation is supposed to happen here, try to fetch the read more.
		if ($this->config->get('composer_truncation_enabled')) {

			// Truncate by characters
			$limit = $this->config->get('composer_truncation_chars', 300);
			$output = '';
			$blocks = $this->getBlocks();

			// Once we have the list of blocks, render the output for each of the blocks
			foreach ($blocks as $block) {
				// We need to always strip the tags when automated truncation kicks in, so the respective blocks, doesn't insert any unwanted stuffs
				$output .= EB::blocks()->renderViewableBlock($block, true);
			}

			// Since this is automated truncation, we need to strip the tags to truncate to the characters
			$output = strip_tags($output);

			if (EBString::strlen($output) > $limit) {
				return true;
			}

			return false;
		}
	}

	/**
	 * Retrieves the intro text part of a document
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getIntro($stripTags = false, $limit = null, $options = [])
	{
		$output = '';

		// force the module do not show all the audio/image content.
		$forceSkipAudio = isset($options['skipAudio']) ? $options['skipAudio'] : false;
		$forceSkipImage = isset($options['skipImage']) ? $options['skipImage'] : false;
		$forceSkipVideo = isset($options['skipVideo']) ? $options['skipVideo'] : false;
		$skipTriggerPlugins = isset($options['triggerPlugins']) ? $options['triggerPlugins'] : true;
		$forceNotSkipHTML = isset($options['notSkipHTML']) ? $options['notSkipHTML'] : false;

		// When a post already has a cover picture, skip processing of images
		if (isset($options['hasCover']) && $options['hasCover']) {
			$forceSkipImage = true;
		}

		// If there's no blocks here, skip this altogether
		if (!isset($this->blocks) || !$this->blocks) {
			return $output;
		}

		// Calculate the total amount of blocks available on this document.
		$total = count($this->blocks);

		// We only retrieve the intro blocks
		$blocks = $this->intro;

		// Render the blocks library
		$blocksLib = EB::blocks();

		// If there is already "intro", we don't need to perform any truncation since the author
		// explicitly used the read more block to split the text
		if ($blocks) {
			foreach ($blocks as $block) {
				$output .= $blocksLib->renderViewableBlock($block, $stripTags);
			}

			if ($stripTags) {
				$output = strip_tags($output);
			}

			return $output;
		}

		// If there is no blocks under the intro property, try to use the blocks from content property.
		$blocks = $this->content;

		$nestedBlocks = [];
		$nestedBlocksUid = [];

		// Get a list of nested blocks
		foreach ($blocks as $block) {

			if (isset($block->blocks) && is_array($block->blocks)) {
				foreach ($block->blocks as $nestedBlock) {

					if (!in_array($nestedBlock->uid, $nestedBlocksUid)) {
						$nestedBlocks[] = $nestedBlock;
						$nestedBlocksUid[] = $nestedBlock->uid;
					}
				}
			}
		}


		// These settings determines if the media's should be placed in the intro
		$videoBlocks = [];
		$imageBlocks = [];
		$audioBlocks = [];

		$videoTypes = ['video', 'blip', 'dailymotion','liveleak','livestream', 'metacafe','ted','vimeo','yahoo','youtube'];

		// If the automated truncation is supposed to happen here, try to fetch the read more.
		if ($this->config->get('composer_truncation_enabled') && !$forceNotSkipHTML || $limit) {

			$remainingBlocks = [];

			// Go through each of the nested blocks
			if ($nestedBlocks) {

				foreach ($nestedBlocks as $nestedBlock) {

					if ($nestedBlock->type == 'image') {
						$imageBlocks[] = $nestedBlock;
						continue;
					}

					if (in_array($nestedBlock->type, $videoTypes)) {
						$videoBlocks[] = $nestedBlock;
						continue;
					}

					if ($nestedBlock->type == 'audio') {
						$audioBlocks[] = $nestedBlock;
						continue;
					}
				}
			}

			// Go through each of the main blocks
			foreach ($blocks as $block) {

				if ($block->type == 'image') {
					$imageBlocks[] = $block;
					continue;
				}

				if (in_array($block->type, $videoTypes)) {
					$videoBlocks[] = $block;
					continue;
				}

				if ($block->type == 'audio') {
					$audioBlocks[] = $block;
					continue;
				}

				$remainingBlocks[] = $block;
			}

			$blocks = $remainingBlocks;

			// Once we have the list of blocks, render the output for each of the blocks
			foreach ($blocks as $block) {
				// We need to always strip the tags when automated truncation kicks in, so the respective blocks, doesn't insert any unwanted stuffs
				$output .= EB::blocks()->renderViewableBlock($block, true);
			}

			// Since this is automated truncation, we need to strip the tags to truncate to the characters
			$output = strip_tags($output);

			if (!$limit) {
				// Truncate by characters
				$limit = $this->config->get('composer_truncation_chars', 300);
			}

			if (EBString::strlen($output) > $limit) {
				$output = EBString::substr($output, 0, $limit);
				$output .= JText::_('COM_EASYBLOG_ELLIPSES');
			}

			$imageHTML = '';
			$videoHTML = '';
			$audioHTML = '';

			$imageLimit = $this->config->get('composer_truncate_image_limit', 0);
			$videoLimit = $this->config->get('composer_truncate_video_limit', 0);
			$audioLimit = $this->config->get('composer_truncate_audio_limit', 0);

			if ($imageBlocks && $this->config->get('composer_truncate_image_position') != 'hidden') {
				$i = 0;
				foreach ($imageBlocks as $imageBlock) {
					if ($imageLimit && $i >= $imageLimit) {
						continue;
					}

					$imageHTML .= EB::blocks()->renderViewableBlock($imageBlock, $stripTags);
					$i++;
				}
			}

			if ($videoBlocks && $this->config->get('composer_truncate_video_position') != 'hidden') {
				$i = 0;
				foreach ($videoBlocks as $videoBlock) {
					if ($videoLimit && $i >= $videoLimit) {
						continue;
					}

					$videoHTML .= EB::blocks()->renderViewableBlock($videoBlock, $stripTags);
					$i++;
				}
			}

			if ($audioBlocks && $this->config->get('composer_truncate_audio_position') != 'hidden') {
				$i = 0;
				foreach ($audioBlocks as $audioBlock) {
					if ($audioLimit && $i >= $audioLimit) {
						continue;
					}

					$audioHTML .= EB::blocks()->renderViewableBlock($audioBlock, $stripTags);
					$i++;
				}
			}

			// We need to place the media files accordingly.

			// Determines if we should prefix or postfix the items
			if (!$forceSkipImage && $this->config->get('composer_truncate_image_position') == 'top') {
				$output = $imageHTML . $output;
			}

			if (!$forceSkipImage && $this->config->get('composer_truncate_image_position') == 'bottom') {
				$output = $output . $imageHTML;
			}

			if (!$forceSkipVideo && $this->config->get('composer_truncate_video_position') == 'top') {
				$output = $videoHTML . $output;
			}

			if (!$forceSkipVideo && $this->config->get('composer_truncate_video_position') == 'bottom') {
				$output = $output . $videoHTML;
			}

			if (!$forceSkipAudio && $this->config->get('composer_truncate_audio_position') == 'top') {
				$output = $audioHTML . $output;
			}

			if (!$forceSkipAudio && $this->config->get('composer_truncate_audio_position') == 'bottom') {
				$output = $output . $audioHTML;
			}

			return $output;
		}

		// Once we have the list of blocks, render the output for each of the blocks
		foreach ($blocks as $block) {
			$output .= EB::blocks()->renderViewableBlock($block, $stripTags);
		}

		if ($stripTags) {
			$output = strip_tags($output);
		}

		return $output;
	}

	/**
	 * Retrieves the content which is separated from the intro
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getContentWithoutIntro($stripTags = false)
	{
		// If automated truncation enabled, only get blocks located
		$output = '';

		// If there's no blocks here, skip this altogether
		if (!isset($this->blocks) || !$this->blocks) {
			return $output;
		}

		// Calculate the total amount of blocks available on this document.
		$total = count($this->blocks);

		// We only retrieve the intro blocks
		$blocks = $this->content;

		// Will temporary commented out this is because already removed this option 'composer_truncation_blocks'

		// If there is no blocks under the intro property, try to use the blocks from content property.
		// if ($blocks && !$this->intro) {

		// 	// If the automated truncation is supposed to happen here, try to fetch the read more.
		// 	if ($this->config->get('composer_truncation_enabled')) {

		// 		// Truncation settings for blocks
		// 		$max = $this->config->get('composer_truncation_blocks');

		// 		// Get the total number of blocks
		// 		if ($total > $max) {
		// 			$blocks = array_splice($blocks, $max);
		// 		}
		// 	}
		// } else if (!$blocks && $this->intro) {
		// 	// empty content. lets just display intro text fullly since the getIntro fucntion did not apply truncation on intro.
		// 	$blocks = $this->intro;
		// }

		// Once we have the list of blocks, render the output for each of the blocks
		foreach ($blocks as $block) {
			$output .= EB::blocks()->renderViewableBlock($block, $stripTags);
		}

		if ($stripTags) {
			$output = strip_tags($output);
		}

		return $output;
	}

	/**
	 * Retrieves a list of unique blocks from the document
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getUniqueBlocks()
	{

	}

	/**
	 * Retrieves all the block uid's within a document
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUids()
	{
		$uids = [];
		$index = 0;

		foreach ($this->getBlocks() as $block) {
			$uids[$block->uid] = $index;

			$index++;
		}


		return $uids;
	}

	/**
	 * Retrieves the document content that is used for diff
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDiffContent(EasyBlogDocument $target)
	{
		$theme = EB::themes();

		$currentBlocks = $this->getBlocks();

		$tmp = $target->getBlocks();

		// Reset target blocks indexes to use it's uids
		$targetBlocks = [];

		foreach ($tmp as $tmpBlock) {
			$targetBlocks[$tmpBlock->uid] = $tmpBlock;
		}

		$currentBlocksFlat = [];
		foreach ($currentBlocks as $curTmpBlock) {
			$this->traverseBlocks($curTmpBlock, $currentBlocksFlat);
		}

		$currentTotalBlocks = count($currentBlocks);
		$targetTotalBlocks = count($targetBlocks);

		// Get all the uid's from both documents
		$currentUids = $this->getUids();
		$targetUids = $target->getUids();

		// $total = $currentTotalBlocks > $targetTotalBlocks ? $currentTotalBlocks : $targetTotalBlocks;


		$html = '';
		foreach( $targetBlocks as $block) {
			$html .= EB::blocks()->renderDiffBlocks($block, $currentBlocksFlat );
		}

		// $theme->set('currentUids', $currentUids);
		// $theme->set('targetUids', $targetUids);

		// $theme->set('currentBlocksFlat', $currentBlocksFlat);
		// $theme->set('currentBlocks', $currentBlocks);
		// $theme->set('targetBlocks', $targetBlocks);

		$theme->set('html', $html);

		$output = $theme->output('site/composer/revisions/compare.blocks');

// echo $output;exit;

		return $output;
	}

	private function traverseBlocks($block, &$container)
	{
		$container[$block->uid] = $block;

		if (isset($block->blocks)) {
			foreach($block->blocks as $innerBlock) {
				$this->traverseBlocks($innerBlock, $container);
			}
		}
	}

	/**
	 * Retrieves the document content by rendering the blocks
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getContent()
	{
		$output = '';

		if (!isset($this->blocks) || !$this->blocks) {
			return $output;
		}

		foreach ($this->blocks as $block) {
			$output .= EB::blocks()->renderViewableBlock($block, false);
		}

		return $output;
	}

	/**
	 * Retrieves the document content by rendering the blocks
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getInstantContent()
	{
		$output = '';

		if (!isset($this->blocks) || !$this->blocks) {
			return $output;
		}

		foreach ($this->blocks as $block) {
			$blockOutput = EB::blocks()->renderInstantBlock($block);

			$output .= $blockOutput;
		}

		return $output;
	}

	/**
	 * Retrieves the document content by rendering the blocks
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getAMPContent()
	{
		$output = '';

		if (!isset($this->blocks) || !$this->blocks) {
			return $output;
		}

		foreach ($this->blocks as $block) {

			$blockOutput = EB::blocks()->renderAMPBlock($block);

			$output .= $blockOutput;
		}

		return $output;
	}

	/**
	 * Retrieves the editable html codes for each blocks
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getEditableContent($postTemplateIsLocked = false)
	{
		$output = '';

		if (!isset($this->blocks) || !$this->blocks) {
			return $output;
		}

		foreach ($this->blocks as $block) {

			// Backward compatibility
			EB::blocks()->legacyBlockCompatibility($block);

			$output .= EB::blocks()->renderEditableBlock($block, true, $postTemplateIsLocked);
		}

		return $output;
	}

	public function processCopy()
	{
		$blocks = $this->blocks;

		$blocks = $this->replaceTabId($blocks);

		$this->blocks = $blocks;
	}

	/**
	 * At times redactor would inject zero width invisible characters and this could cause integrity issue to the content.
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function removeInvisibleCharacter($content)
	{
		$zeroWidth = "\xE2\x80\x8B";
		$content = EBString::str_ireplace($zeroWidth, '', $content);

		return $content;
	}

	public function replaceTabId($blocks)
	{
		foreach ($blocks as $block) {
			if ($block->type == 'tabs') {
				$uniqueId = uniqid();

				$block->html = str_replace('firsttab', 'firsttab-'. $uniqueId, $block->html);
				$block->html = str_replace('href="#tab-', 'href="#tab-'. $uniqueId . '-', $block->html);
				$block->html = str_replace('id="tab-', 'id="tab-'. $uniqueId . '-', $block->html);

				$block->editableHtml = str_replace('firsttab', 'firsttab-'. $uniqueId, $block->editableHtml);
				$block->editableHtml = str_replace('href="#tab-', 'href="#tab-'. $uniqueId . '-', $block->editableHtml);
				$block->editableHtml = str_replace('id="tab-', 'id="tab-'. $uniqueId . '-', $block->editableHtml);
			}

			if ($block->blocks) {
				$block->blocks = $this->replaceTabId($block->blocks);
			}
		}

		return $blocks;
	}


	/**
	 * Retrieves the intro text part of a document
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function processIntro($options = [])
	{
		$output = '';

		// If there's no blocks here, skip this altogether
		if (!isset($this->blocks) || !$this->blocks) {
			return $output;
		}

		$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

		// Calculate the total amount of blocks available on this document.
		$total = count($this->blocks);

		// We only retrieve the intro blocks
		$blocks = $this->intro;

		// Render the blocks library
		$blocksLib = EB::blocks();

		// If there is already "intro", we don't need to perform any truncation since the author
		// explicitly used the read more block to split the text
		if ($blocks) {
			foreach ($blocks as $block) {
				$output .= $blocksLib->renderViewableBlock($block, false, $useRelative);
			}

			$output = $this->removeInvisibleCharacter($output);
			return $output;
		}

		// // If there is no blocks under the intro property, try to use the blocks from content property.
		// $blocks = $this->content;

		// $nestedBlocks = [];
		// $nestedBlocksUid = [];

		// if ($blocks) {
		// 	foreach ($blocks as $block) {
		// 		if ($block->type == 'readmore') {
		// 			continue;
		// 		}
		// 		$output .= $blocksLib->renderViewableBlock($block, false, $useRelative);
		// 	}

		// 	return $output;
		// }

		$output = $this->removeInvisibleCharacter($output);

		return $output;
	}

	/**
	 * Retrieves the intro text part of a document
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function processContent($options = [])
	{
		$output = '';

		// If there's no blocks here, skip this altogether
		if (!isset($this->blocks) || !$this->blocks) {
			return $output;
		}

		$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

		$blocks = $this->content;


		// Render the blocks library
		$blocksLib = EB::blocks();

		if ($blocks) {
			foreach ($blocks as $block) {
				$output .= $blocksLib->renderViewableBlock($block, false, $useRelative);
			}

			$output = $this->removeInvisibleCharacter($output);
			return $output;
		}

		$output = $this->removeInvisibleCharacter($output);
		return $output;
	}

	/**
	 * Retrieves the media from a document
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function extractMedia()
	{
		if (!isset($this->blocks) || !$this->blocks) {
			return false;
		}

		$total = count($this->blocks);
		if ($total <= 0) {
			return false;
		}

		$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

		$videoTypes = ['video', 'blip', 'dailymotion', 'liveleak', 'livestream', 'metacafe', 'ted', 'vimeo', 'yahoo', 'youtube'];

		$blocks = $this->blocks;

		$nestedBlocks = [];
		$nestedBlocksUid = [];

		// These settings determines if the media's should be placed in the intro
		$videoBlocks = [];
		$imageBlocks = [];
		$audioBlocks = [];
		$pdfBlocks = [];
		$galleryBlocks = [];

		$firstImageBlockUid = false;

		// Get a list of nested blocks
		foreach ($blocks as $block) {

			if ($block->type == 'image' && $firstImageBlockUid == false) {
				$firstImageBlockUid = $block->uid;
			}

			if (isset($block->blocks) && is_array($block->blocks)) {

				foreach ($block->blocks as $nestedBlock) {

					if (!in_array($nestedBlock->uid, $nestedBlocksUid)) {

						$nestedBlocks[] = $nestedBlock;
						$nestedBlocksUid[] = $nestedBlock->uid;

						if ($nestedBlock->type == 'image' && $firstImageBlockUid == false) {
							$firstImageBlockUid = $nestedBlock->uid;
						}
					}
				}
			}
		}

		// Render the blocks library
		$blocksLib = EB::blocks();

		// Go through each of the nested blocks
		if ($nestedBlocks) {

			foreach ($nestedBlocks as $nestedBlock) {

				// We need to check for the nested block whether got image block inside
				if (isset($nestedBlock->blocks) && is_array($nestedBlock->blocks)) {
					foreach ($nestedBlock->blocks as $nestedChildBlock) {

						if ($nestedChildBlock->type == 'image' && $firstImageBlockUid == false) {
							$imageBlocks[] = $nestedChildBlock;
							continue;
						}
					}
				}

				if ($nestedBlock->type == 'image') {
					$imageBlocks[] = $nestedBlock;
					continue;
				}

				if (in_array($nestedBlock->type, $videoTypes)) {
					$videoBlocks[] = $nestedBlock;
					continue;
				}

				if ($nestedBlock->type == 'audio') {
					$audioBlocks[] = $nestedBlock;
					continue;
				}

				if ($nestedBlock->type == 'gallery') {
					$galleryBlocks[] = $nestedBlock;
					continue;
				}

				if ($nestedBlock->type == 'pdf') {
					$pdfBlocks[] = $nestedBlock;
					continue;
				}
			}
		}

		// Go through each of the main blocks
		foreach ($blocks as $block) {

			if ($block->type == 'image') {
				$imageBlocks[] = $block;
				continue;
			}

			if (in_array($block->type, $videoTypes)) {
				$videoBlocks[] = $block;
				continue;
			}

			if ($block->type == 'audio') {
				$audioBlocks[] = $block;
				continue;
			}

			if ($block->type == 'gallery') {
				$galleryBlocks[] = $block;
				continue;
			}

			if ($block->type == 'pdf') {
				$pdfBlocks[] = $block;
				continue;
			}

		}

		$data = ['images' => [],'videos' => [],'audios' => [], 'galleries' => [], 'pdf' => []];

		if ($pdfBlocks) {
			foreach ($pdfBlocks as $block) {
				$obj = new stdClass();

				$obj->url = $block->data->url;
				if ($useRelative) {
					// make sure url is relative
					$obj->url = EB::string()->abs2rel($block->data->url);
				}
				$obj->html = $blocksLib->renderViewableBlock($block, false, $useRelative);

				$data['pdf'][] = $obj;
			}
		}


		if ($imageBlocks) {
			foreach ($imageBlocks as $block) {
				$obj = new stdClass();

				$obj->isurl = $block->data->isurl;
				$obj->url = $block->data->url;

				$isLocal = stripos($block->data->uri, 'flickr') === false;

				// If the block image is not an external url and we want to use relative
				if (!$block->data->isurl && $isLocal && $useRelative) {
					// make sure url is relative
					$obj->url = EB::string()->abs2rel($block->data->url);
				}

				$obj->uri = $block->data->uri;
				$obj->html = $blocksLib->renderViewableBlock($block, false, $useRelative);

				if ($block->uid == $firstImageBlockUid) {
					// This is to make sure the first image will always be the first #1331
					array_unshift($data['images'], $obj);
				} else {
					$data['images'][] = $obj;
				}
			}
		}

		if ($videoBlocks) {
			foreach ($videoBlocks as $block) {
				$obj = new stdClass();

				$obj->url = $block->data->url;
				if ($useRelative) {
					// make sure url is relative
					$obj->url = EB::string()->abs2rel($block->data->url);
				}
				$obj->html = $blocksLib->renderViewableBlock($block, false, $useRelative);

				$data['videos'][] = $obj;
			}
		}

		if ($audioBlocks) {
			foreach ($audioBlocks as $block) {
				$obj = new stdClass();

				$obj->url = $block->data->url;
				if ($useRelative) {
					// make sure url is relative
					$obj->url = EB::string()->abs2rel($block->data->url);
				}
				$obj->html = $blocksLib->renderViewableBlock($block, false, $useRelative);

				$data['audios'][] = $obj;
			}
		}

		if ($galleryBlocks) {
			foreach ($galleryBlocks as $block) {

				$obj = new stdClass();
				$obj->html = $blocksLib->renderViewableBlock($block, false, $useRelative);

				$data['galleries'][] = $obj;
			}
		}

		return $data;
	}

	/**
	 * Exports a document object into a json string
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toJSON()
	{
		$document = new stdClass();

		$document->type = $this->type;
		$document->blocks = $this->blocks;
		$document->version = $this->version;

		return json_encode($document);
	}
}
