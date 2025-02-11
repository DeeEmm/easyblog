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

class EasyBlogEmotify
{
	/**
	 * Determines if emotify is enabled
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function enabled()
	{
		$config = EB::config();

		if ($config->get('emotify_enabled')) {
			return true;
		}

		return false;
	}

	/**
	 * Generates the emotify html codes
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html()
	{
		if (!$this->enabled()) {
			return;
		}
		
		$theme = EB::themes();
		$output = $theme->output('site/integrations/emotify/default');

		return $output;
	}
}
