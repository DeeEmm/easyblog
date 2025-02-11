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

class EasyBlogAvatarDefault
{
	/**
	 * Retrieves the avatar url
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getAvatar($profile, $fromOpengraph = false)
	{
		static $cache = [];
		static $default = 'media/com_easyblog/images/avatars/author.png';

		if (!isset($cache[$profile->id])) {
			$link = '';

			$avatarRelativePath = EB::image()->getAvatarRelativePath();

			$path = JPATH_ROOT . '/' . $avatarRelativePath . '/' . $profile->avatar;
			$image = $avatarRelativePath . '/' . $profile->avatar;

			if (JFile::exists($path)) {
				$link = $image;
			}

			// Check for default overrides (legacy)
			$siteTemplate = FH::getCurrentTemplate();

			if (!$link) {
				$overrides = JPATH_ROOT . '/templates/' . $siteTemplate . '/html/com_easyblog/assets/images/default_blogger.png';
				$exists = JFile::exists($overrides);

				if ($exists) {
					$link = 'templates/' . $siteTemplate . '/html/com_easyblog/assets/images/default_blogger.png';
				}
			}

			// Check for new overrides (since 6.0.x)
			if (!$link) {
				$override = FH::getTemplateOverrideFolder('easyblog') . '/images/avatars/author.png';

				if (JFile::exists($override)) {
					$link = FH::getTemplateOverrideFolder('easyblog', true) . '/images/avatars/author.png';
				}
			}

			// If all else fails, we just fall back to the default avatar
			if (!$link) {
				$link = $default;
			}

			$cache[$profile->id] = rtrim(JURI::root(), '/') . '/' . $link;
		}

		return $cache[$profile->id];
	}
}
