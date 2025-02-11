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

class EasyBlogAvatarKunena
{
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_kunena/kunena.php';

		if (!JFile::exists($file)) {
			return false;
		}

		return true;
	}

	public function getAvatar($profile, $fromOpengraph = false)
	{
		if (!$this->exists()) {
			return;
		}
		
		$id = $profile->id;

		if (!$id) {
			$id = 0;
		}
		$user = KunenaFactory::getUser($id);

		$avatar = $user->getAvatarURL('kavatar');

    	return $avatar;
	}
}