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

class EasyBlogThemesHelperPostList
{
	/**
	 * Renders the post actions on listing page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function authorAvatar(EasyBlogPost $post)
	{
		static $cache = [];

		// If it is already cached, we don't need to reload to get the author
		$author = isset($post->creator) ? $post->creator : $post->getAuthor();

		$index = md5($author->id . $post->getAuthorPermalink() . $post->getAuthorName());

		if (!isset($cache[$index])) {
			$theme = EB::themes();
			$theme->set('post', $post);
			$theme->set('author', $author);
			$cache[$index] = $theme->output('site/helpers/post/list/author.avatar');
		}

		return $cache[$index];
	}

	/**
	 * Renders the post actions on listing page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function actions(EasyBlogPost $post, $params, $ratings = true, $hits = true, $comments = true)
	{
		$theme = EB::themes();
		$theme->set('ratings', $ratings);
		$theme->set('hits', $hits);
		$theme->set('comments', $comments);
		$theme->set('post', $post);
		$theme->set('params', $params);
		$output = $theme->output('site/helpers/post/list/actions');

		return $output;
	}

	/**
	 * Renders the intro of a post on the listing page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function content(EasyBlogPost $post, $params, $cover = true)
	{
		$content = $post->getIntro();

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('content', $content);
		$theme->set('params', $params);
		$theme->set('cover', $cover);

		$output = $theme->output('site/helpers/post/list/content');

		return $output;
	}

	/**
	 * Renders the post cover for listing page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function cover(EasyBlogPost $post, $params)
	{
		if (!$params instanceof JRegistry) {
			$params = new JRegistry($params);
		}

		$hasImage = $post->image;
		$displayCover = $params->get('post_image', true);
		$displayPlaceholder = $params->get('post_image_placeholder', false);

		// For card layouts we always need to use a placeholder
		$postStyle = $params->get('layout_style', '');

		if ($postStyle === 'card') {
			$displayPlaceholder = true;
		}

		$postImage = $post->getImage('original', $displayPlaceholder);

		// #2806 - There is a possibility that the author set a post cover on the post but later deleted the image via the media manager
		if ($post->image && $postImage === false) {
			$hasImage = false;
		}

		// Only render the cover for the list if needed
		if (
			($hasImage && $displayCover || (!$hasImage && ($post->usePostImage() || $postImage) && $displayCover)) ||
			(!$hasImage && !$post->usePostImage() && $displayPlaceholder && $displayCover)
		) {
			$config = EB::config();
			$theme = EB::themes();
			$useFirstImage = $config->get('cover_firstimage', 0);

			$isImage = EB::image()->isImage($postImage) || $post->isExternalImageCover($postImage) || $post->isUsingFirstImage;
			$isWebp = EB::image()->isWebp($postImage);
			$fullWidthCover = $config->get('cover_width_full');
			$cropCover = $config->get('cover_crop', false);
			$width = $config->get('cover_width');
			$height = $config->get('cover_height');
			$alignment = $config->get('cover_alignment');
			$coverAspectRatio = $config->get('cover_aspect_ratio');

			$cover = new stdClass();
			$cover->url = $post->getImage(EB::getCoverSize('cover_size'), $displayPlaceholder, true, $useFirstImage);
			$cover->webp = $isWebp;
			$cover->fallbackUrl = $isWebp ? $post->getImage(EB::getCoverSize('cover_size'), true, true, $useFirstImage, true) : '';
			$cover->image = $isImage;
			$cover->alt = $theme->escape($post->getCoverImageAlt());
			$cover->title = $theme->escape($post->getImageTitle());
			$cover->caption = $theme->escape($post->getImageCaption());
			$cover->video = !$isImage ? EB::media()->renderVideoPlayer($postImage, [
				'width' => '260',
				'height' => '200',
				'ratio' => '',
				'muted' => false,
				'autoplay' => false,
				'loop' => false], false) : '';
			$cover->videoEmbed = !$isImage && $post->isEmbedCover() ? $post->getEmbedCover() : '';

			// replace to use privacy enhanced mode
			$enablePrivacyEnhancedMode = $config->get('main_youtube_nocookie');

			if ($cover->videoEmbed && $enablePrivacyEnhancedMode) {
				$cover->videoEmbed = str_replace('youtube.com/', 'youtube-nocookie.com/', $cover->videoEmbed);
			}

			// Determine if we should show the cover at all.
			$showCover = false;

			if (($isImage && $cover->url) || ($post->isEmbedCover() || $cover->video)) {
				$showCover = true;
			}

			$theme->set('showCover', $showCover);
			$theme->set('cover', $cover);
			$theme->set('cropCover', $cropCover);
			$theme->set('isWebp', $isWebp);
			$theme->set('isImage', $isImage);
			$theme->set('fullWidthCover', $fullWidthCover);
			$theme->set('post', $post);
			$theme->set('params', $params);
			$theme->set('width', $width);
			$theme->set('height', $height);
			$theme->set('alignment', $alignment);
			$theme->set('coverAspectRatio', $coverAspectRatio);

			$html = $theme->output('site/helpers/post/list/cover');

			$layoutStyle = $params->get('layout_style', '');
			$availableLayoutStyles = ['card', 'simple'];

			if (in_array($layoutStyle, $availableLayoutStyles)) {
				$html = $theme->output('site/helpers/post/list/cover.' . $layoutStyle);
			}

			return $html;
		}

		return;
	}

	/**
	 * Renders a preview of comments. (This feature has been removed in v6.0.0. See #2627)
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function commentsPreview(EasyBlogPost $post, $params)
	{
		// Do not process this if theres no comments
		if (!EB::comment()->allowPreview() || !$params->get('post_comment_preview', false) || !$post->getPreviewComments($params->get('post_comment_preview_limit', 3))) {
			return;
		}

		$comments = $post->getPreviewComments($params->get('post_comment_preview_limit', 3));

		$theme = EB::themes();
		$theme->set('comments', $comments);
		$theme->set('post', $post);

		$output = $theme->output('site/helpers/post/list/comments.preview');

		return $output;
	}

	/**
	 * Renders the empty placeholder for post listings
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function emptyList($text, $icon = true)
	{
		static $cache = [];

		if ($icon === true) {
			$icon = 'fdi far fa-paper-plane';
		}

		$index = md5($text . $icon);

		if (!isset($cache[$index])) {
			$theme = EB::themes();
			$theme->set('text', $text);
			$theme->set('icon', $icon);

			$cache[$index] = $theme->output('site/helpers/post/list/empty');
		}

		return $cache[$index];
	}
	/**
	 * Renders the post footer on listing page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function footer(EasyBlogPost $post, $params)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('params', $params);
		$output = $theme->output('site/helpers/post/list/footer');

		return $output;
	}

	/**
	 * Renders a post item template on listings
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function item(EasyBlogPost $post, $style, $index, $params, $returnLink = '', $currentPageLink = '')
	{
		$config = EB::config();

		$namespace = 'site/listing/' . $style;
		$protected = false;

		if (!FH::isSiteAdmin() && $config->get('main_password_protect') && !empty($post->blogpassword) && !$post->verifyPassword()) {
			$protected = true;
		}

		$hasAdminTools = false;
		$acl = EB::acl();

		if (FH::isSiteAdmin() ||
			($post->isMine() && !$post->hasRevisionWaitingForApproval()) ||
			($post->isMine() && $acl->get('publish_entry')) ||
			($post->isMine() && $acl->get('delete_entry')) ||
			$acl->get('feature_entry') ||
			$acl->get('moderate_entry')) {
			$hasAdminTools = true;
		}

		$theme = EB::themes();
		$theme->set('protected', $protected);
		$theme->set('post', $post);
		$theme->set('hasAdminTools', $hasAdminTools);
		$theme->set('index', $index);
		$theme->set('params', $params);
		$theme->set('return', $returnLink);
		$theme->set('currentPageLink', $currentPageLink);
		$contents = $theme->output($namespace);

		return $contents;
	}

	/**
	 * Renders the meta of a post in the listing page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function meta(EasyBlogPost $post, $params)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('params', $params);
		$output = $theme->output('site/helpers/post/list/meta');

		return $output;
	}

	/**
	 * Renders the read more button on listing page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function readmore(EasyBlogPost $post)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/list/readmore');

		return $output;
	}

	/**
	 * Renders the schema for the post (ld+json)
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function schema(EasyBlogPost $post)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/list/schema');

		return $output;
	}

	/**
	 * Renders the simple listing that is used in category and authors
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function simple(EasyBlogPost $post, $dateSource, $dateFormat = 'DATE_FORMAT_LC3')
	{
		$theme = EB::themes();
		$theme->set('dateFormat', $dateFormat);
		$theme->set('dateSource', $dateSource);
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/list/simple');

		return $output;
	}

	/**
	 * Renders the title area of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function title($post, $options = [])
	{
		$type = $post->getType();

		$class = EB::normalize($options, 'class', '');

		$namespace = 'site/helpers/post/list/title/' . $type;

		$theme = EB::themes();
		$theme->set('class', $class);
		$theme->set('post', $post);
		$output = $theme->output($namespace);

		return $output;
	}
}
