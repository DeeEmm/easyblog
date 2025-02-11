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

class EasyBlogThemesHelperDialog
{
	/**
	 * Renders the close dialog button
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function cancelButton($title = 'COM_EASYBLOG_CANCEL_BUTTON', $type = 'default', $options = [])
	{
		return $this->closeButton($title, $type, $options);
	}

	/**
	 * Renders the close dialog button
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function closeButton($title = 'COM_EASYBLOG_CLOSE_BUTTON', $type = 'default', $options = [])
	{
		$attributes = EB::normalize($options, 'attributes', 'data-close-button');
		$class = EB::normalize($options, 'class', '');

		return EB::fd()->html('dialog.button', $title, $type, [
			'attributes' => $attributes,
			'class' => $class
		]);
	}

	/**
	 * Renders the close dialog button
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function submitButton($title, $type = 'primary', $options = [])
	{
		$attributes = EB::normalize($options, 'attributes', 'data-submit-button');
		$class = EB::normalize($options, 'class', '');

		return EB::fd()->html('dialog.button', $title, $type, [
			'attributes' => $attributes,
			'class' => $class
		]);
	}
}
