<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAvatarK2
{
	public function exists()
	{
		$file1 = JPATH_ROOT . '/components/com_k2/helpers/route.php';
		$file2 = JPATH_ROOT . '/components/com_k2/helpers/utilities.php';

		if (!JFile::exists($file1) || !JFile::exists($file2)) {
			return false;
		}

		require_once($file1);
		require_once($file2);

		return true;
	}

	public function getAvatar($profile, $fromOpengraph = false)
	{
		if (!$this->exists()) {
			return false;
		}

		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->quoteName('#__k2_users') . ' '
				. 'WHERE ' . $db->nameQuote('userID') . '=' . $db->Quote($profile->id);

		$db->setQuery($query);
		$result	= $db->loadObject();

		if (!$result || !$result->image) {
			return false;
		}

		$avatar = JURI::root() . 'media/k2/users/' . $result->image;

		return $avatar;
	}
}