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

class EasyBlogFormatterPost extends EasyBlogFormatterStandard
{
	public function execute()
	{
		$table = $this->items;

		$blog = new stdClass();

		// Post details
		$blog->id = $table->id;
		$blog->title = $table->title;
		$blog->content = $table->content;
		$blog->content_plain = $this->sanitize($blog->content);
		$blog->image = $table->getImage('thumbnail');
		$blog->created = $table->created;
		$blog->hits = $table->hits;
		$blog->permalink = $table->getPermalink(true, true) . '?format=json';


		// Get the author details
		$author = $table->getAuthor();
		$blog->creator = new stdClass();
		$blog->creator->name = $author->getName();
		$blog->creator->avatar = $author->getAvatar();

		// alias incase 3rd party still referencing author property
		$blog->author = $blog->creator;

		// Get the category details
		$category = $table->getPrimaryCategory();
		$blog->category = new stdClass();
		$blog->category->id = $category->id;
		$blog->category->title = $category->getTitle();
		$blog->category->avatar = $category->getAvatar();

		return $blog;
	}
}
