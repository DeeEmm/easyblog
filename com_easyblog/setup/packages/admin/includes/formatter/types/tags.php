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

class EasyBlogFormatterTags extends EasyBlogFormatterStandard
{
	public function execute()
	{
		if (!$this->items) {
			return $this->items;
		}

		$tags = array();

		$cacheLib = EB::cache();

		foreach ($this->items as $row) {

			// We want to load the table objects
			$tag = EB::table('Tag');
			$tag->bind($row);

			// binding the extra info
			if (isset($row->post_count)) {
				$tag->post_count = $row->post_count;
			}

			if ($this->cache) {
				// cache tag jtable
				$cacheLib->set($tag, 'tag');
			}

			$tags[] = $tag;
		}

		return $tags;
	}
}
