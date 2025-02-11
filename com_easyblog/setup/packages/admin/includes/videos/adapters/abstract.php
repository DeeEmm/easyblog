<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

abstract class EasyBlogVideoProvider extends EasyBlog
{
	/**
	 * Given a regex pattern, try to match for items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function match($pattern, $url)
	{
		// Some content plugins tries to replace & with &amp; in the content. We need to ensure that the URL doesn't contain &amp;
		$url = str_ireplace('&amp;', '&', $url);

		preg_match($pattern, $url, $matches);

		if (!empty($matches)) {
			$code = explode('&', $matches[1]);

			if (count($code) > 1) {
				return $code[0];
			}

			return $matches[1];
		}

		return false;
	}
}