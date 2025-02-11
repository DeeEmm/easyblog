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

class EasyBlogAlbum
{
	/**
	 * Search and removes the gallery tag from the content.
	 *
	 * @access	public
	 * @param	string	$content	The content to search on.
	 *
	 */
	public function strip($content)
	{
		$pattern = '/\[embed=album\].*?\[\/embed\]/';

		return preg_replace($pattern, '' , $content);
	}

	/**
	 * Used in conjunction with EB::formatter()
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function stripCodes(EasyBlogPost &$post)
	{
		if (isset($post->text)) {
			$post->text = $this->strip($post->text);
		}

		$post->intro = $this->strip($post->intro);
		$post->content = $this->strip($post->content);
	}
}
