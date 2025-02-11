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

class EasyBlogThemesHelperPostEntry
{
	/**
	 * Renders the author box section on the entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function authorBox(EasyBlogPost $post, $params)
	{
		// Determines if the viewer has subscribed to the author
		$subscribed = false;

		$config = EB::config();

		if ($config->get('main_bloggersubscription')) {
			$my = JFactory::getUser();

			$model = EB::model('Blogger');
			$subscribed = $model->isBloggerSubscribedEmail($post->getAuthor()->id, $my->email);
		}

		$recentPosts = [];
		$postParams = $post->getMenuParams();

		if ($postParams->get('post_author_recent', true)) {
			$limit = (int) $postParams->get('post_author_recent_limit', 5);
			if ($limit < 0) {
				$limit = 5;
			}

			$model = EB::model('Blogger');
			$recentPosts = $model->getRecentPosts($post->getAuthor()->id, $limit, $post->id);

			EB::cache()->insert($recentPosts);
		}

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('params', $params);
		$theme->set('subscribed', $subscribed);
		$theme->set('recentPosts', $recentPosts);

		$output = $theme->output('site/helpers/post/entry/author.box');

		return $output;
	}

	/**
	 * Renders the fontsize tool on the entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function fontsize(EasyBlogPost $post)
	{
		$theme = EB::themes();
		$theme->set('post', $post);

		$output = $theme->output('site/helpers/post/entry/fontsize');

		return $output;
	}

	/**
	 * Renders the post cover for entry page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function cover(EasyBlogPost $post, $options = [])
	{
		$showCover = EB::normalize($options, 'showCover', false);
		$showCoverPlaceholder = EB::normalize($options, 'showCoverPlaceholder', false);

		$hasCover = $post->image;

		// Maybe the post has default category cover
		$postCover = $post->getImage('original', $showCoverPlaceholder, false, false);

		$showCover = ((($hasCover) || (!$hasCover && ($postCover || $showCoverPlaceholder))) && $showCover);

		// #2806 - There is a possibility that the author set a post cover on the post but later deleted the image via the media manager
		if ($post->image && $postCover === false && !$showCoverPlaceholder) {
			$showCover = false;
		}

		if (!$showCover) {
			return;
		}

		$config = EB::config();
		$themes = EB::themes();

		$fullWidthCover = $config->get('cover_width_entry_full');
		$width = $config->get('cover_width_entry');
		$height = $config->get('cover_height_entry');
		$cropCover = $config->get('cover_crop_entry', false);
		$alignment = $config->get('cover_alignment_entry');

		$isImage = EB::image()->isImage($postCover) || $post->isExternalImageCover($postCover);
		$isWebp = EB::image()->isWebp($postCover);

		$cover = new stdClass();
		$cover->title = $themes->escape($post->getImageTitle());
		$cover->caption = $themes->escape($post->getImageCaption());
		$cover->alt = $themes->escape($post->getCoverImageAlt());
		$cover->isWebp = $isWebp;
		$cover->originalUrl = $post->getImage('original', $showCoverPlaceholder, false, false);
		$cover->url = $post->getImage(EB::getCoverSize('cover_size_entry'), $showCoverPlaceholder, false, false);
		$cover->fallbackUrl = $isWebp ? $post->getImage(EB::getCoverSize('cover_size_entry'), true, false, false, true) : '';
		$cover->video = !$isImage ? EB::media()->renderVideoPlayer($postCover, ['width' => '260', 'height' => '200', 'ratio' => '', 'muted' => false, 'autoplay' => false, 'loop' => false], false) : '';
		$cover->videoEmbed = !$isImage && $post->isEmbedCover() ? $post->getEmbedCover() : '';

		// replace to use privacy enhanced mode
		$enablePrivacyEnhancedMode = $config->get('main_youtube_nocookie');

		if ($cover->videoEmbed && $enablePrivacyEnhancedMode) {
			$cover->videoEmbed = str_replace('youtube.com/', 'youtube-nocookie.com/', $cover->videoEmbed);
		}

		$themes->set('post', $post);
		$themes->set('postCover', $postCover);
		$themes->set('fullWidthCover', $fullWidthCover);
		$themes->set('width', $width);
		$themes->set('height', $height);
		$themes->set('alignment', $alignment);
		$themes->set('cropCover', $cropCover);
		$themes->set('cover', $cover);
		$themes->set('isImage', $isImage);

		$html = $themes->output('site/helpers/post/entry/cover');

		return $html;
	}

	/**
	 * Renders the content of the post for entry page
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function content(EasyBlogPost $post, $content, $options = [])
	{
		if (!in_array($post->getType(), ['photo', 'standard', 'twitter', 'email', 'link', 'video'])) {
			return;
		}

		$showCover = EB::normalize($options, 'showCover', false);
		$showCoverPlaceholder = EB::normalize($options, 'showCoverPlaceholder', false);
		$requireLogin = EB::normalize($options, 'requireLogin', false);
		$preview = EB::normalize($options, 'preview', false);

		$themes = EB::themes();
		$themes->set('post', $post);
		$themes->set('content', $content);
		$themes->set('preview', $preview);
		$themes->set('requireLogin', $requireLogin);
		$themes->set('showCover', $showCover);
		$themes->set('showCoverPlaceholder', $showCoverPlaceholder);

		$output = $themes->output('site/helpers/post/entry/content');

		return $output;
	}

	/**
	 * Renders the comments section on the entry
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function comments(EasyBlogPost $post)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/entry/comments');

		return $output;
	}

	/**
	 * Renders the moderation box for entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function moderate(EasyBlogPost $post)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/entry/moderate');

		return $output;
	}

	/**
	 * Renders the authors section of a post in the entry
	 *
	 * @since	6.0.10
	 * @access	public
	 */
	public function authors(EasyBlogPost $post, $params)
	{
		$config = EB::config();

		if (!$config->get('reviewer_fact_checker_enabled')) {
			return;
		}

		$reviewerName = $post->getReviewerName();
		$factCheckerName = $post->getFactCheckerName();

		// Do not proceed if both of these are empty
		if (!$reviewerName && !$factCheckerName) {
			return;
		}

		$authorName = $post->getAuthorName();
		$authorPermalink = $post->getAuthorPermalink();
		$reviewerLink = $post->getReviewerLink();
		$factCheckerLink = $post->getFactCheckerLink();

		$themes = EB::themes();
		$themes->set('authorName', $authorName);
		$themes->set('authorPermalink', $authorPermalink);
		$themes->set('reviewerName', $reviewerName);
		$themes->set('reviewerLink', $reviewerLink);
		$themes->set('factCheckerName', $factCheckerName);
		$themes->set('factCheckerLink', $factCheckerLink);
		$themes->set('params', $params);

		$output = $themes->output('site/helpers/post/entry/authors');

		return $output;
	}

	/**
	 * Renders the meta of a post in the entry
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function meta(EasyBlogPost $post, $params)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('params', $params);

		$output = $theme->output('site/helpers/post/entry/meta');

		return $output;
	}

	/**
	 * Renders the navigation of a post in the entry
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function navigation(EasyBlogPost $post, $navigation)
	{
		if (empty($navigation->prev) && empty($navigation->next)) {
			return;
		}

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('navigation', $navigation);
		$output = $theme->output('site/helpers/post/entry/navigation');

		return $output;
	}

	/**
	 * Renders the print action on the entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function printer(EasyBlogPost $post)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/entry/print');

		return $output;
	}

	/**
	 * Renders the reading progress bar in the entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function progress()
	{
		$theme = EB::themes();
		$output = $theme->output('site/helpers/post/entry/progress');

		return $output;
	}

	/**
	 * Renders the preview notices on the entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function preview(EasyBlogPost $post)
	{
		$revisions = [];
		$namespace = 'unpublished';

		if (!$post->canModerate() && $post->isPending()) {
			$namespace = 'pending';
		}

		if ($post->isPostPublished()) {
			$namespace = 'revision';
		}

		$themes = EB::themes();
		$themes->set('post', $post);

		if ($namespace === 'unpublished') {
			$revisions = $post->getRevisions();

			$themes->set('revisions', $revisions);
		}

		$output = $themes->output('site/helpers/post/entry/previews/' . $namespace);

		return $output;
	}

	/**
	 * Renders the reading progress bar in the entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function related(EasyBlogPost $post, $options = [])
	{
		$params = $post->getMenuParams();

		$showCover = EB::normalize($options, 'showCover', $params->get('post_related_image', true));
		$behavior = EB::normalize($options, 'behavior', $params->get('post_related_behavior', 'tags'));
		$ordering = EB::normalize($options, 'ordering', $params->get('post_related_ordering', 'created'));
		$sorting = EB::normalize($options, 'sorting', $params->get('post_related_sort', 'desc'));
		$newNess = EB::normalize($options, 'newness', $params->get('post_related_interval', 180));
		$limit = (int) EB::normalize($options, 'limit', $params->get('post_related_limit', 5));
		if ($limit < 0) {
			$limit = 5;
		}

		$options = [
			'ordering' => $ordering,
			'sort' => $sorting,
			'newness' => $newNess
		];

		$model = EB::model('Blog');
		$posts = $model->getRelatedPosts($post->id, $limit, $behavior, $post->getPrimaryCategory()->id, $post->getTitle(), $options);

		if (!$posts) {
			return;
		}

		$config = EB::config();
		$theme = EB::themes();
		$useFirstImage = $config->get('cover_firstimage', 0);
		$coverAspectRatio = $config->get('cover_aspect_ratio');

		// Format the related posts image
		foreach ($posts as $post) {
			$postImage = $post->getImage('thumbnail', true, true);
			$isWebp = EB::image()->isWebp($postImage);
			$isImage = EB::image()->isImage($postImage) || EB::unsplash()->isValidUrl($postImage);

			$cover = new stdClass();
			$cover->url = $post->getImage('thumbnail', true, true);
			$cover->webp = $isWebp;
			$cover->fallbackUrl = $isWebp ? $post->getImage(EB::getCoverSize('cover_size'), true, true, $useFirstImage, true) : '';
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

			$post->cover = $cover;
			$post->isImage = $isImage;
		}

		$theme = EB::themes();
		$theme->set('showCover', $showCover);
		$theme->set('posts', $posts);
		$theme->set('coverAspectRatio', $coverAspectRatio);
		$output = $theme->output('site/helpers/post/entry/related');

		return $output;
	}

	/**
	 * Renders the schema for the post (ld+json)
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function schema(EasyBlogPost $post, $ratings = false, $options = [])
	{
		$config = EB::config();

		$isPreview = EB::normalize($options, 'isPreview', false);
		$showPostRatings = EB::normalize($options, 'showPostRatings', false);
		$totalRatings = EB::normalize($options, 'totalRatings', 0);

		if (!$config->get('main_ratings') || ($isPreview || !$showPostRatings || $totalRatings <= 0)) {
			$ratings = false;
		}

		// Get the content for the Schema.org
		$schemaContent = $post->getContent(EASYBLOG_VIEW_ENTRY, false, null, ['isPreview' => true, 'ignoreCache' => true]);

		// We don't want to load any module in schema.
		$schemaContent = $post->removeLoadmodulesTags($schemaContent);

		// We also don't want to load any 3rd party tags in the schema as well
		$schemaContent = $post->remove3rdPartyTags($schemaContent);

		$themes = EB::themes();
		$themes->set('ratings', $ratings);
		$themes->set('post', $post);
		$themes->set('schemaContent', $schemaContent);
		$output = $themes->output('site/helpers/post/entry/schema');

		return $output;
	}

	/**
	 * Renders the tools that appear on the entry view (font size, report, print)
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function report(EasyBlogPost $post)
	{
		$config = EB::config();
		$my = JFactory::getUser();

		if (!$config->get('main_reporting') || ($my->guest && !$config->get('main_reporting_guests'))) {
			return;
		}

		$theme = EB::themes();
		$theme->set('post', $post);

		$output = $theme->output('site/helpers/post/entry/report');

		return $output;
	}

	/**
	 * Renders the restriction section on the entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function restricted(EasyBlogPost $post)
	{
		$currentUri = $post->getPermalink(true, true);
		$loginLink = EB::getLoginLink(base64_encode($currentUri));

		$theme = EB::themes();
		$theme->set('loginLink', $loginLink);

		$output = $theme->output('site/helpers/post/entry/restricted');

		return $output;
	}

	/**
	 * Renders the reading time required for a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function readingTime(EasyBlogPost $post)
	{

		$theme = EB::themes();
		$theme->set('post', $post);

		$output = $theme->output('site/helpers/post/entry/reading.time');

		return $output;
	}

	/**
	 * Renders the title area of a post for the entry area
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function title($post, $options = [])
	{
		$class = EB::normalize($options, 'class', '');
		$type = $post->getType();

		if (in_array($type, ['photo', 'standard', 'video', 'email', 'link'])) {
			$type = 'standard';
		}

		$namespace = 'site/helpers/post/entry/title/' . $type;

		$theme = EB::themes();
		$theme->set('class', $class);
		$theme->set('post', $post);
		$output = $theme->output($namespace);

		return $output;
	}

	/**
	 * Renders the unpublished box for entry view
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function unpublished(EasyBlogPost $post)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/entry/unpublished');

		return $output;
	}
}
