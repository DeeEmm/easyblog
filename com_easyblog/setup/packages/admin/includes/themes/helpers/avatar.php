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

class EasyBlogThemesHelperAvatar
{
	/**
	 * Centralized method of retrieving the html output of the avatar
	 *
	 * @since	6.0.0
	 * @access	private
	 */
	private function render($url, $size, $anchorLink, $options = [])
	{
		$fd = EB::fd();
		$config = EB::config();

		return $fd->html('avatar.' . $size, $url, $anchorLink, array_merge($options, [
			'style' => $config->get('layout_avatar_style')
		]));
	}

	/**
	 * Renders the avatar of the user
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function user(EasyBlogTableProfile $user, $size = 'default', $anchorLink = true, $options = [])
	{
		$options['name'] = $user->getName();
		$options['permalink'] = $user->getProfileLink();

		$output = $this->render($user->getAvatar(), $size, $anchorLink, $options);

		return $output;
	}

	/**
	 * Renders the avatar of the category
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function category(EasyBlogTableCategory $category, $size = 'default', $anchorLink = true, $options = [])
	{
		$options['name'] = $category->getTitle();
		$options['permalink'] = $category->getPermalink();

		$output = $this->render($category->getAvatar(), $size, $anchorLink, $options);

		return $output;
	}

	/**
	 * Renders the avatar of the team
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function team($team, $size = 'default', $anchorLink = true, $options = [])
	{
		$options['name'] = $team->getTitle();
		$options['permalink'] = $team->getPermalink();

		$output = $this->render($team->getAvatar(), $size, $anchorLink, $options);

		return $output;
	}
}
