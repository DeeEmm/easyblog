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

class EasyBlogFormatterEntry extends EasyBlogFormatterStandard
{
	public function execute()
	{
		$post = $this->items;

		// Get the blogger object
		$post->creator = $post->getAuthor();
		$post->author = $post->creator; // to avoid issue in template override #519

		// Determines if the post is featured
		$post->isFeatured = $post->isFeatured();

		// determines if this is a teamblog post
		$post->team_id = ($post->source_type == EASYBLOG_POST_SOURCE_TEAM) ? $post->source_id : '0';

		// Format microblog postings
		if ($post->posttype) {
			$this->formatMicroblog($post);
		} else {
			$post->posttype = 'standard';
		}

		// We want to format all the content first before the theme displays the content

		// 20160-06-13, callling getContent here cause content plugin to triggered twice. Since in entry page
		// we use getContent() / getContentWithoutIntro() to output, we no longer need this post->text.
		// $post->text = $post->getContent('entry');

		// Get the total comments
		$post->totalComments = EB::comment()->getCommentCount($post);

		// Get custom fields for this blog post
		$post->fields = $post->getCustomFields();

		// Assign the category object into the blog
		$post->categories = $post->getCategories();
		$post->category = $post->getPrimaryCategory();

		// Get the post assets
		$post->assets = $post->getAssets();

		// Retrieve list of tags
		$post->tags = $post->getTags();

		return $post;
	}
}
