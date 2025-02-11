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

class EasyBlogModules extends EasyBlog
{
	const SOURCE_INTRO = "0";
	const SOURCE_CONTENT = "1";
	const SOURCE_HIDDEN = "-1";

	const POST_TRUNCATE = false;
	const POST_STRIP_TAGS = false;
	const POST_SOURCE_INTRO_COLUMN = 'intro';
	const POST_SOURCE_CONTENT_COLUMN = 'content';
	const POST_TRIGGER_PLUGIN = false;

	public $name = null;
	public $module = null;
	public $params = null;
	public $baseurl = null;

	public function __construct($module, $requireScripts = true, $requireCss = true)
	{
		parent::__construct();

		$this->module = $module;
		$this->name = $this->module->module;

		// At times, the $module->params variable could be converted into a registry object already by the Joomla template
		// To ensure compatibility with these sort of templates, we cannot convert it into a JRegistry again.
		if ($this->module->params instanceof JRegistry) {
			$this->params = $this->module->params;
		} else {
			$this->params = new JRegistry($this->module->params);
		}

		$this->baseurl = JURI::root(true);

		if ($requireScripts) {
			$this->requireScripts();
		}

		if ($requireCss) {
			$this->requireCss();
		}

		// Try to load component's language file just in case the module needs it
		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);
	}

	/**
	 * Load helpers for the module
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function html()
	{
		$theme = EB::themes();
		$args = func_get_args();

		$output = call_user_func_array(array($theme, 'html'), $args);

		return $output;
	}

	/**
	 * Initialize scripts for modules
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function requireScripts()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			// Ensure that scripts are loaded
			EB::init('site');


			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Initialize modules
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function requireCss()
	{
		static $loaded = null;

		if (is_null($loaded)) {

			$stylesheet = EB::stylesheet('site', $this->config->get('theme_site'));
			$stylesheet->attach();

			// Attach fontawesome stylesheet if needed to
			if ($this->config->get('css_fontawesome')) {
				$stylesheet->attachFontawesome();
			}

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Render site stylesheet
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderSiteStylesheet()
	{
		return $this->requireCss();
	}

	/**
	 * Normalize comma separated values or arrays into proper quote
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function join($items)
	{
		$db = EB::db();

		if (!is_array($items)) {
			$items = str_replace(' ', '', $items);
			$items = explode(',', $items);
		}

		$temp = array();

		foreach ($items as $item) {
			$temp[] = $db->quote($item);
		}

		$result = implode(',', $temp);

		return $result;
	}

	/**
	 * Formats the post items from modules
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function processItems($data)
	{
		$result = [];

		if (!$data) {
			return $result;
		}

		$posts = EB::formatter('list', $data, false);

		foreach ($posts as $post) {
			// Default media items
			$post->media = '';

			// If the post doesn't have a blog image try to locate for an image
			if ($post->posttype == 'standard' && !$post->hasImage()) {

				$photoWSize = $this->params->get('photo_width', 250);
				$photoHSize = $this->params->get('photo_height', 250);
				$size = array('width' => $photoWSize, 'height' => $photoHSize);

				$post->media = $this->getMedia($post, $this->params, $size);
			} else {
				$post->media = $post->getImage();
			}

			// Get the comment count
			$post->commentCount = $post->getTotalComments();

			// Determines if this post is password protected or not.
			$requireVerification = false;

			if ($this->config->get('main_password_protect', true) && $post->isPasswordProtected()) {
				$post->title = JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $post->title);
				$requireVerification = true;
			}

			$post->showRating = true;
			$post->protect = false;
			$post->summary = '';

			// Only if verification is necessary, we do not want to show the content
			if ($requireVerification && !EB::verifyBlogPassword($post->blogpassword, $post->id)) {
				$theme = EB::themes();
				$post->summary = $theme->html('post.protectedPost', $post);

				$post->showRating = false;
				$post->protect = true;
			}

			if (!$post->protect) {
				// Determines the content source
				$contentSource = $this->params->get('showintro', -1);

				// Determines if we should trigger plugins
				$triggerPlugins = $this->params->get('trigger_plugins', false);

				// Display only the intro
				if ($contentSource == self::SOURCE_INTRO) {
					$options = array('skipAudio' => true, 'skipImage' => true, 'triggerPlugins' => $triggerPlugins, 'fromModule' => true);

					$post->summary = $post->getIntro(self::POST_STRIP_TAGS, self::POST_TRUNCATE, self::POST_SOURCE_INTRO_COLUMN, null, $options);
				}

				// Display the main content without intro
				if ($contentSource == self::SOURCE_CONTENT) {
					$post->summary = $post->getContentWithoutIntro('list', $triggerPlugins);
				}

				// Checks if this post have a video embedded using legacy video.
				// If true, properly process the video and get the link.
				$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
				preg_match_all($pattern, $post->summary, $matches, PREG_SET_ORDER);

				if ($matches) {
					$post->summary = EB::videos()->processVideos($post->summary);
				}

				// Truncation settings
				$maxLength = $this->params->get('textcount', 0);
				$length = EBString::strlen($post->summary);
				$autoTruncate = ($maxLength && $length > $maxLength && !$post->protect);

				// Remove any html codes from the content
				$stripTags = $this->params->get('striptags', true);

				if ($stripTags || $autoTruncate) {
					// Since we want to strip tags the content,
					// it is safe to remove the image
					// This is to prevent image caption from appearing in the content #1067
					$media = $post->getMedia();

					// Strip out known image codes
					if ($media->images) {
						// foreach ($media->images as $item) {
						// 	$post->summary = EBString::str_ireplace($item->html, '', $post->summary);
						// }

						// remove image caption before we strip tags #1067, #1548, #1676
						$pattern = '/<div class="eb-image-caption".*?>[\s\S]+?<\/div>/i';

						$post->summary = preg_replace($pattern, '', $post->summary);
					}

					$post->summary = EB::truncater()->strip_only($post->summary, '<script>', true);
					$post->summary = EB::truncater()->strip_only($post->summary, '<object>', true);
					$post->summary = EB::truncater()->stripTags($post->summary);
					$post->summary = EB::string()->trimEmptySpace($post->summary);
				}

				if ($autoTruncate) {
					$post->summary = EBString::substr($post->summary, 0, $maxLength) . JText::_('COM_EASYBLOG_ELLIPSES');
				}
			}

			// Get the photo cover of the post
			$post->cover = $this->getPostCover($post);

			$post->isCoverImage = $post->cover && (EB::image()->isImage($post->cover) || $post->isExternalImageCover($post->cover) || $post->isUsingFirstImage);
			$post->videoEmbedCover = !$post->isCoverImage && $post->isEmbedCover() ? $post->getEmbedCover() : '';
			$post->videoCover = !$post->isCoverImage ? EB::media()->renderVideoPlayer($post->cover, [
				'ratio' => '',
				'muted' => false,
				'autoplay' => false,
				'loop' => false
			], false) : '';

			$result[] = $post;
		}

		return $result;
	}

	/**
	 * Retrieves the post cover for a post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPostCover(EasyBlogPost $post)
	{
		$cover = false;
		$size = $this->params->get('photo_size', 'thumbnail');

		$oldSizes = ['icon', 'small', 'medium'];

		// Backward compatibility
		if (in_array($size, $oldSizes)) {
			$size = 'thumbnail';
		}

		$useFirstImage = $this->params->get('photo_legacy', true);
		$showPlaceholder = $this->params->get('show_photo_placeholder', false);

		// This is the standard method when authors adds a post cover on the post
		if ($post->hasImage()) {
			$cover = $post->getImage($size);
		}

		// Get the first image to be used as the post cover
		if (!$post->hasImage() && $useFirstImage) {
			$cover = $post->getFirstImage($size, true);

			if ($cover) {
				$post->isUsingFirstImage = true;
			}
		}

		if (!$cover) {
			$cover = $post->getImage($size, $showPlaceholder, true, $useFirstImage);
		}

		return $cover;
	}

	/**
	 * Retrieves the cover layout settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCoverLayout()
	{
		$layout = $this->params->get('photo_layout');

		return $layout;
	}

	/**
	 * Retrieves the cover alignment settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCoverAlignment()
	{
		$layout = $this->getCoverLayout();
		$alignment = isset($layout->alignment) ? $layout->alignment : 'center';

		// If the full width photo is enabled, we strict the alignment to use center
		if (isset($layout->full)) {
			$alignment = 'center';
		}

		return $alignment;
	}

	/**
	 * Retrieve the media item from a post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getMedia(&$row, $size = array())
	{
		$media = '';
		$type = 'image'; //default to image only.

		switch($type)
		{
			case 'video':
				$row->intro = EB::videos()->processVideos($row->intro);
				$row->content = EB::videos()->processVideos($row->content);

				break;
			case 'audio':
				$row->intro = EB::audio()->process($row->intro);
				$row->content = EB::audio()->process($row->content);
				break;
			case 'image':

				$imgSize = '';
				if (!empty($size)) {
					if (isset($size['width']) && isset($size['height'])) {
						$width = $size[ 'width' ] != 'auto' ? $size['width'] . 'px' : 'auto';
						$height = $size[ 'height' ] != 'auto' ? $size['height'] . 'px' : 'auto';

						$imgSize = ' style="width: ' . $width . ' !important; height:' . $height . ' !important;"';
					}
				}

				if ($row->getImage()) {

					$mediaSize = $this->params->get('photo_size', 'small');
					$media = $row->getImage($mediaSize);

					if (!empty($imgSize)) {
						$media  = str_replace('<img', '<img ' . $imgSize . ' ', $media);
					}
				}

				if (empty($media)) {
					$media = self::getFeaturedImage($row);
					if (!empty($imgSize)) {
						$media  = str_replace('<img', '<img ' . $imgSize . ' ', $media);
					}
				} else {
					$media = '<img src=" ' . $media . '" class="blog-image" style="margin: 0 5px 5px 0;border: 1px solid #ccc;padding:3px;" ' .$imgSize.'/>';
				}

				break;
			default:
				break;
		}

		if ($type != 'image') {
			// remove images.
			$pattern = '#<img[^>]*>#i';
			preg_match($pattern , $row->intro . $row->content , $matches);
			if (isset($matches[0])) {
				// After extracting the image, remove that image from the post.
				$row->intro = str_ireplace($matches[0] , '' , $row->intro);
				$row->content = str_ireplace($matches[0] , '' , $row->intro);
			}
		}

		return $media;
	}


	public static function getFeaturedImage(&$row)
	{
		$pattern = '#<img class="featured"[^>]*>#i';
		$content = $row->intro . $row->content;

		preg_match($pattern , $content , $matches);

		if (isset($matches[0])) {
			return self::getResizedImage($matches[0]);
		}

		// If featured image is not supplied, try to use the first image as the featured post.
		$pattern = '#<img[^>]*>#i';

		preg_match($pattern , $content , $matches);

		if (isset($matches[0])) {
			// After extracting the image, remove that image from the post.
			$row->intro = str_ireplace($matches[0] , '' , $row->intro);
			$row->content = str_ireplace($matches[0] , '' , $row->intro);

			return self::getResizedImage($matches[0]);
		}

		// If all else fail, try to use the default image
		return false;
	}


	/**
	 * Retrieves the layout set in the module
	 *
	 * @since	5.4.4
	 * @access	public
	 */
	public function getLayout($default = 'default')
	{
		$layout = $this->params->get('layout', $default);

		$output = JModuleHelper::getLayoutPath($this->name, $layout);

		return $output;
	}

	public static function getResizedImage($img)
	{
		preg_match('/src= *[\"¦\']{0,1}([^\"\'\>]*)/i' , $img , $matches);

		if (!isset($matches[ 1 ])) {
			return $img;
		}

		// We find the thumb and make it a popup
		if (stristr($matches[1] , 'thumb_') === false) {
			return $img;
		}

		// Test if the full image exists.
		jimport('joomla.filesystem.file');

		$info = pathinfo($matches[ 1 ]);

		$thumb = JPATH_ROOT . DIRECTORY_SEPARATOR . str_ireplace('/' , DIRECTORY_SEPARATOR , $matches[ 1 ]);
		$full = str_ireplace('thumb_' , '' , $thumb);

		if (!JFile::exists($full)) {
			return $img;
		}

		return '<a href="' . str_ireplace('thumb_' , '' , $matches[1]) . '" class="easyblog-thumb-preview">'
			 . $img . '</a>';
	}

	/**
	 * Renders the wrapper class for a module
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getWrapperClass()
	{
		$class = '';

		// This must always be first
		if ($this->params->get('moduleclass_sfx', '')) {

			// Add additional spacing before attaching module class suffix. #596
			$class .= ' ' . trim($this->params->get('moduleclass_sfx'));
		}

		if ($this->isMobile()) {
			$class .= ' is-mobile';
		}

		if ($this->isTablet()) {
			$class .= ' is-tablet';
		}

		return $class;
	}

	public static function getThumbnailImage($img)
	{
		$srcpattern = '/src=".*?"/';

		preg_match($srcpattern , $img , $src);

		if (isset($src[0])) {
			$imagepath = trim(str_ireplace('src=', '', $src[0]) , '"');
			$segment = explode('/', $imagepath);
			$file = end($segment);
			$thumbnailpath = str_ireplace($file, 'thumb_'.$file, implode('/', $segment));

			if (!JFile::exists($thumbnailpath)) {
				$image = EB::imagelib();
				$image->load($imagepath);
				$image->resize(64, 64);
				$image->save($thumbnailpath);
			}

			$newSrc = 'src="'.$thumbnailpath.'"';
		} else {
			return false;
		}

		$oldAttributes = array('src'=>$srcpattern, 'width'=>'/width=".*?"/', 'height'=>'/height=".*?"/');
		$newAttributes = array('src'=>$newSrc,'width'=>'', 'height'=>'');

		return preg_replace($oldAttributes, $newAttributes, $img);
	}

	/**
	 * Replace images
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function replaceImage($post)
	{
		$content = $post->content;

		if (!$post->protect) {
			$content = $post->summary;
		}

		// Legacy post
		if ($post->isLegacy()) {
			preg_match("/<img[^>]+\>/i", $content, $matches);

			if ($matches) {
				$content = str_replace($matches[0], '', $content);
			}
		}

		// If this post is EBD, we need to remove the whole image block
		if ($post->isEbd()) {
			$content = self::replaceEbdImage($post, $content);
		}

		return $content;
	}

	/**
	 * Find EBD image and replace it
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function replaceEbdImage($post, $content)
	{
		$imageHTML = '';

		$doc = EB::document($post->document);

		// If there is no blocks under the intro property, try to use the blocks from content property.
		$blocks = $doc->content;

		$nestedBlocks = array();
		$nestedBlocksUid = array();

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

		$imageBlocks = array();

		// Go through each of the nested blocks
		if ($nestedBlocks) {
			foreach ($nestedBlocks as $nestedBlock) {
				if ($nestedBlock->type == 'image') {
					$imageBlocks[] = $nestedBlock;
					break;
				}
			}
		}

		// Go through each of the main blocks
		foreach ($blocks as $block) {
			if ($block->type == 'image') {
				$imageBlocks[] = $block;
				break;
			}
		}

		// Get the first image block available
		if ($imageBlocks) {
			foreach ($imageBlocks as $imageBlock) {
				$imageHTML = EB::blocks()->renderViewableBlock($imageBlock);
				break;
			}
		}

		if ($imageHTML) {
			$content = str_replace($imageHTML, '', $content);
		}

		return $content;
	}

	/**
	 * Format posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function formatPost(&$posts)
	{
		// Removing duplicate images for legacy image behavior.
		$images = array();

		foreach ($posts as $post) {
			if ($post->posttype != 'quote' && $this->params->get('showintro', '-1') != '-1') {
				if ($this->params->get('photo_legacy', 0)) {
					if ($post->protect) {
						$post->content = self::replaceImage($post);
					}

					if (!$post->protect) {
						$post->summary = self::replaceImage($post);
					}
				}
			}
		}
	}
}
