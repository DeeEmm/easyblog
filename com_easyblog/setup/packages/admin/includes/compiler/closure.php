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

class EasyBlogCompilerClosure
{
	const URL = 'http://compiler.stackideas.com/';

	/**
	 * Minifies javascript contents
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function minify($contents)
	{
		$post = http_build_query([
			'code' => $contents
		]);

		$ch = curl_init(self::URL);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		$contents = curl_exec($ch);
		curl_close($ch);

		if (false === $contents) {
			return JError::raiseError(500, 'No HTTP response from compiler server');
		}

		return trim($contents);
	}
}
