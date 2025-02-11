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

class EasyBlogInternalButtonFacebook extends EasyBlogInternalButtons
{
	public $url = 'https://www.facebook.com/sharer/sharer.php';

	/**
	 * Generates the external link
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getPermalink()
	{
		$link = $this->url . '?u=' . urlencode($this->permalink);

		return $link;
	}
}
