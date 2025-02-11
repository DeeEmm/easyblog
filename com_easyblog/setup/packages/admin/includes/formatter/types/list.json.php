<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogFormatterList extends EasyBlogFormatterStandard
{
	public function execute()
	{

		if ($this->cache) {
			// Cache data and preload posts
			EB::cache()->insert($this->items);
		}

		// For list items we wouldn't want to process comments
		$comment = true;

		// For list items we do not want to load videos
		$video = false;

		// For list items we do not want to process gallery
		$gallery = false;

		// Result
		$result = array();

		// Load up the tags model
		$tagsModel = EB::model('PostTag');

		foreach ($this->items as $item) {

			$post = EB::post($item->id);

			$blog = new stdClass();

			// Post details
			$blog->id = $post->id;
			$blog->title = $post->title;
			$blog->intro = $post->getIntro();
			$blog->content = $post->getContent();
			$blog->content_plain = $this->sanitize($blog->content);
			$blog->image = $post->getImage('thumbnail');
			$blog->created = $post->created;
			$blog->hits = $post->hits;
			$blog->permalink = $post->getPermalink(true, true, 'json');

			// Get the author details
			$author = $post->getAuthor();
			$blog->creator = new stdClass();
			$blog->creator->name = $author->getName();
			$blog->creator->avatar = $author->getAvatar();

			// alias incase 3rd party still referencing author property
			$blog->author = $blog->creator;

			// Get the tags for this post
			$tags = $post->getTags();
			$blog->tags = array();

			if ($tags) {
				foreach ($tags as $tag) {
					$item = new stdClass();
					$item->title = $tag->getTitle();
					$item->permalink = $tag->getExternalPermalink('json');

					$blog->tags[] = $item;
				}
			}
			// Get the category details
			$category = $post->getPrimaryCategory();
			$blog->category = new stdClass();
			$blog->category->id = $category->id;
			$blog->category->title = $category->getTitle();
			$blog->category->avatar = $category->getAvatar();
			$blog->category->permalink = $category->getExternalPermalink('json');

			$result[]	= $blog;
		}

		return $result;
	}
}
