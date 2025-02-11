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

class EasyBlogThemesHelperSnackbar
{
	/**
	 * Renders heading snackbars
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function heading($title, $btn = [])
	{
		$title = JText::_($title);

		// Normalize the button
		$button = null;

		if ($btn) {
			$button = (object) [
				'link' => EB::normalize($btn, 'link', 'javascript:void(0)'),
				'text' => JText::_(EB::normalize($btn, 'text', '')),
				'icon' => EB::normalize($btn, 'icon', ''),
				'style' => EB::normalize($btn, 'style', 'btn btn-default'),
				'attribute' => EB::normalize($btn, 'attribute', '')
			];
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('button', $button);

		$html = $theme->output('site/helpers/snackbar/heading');

		return $html;
	}

	/**
	 * Renders the search for the snackbar
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function search($element, $value, $options = [])
	{
		$placeholder = EB::normalize($options, 'placeholder', JText::_('COM_EASYBLOG_TOOLBAR_PLACEHOLDER_SEARCH'));
		$buttonAttributes = EB::normalize($options, 'buttonAttributes', '');

		$theme = EB::themes();
		$theme->set('element', $element);
		$theme->set('value', $value);
		$theme->set('placeholder', $placeholder);
		$theme->set('buttonAttributes', $buttonAttributes);

		$html = $theme->output('site/helpers/snackbar/search');

		return $html;
	}

	/**
	 * Standard snackbar heading
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function standard($title, $actions = [])
	{
		$title = JText::_($title);

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('actions', $actions);

		$html = $theme->output('site/helpers/snackbar/standard');

		return $html;
	}
}
