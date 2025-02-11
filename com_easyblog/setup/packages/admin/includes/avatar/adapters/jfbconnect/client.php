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

class EasyBlogAvatarJFBConnect
{
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_jfbconnect/jfbconnect.php';

		if(! JFile::exists($file)) {
			return false;
		}

		return true;
	}

	private function getId($userId)
	{
		$db = EB::db();

		// Get columns
		$columns = $db->getTableColumns('#__jfbconnect_user_map');

		// Set the default column
		$query 	= 'SELECT ' . $db->quoteName('fb_user_id') . ' AS ' . $db->quoteName('id');

		// If it is new version
		if (in_array('provider_user_id', $columns)) {

			$query = 'SELECT ' . $db->quoteName('provider_user_id') . ' AS ' . $db->quoteName('id');
		}

		$query .= ' FROM ' . $db->quoteName('#__jfbconnect_user_map');
		$query .= ' WHERE ' . $db->quoteName('j_user_id') . '=' . $db->Quote($userId);

		$db->setQuery($query);

		$id = $db->loadResult();

		if (!$id) {
			return false;
		}

		return $id;
	}

	public function getAvatar($profile, $fromOpengraph = false)
	{
		if (!$this->exists()) {
			return false;
		}

		// Get facebook's id
		$id = $this->getId($profile->id);

		if (!$id) {
			return false;
		}

		$avatar = 'https://graph.facebook.com/' . $id . '/picture?type=small';

		return $avatar;
	}
}