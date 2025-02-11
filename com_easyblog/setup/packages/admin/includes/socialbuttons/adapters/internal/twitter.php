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

class EasyBlogInternalButtonTwitter extends EasyBlogInternalButtons
{
	public $url = 'https://twitter.com/intent/tweet';

	/**
	 * Generates the external link
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getPermalink()
	{
		$config = EB::config();
		$doc = JFactory::getDocument();

		$via = $config->get('main_twitter_button_via_screen_name', '');

		if ($via && $doc->getType() == 'html') {
			$via = EBString::substr($via, 1);

			$doc->addHeadLink('https://twitter.com/' . $via, 'me');
		}

		// retrieve post title
		$title = $this->getPostTitle();

		$link = $this->url . '?url=' . urlencode($this->permalink) . '&amp;text=' . $title;

		if ($via) {
			$link .= '&via=' . $via;
		}
		
		return $link;
	}
}
