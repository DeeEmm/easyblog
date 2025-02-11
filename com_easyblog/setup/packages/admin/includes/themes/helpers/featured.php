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

class EasyBlogThemesHelperFeatured
{
	/**
	 * Renders the featured slider in EasyBlog
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function slider($posts, $options = [])
	{
		if (!$posts) {
			return;
		}

		$config = EB::config();
		$totalPosts = count($posts);

		$fromModule = EB::normalize($options, 'fromModule', false);

		$slider = (object) [
			'style' => EB::normalize($options, 'style', 'default'),
			'autoplay' => EB::normalize($options, 'autoplay', true),
			'autoplayInterval' => EB::normalize($options, 'autoplayInterval', 8),
			'navigation' => EB::normalize($options, 'navigation', false) && $totalPosts > 1,
			'debug' => EB::normalize($options, 'debug', false),
			'navigationType' => EB::normalize($options, 'navigationType', 'default')
		];

		$postOption = (object) [
			'image' => EB::normalize($options, 'image', false),
			'title' => EB::normalize($options, 'postTitle', false),
			'date' => EB::normalize($options, 'postDate', false),
			'dateSource' => EB::normalize($options, 'postDateSource', 'created'),
			'category' => EB::normalize($options, 'postCategory', false),
			'content' => EB::normalize($options, 'postContent', false),
			'contentLimit' => EB::normalize($options, 'postContentLimit', false),
			'contentSource' => EB::normalize($options, 'postContentSource', 'intro'),
			'contentOptions' => EB::normalize($options, 'postContentOptions', []),
			'authorAvatar' => EB::normalize($options, 'authorAvatar', false),
			'authorTitle' => EB::normalize($options, 'authorTitle', false),
			'readmore' => EB::normalize($options, 'readmore', false),
			'ratings' => EB::normalize($options, 'ratings', false) && $config->get('main_ratings')
		];

		$coverOption = (object) [
			'variation' => EB::normalize($options, 'imageVariation', null),
			'alignment' => EB::normalize($options, 'coverAlignment', $config->get('cover_featured_alignment')),
			'crop' => EB::normalize($options, 'cropCover', $config->get('cover_featured_crop')),
			'width' => EB::normalize($options, 'coverWidth', $config->get('cover_featured_width')),
			'height' => EB::normalize($options, 'coverHeight', $config->get('cover_featured_height')),
			'showPlaceholder' => EB::normalize($options, 'showPlaceholder', true),
			'pickFirstImage' => EB::normalize($options, 'pickFirstImage', $config->get('cover_firstimage'))
		];

		// Generate the default covers
		$defaults = (object) [
			'video' => EB::getPlaceholderImage(false, 'video'),
			'image' => EB::getPlaceholderImage(false, 'image')
		];

		$imageVariation = $coverOption->variation && !in_array($coverOption->variation, EB::getDeprecatedImageSizes()) ? $coverOption->variation : EB::getCoverSize('cover_featured_size');

		$truncate = false;

		if ($postOption->contentLimit) {
			$truncate = true;
		}

		// Format the post
		foreach ($posts as &$post) {
			$post->coverImage = '';

			// For embedded post covers, we assume they are all videos
			if ($post->isVideoCover()) {
				$post->coverImage = $defaults->video;
			}

			// For image covers, try to get an image, be it a placeholder
			if ($post->isImageCover()) {
				$post->coverImage = $post->getImage($imageVariation, $coverOption->showPlaceholder, true, $coverOption->pickFirstImage);
			}

			if ($postOption->contentSource === 'intro') {
				$post->displayContent = $post->getIntro(false, $truncate, 'intro', null, array_merge([
					'triggerPlugins' => false,
					'forceTruncateByChars' => true,
					'forceCharsLimit' => $postOption->contentLimit
				], $postOption->contentOptions));
			}

			if ($postOption->contentSource === 'content') {
				$postType = $fromModule ? 'list' : 'entry';
				$postOption->contentOptions = $fromModule ? array_merge([
					'triggerPlugins' => false,
					'forceTruncateByChars' => true,
					'forceCharsLimit' => $postOption->contentLimit,
					'truncate' => (int) $postOption->contentLimit === 0 ? false : true
				], $postOption->contentOptions) : $postOption->contentOptions;

				$post->displayContent = $post->getContentWithoutIntro($postType, true, $postOption->contentOptions);
			}
		}

		$themes = EB::themes();
		$themes->set('posts', $posts);
		$themes->set('slider', $slider);
		$themes->set('postOption', $postOption);
		$themes->set('coverOption', $coverOption);

		$namespace = 'site/helpers/featured/slider/' . $slider->style;
		$output = $themes->output($namespace);

		return $output;
	}
}