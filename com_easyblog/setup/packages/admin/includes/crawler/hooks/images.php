<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogCrawlerImages
{	
	/**
	 * Ruleset to process document image
	 *
	 * @since	5.1
	 * @access	public
	 */ 	
	public function process($parser, &$contents, $uri)
	{
		$result = array();

		if (!$parser) {
			return $result;
		}
		
		// Find all image tags on the page.
		$images = $parser->find('img');

		foreach ($images as $image) {

			if (!$image->src) {
				continue;
			}

			// Some image source is not valid. We can determine this by checking it's width, if the width is exists.
			if (isset($image->width) && !$image->width) {
				continue;
			}

			// If there's a ../ , we need to replace it.
			if (stristr($image->src, '/../') !== false) {
				$image->src = str_ireplace('/../', '/', $image->src);
			}

			if (stristr($image->src, 'http://') === false && stristr($image->src, 'https://') === false) {
				$image->src = $uri . '/' . $image->src;
			}

			// Convert those html entity &amp; from URL
			$image->src = html_entity_decode($image->src);

			$result[] = $image->src;
		}

		// Ensure that there are no duplicate images.
		$result = array_values(array_unique($result, SORT_STRING));

		return $result;
	}
}
