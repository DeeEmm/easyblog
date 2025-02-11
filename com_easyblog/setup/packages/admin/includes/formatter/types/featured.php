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

class EasyBlogFormatterFeatured extends EasyBlogFormatterStandard
{
	public function execute()
	{
		if ($this->cache) {
			//preload posts information
			EB::cache()->insert($this->items);
		}

		// For featured items we wouldn't want to process comments
		$comment = true;

		// For featured items we want to remove featured image
		$removeFeaturedImage	= true;

		// For featured items we do not want to load videos
		$video = false;

		// For featured items we do not want to process gallery
		$gallery = false;

		// Ensure that the content does not exceed the introtext limit for featured items
		$contentLimit 	= $this->config->get('layout_featured_intro_limit');

		$result = array();

		foreach ($this->items as &$item) {
			$blog = EB::post($item->id);

			// Load the author's profile
			$author = EB::user($blog->created_by);

			// @Assign dynamic properties that must exist everytime formatBlog is called
			// We can't rely on ->author because CB plugins would mess things up.
			$blog->creator = $author;
			$blog->blogger = $author;

			// to avoid issue in template override #519
			$blog->author = $blog->creator;

			// Password verifications
			$this->password($blog);

			// Format microblog postings
			if ($blog->posttype) {
				$this->formatMicroblog($blog);
			}

			// Detect if content requires read more link
			$blog->readmore = $this->hasReadmore($blog);

			// Prepare nice date for the featured area
			$blog->date	= EB::date($blog->created)->format(JText::_('DATE_FORMAT_LC'));

			$result[] = $blog;
		}

		return $result;
	}
}
