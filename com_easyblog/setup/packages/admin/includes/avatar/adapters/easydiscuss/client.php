<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAvatarEasydiscuss
{
	public function getAvatar($profile, $fromOpengraph = false)
	{
		$easydiscuss = EB::easydiscuss();
		
		if (!$easydiscuss->exists()) {
			return false;
		}

		$table = ED::table("Profile");
		$user = $table->load($profile->id);

		// Fix compatability with ED 4 and below. #2926
		if ($table->id) {
			$user = $table;
		}

		$avatar = $user->getAvatar();

		return $avatar;
	}
}
