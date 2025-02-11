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

class EasyBlogAvatarCommunityBuilder
{
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);
		cbimport('cb.database');
		cbimport('cb.tables');
		cbimport('cb.tabs');

		return true;
	}

	public function getAvatar($profile, $fromOpengraph = false)
	{
		if (!$this->exists()) {
			return false;
		}

		$user = CBuser::getInstance($profile->id);

		// @task: Apply guest avatars when necessary.
		if (!$profile->id) {
			$avatar = selectTemplate() . 'images/avatar/tnnophoto_n.png';

			return $avatar;
		}

		if (!$user) {
			$user = CBuser::getInstance(null);
		}

		// Prevent CB from adding anything to the page.
		ob_start();
		$source	= $user->getField( 'avatar' , null , 'php' );
		$reset = ob_get_contents();
		ob_end_clean();
		unset( $reset );

		$source = $source['avatar'];

		//incase we view from backend. we need to remove the /administrator/ from the path.
		$avatar = str_replace('/administrator/','/', $source);

		return $avatar;
	}
}