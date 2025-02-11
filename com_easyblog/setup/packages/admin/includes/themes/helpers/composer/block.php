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

class EasyBlogThemesHelperComposerBlock
{
	/**
	 * Renders the placeholder details for some of the blocks
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function placeholder($icon, $title, $desc = '')
	{
		if ($desc === '') {
			$desc = $title . '_DESC';
		}

		$theme = EB::themes();
		$theme->set('icon', $icon);
		$theme->set('title', $title);
		$theme->set('desc', $desc);

		$output = $theme->output('site/helpers/composer/block/placeholder');

		return $output;
	}
}
