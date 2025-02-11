<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogCommentDisqus extends EasyBlog
{
	public function html(EasyBlogPost &$blog)
	{
		// Get the disqus short code
		$code = $this->config->get('comment_disqus_code');

		if (!$code) {
			return;
		}

		$template = EB::themes();
		$template->set('code', $code);

		$output = $template->output('site/comments/disqus');

		return $output;
	}

	/**
	 * Retrieves the comment count for disqus
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCount(EasyBlogPost $post)
	{
		static $loaded = false;

		if (!$loaded) {
			$theme = EB::themes();
			$headers = $theme->output('site/comments/disqus.count.js');

			$this->doc->addScriptDeclaration($headers);

			$loaded = true;
		}

		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/comments/disqus.count');

		return $output;
	}
}
