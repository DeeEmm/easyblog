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

class EasyBlogThemesHelperComposerPanel
{
	/**
	 * Renders a section header in a panel
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function header($title, $info = false, $options = [])
	{
		$counter = EB::normalize($options, 'counter', '');
		$counterText = EB::normalize($options, 'counterText', '');
		$counterAttr = EB::normalize($options, 'counterAttr', '');

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('info', $info);
		$theme->set('counter', $counter);
		$theme->set('counterText', $counterText);
		$theme->set('counterAttr', $counterAttr);

		$output = $theme->output('site/helpers/composer/panel/header');

		return $output;
	}

	/**
	 * Renders the help section of a panel
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function help($text)
	{
		$theme = EB::themes();
		$theme->set('text', $text);

		$output = $theme->output('site/helpers/composer/panel/help');

		return $output;
	}
}
